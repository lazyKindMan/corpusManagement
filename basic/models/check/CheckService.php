<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/29
 * Time: 19:58
 */

namespace app\models\check;
/*
 * 语料库审核业务
 */

use app\models\corpus\CorporaDictionary;
use app\models\corpus\CorporaText;
use app\models\corpus\DictionaryCorpus;
use app\models\corpus\TextCorpora;
use app\models\UserAthu;
use Math\CalcHelper;
use yii;
class CheckService extends CorporaCheckModel
{
    private $_checkUserIDList;
    private $_distributeCheckers;
    private $_currentUser;
    private $_tableName;
    private $_dictionaryName;
    private $_textName;
    /**
     * CheckService constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->_checkUserIDList=array();
        $this->_distributeCheckers=array();
        $this->_tableName="tb_corpora_check";
        $this->_dictionaryName="tb_corpora_dictionary";
        $this->_textName="tb_corpora_text";
        //查询用户表查找拥有语料权限的用户
        try{
            $datas=UserAthu::getUserIDList("语料审核");
            foreach ($datas as $data)
            {
                $this->_checkUserIDList[]=$data['id'];
            }
        }catch (\Exception $e)
        {
            echo $e->getMessage()." in ".$e->getFile()." ".$e->getLine()." ".$e->getTraceAsString();
        }
    }

    /**
     * 为新增添的数据库分配专家
     * @param $coprus_id
     * @param $kind
     * @throws \Exception
     */
    public function distributeCheckers($coprus_id, $kind,$op)
    {
        if(count($this->_checkUserIDList)==0)
            throw new \Exception("系统中还没有审核专家",2000);
        else
        {
            if(count($this->_checkUserIDList)<3)
                $this->_getRandomCheckers(1);
            else
                $this->_getRandomCheckers(3);

        }
        $insertDatas=array();
        foreach ($this->_distributeCheckers as $checkers)
        {
            $insertDatas[]=array(
                'corpus_id'=>$coprus_id,
                'user_id'=>$checkers,
                'status'=>self::STATUSCHECKING,
                'op'=>$op,
                'kind'=>$kind
            );
        }
//        $this->begin();

            $this->insertAllChecks($insertDatas);
//            $this->commit();
    }
    private function _getRandomCheckers($generateNum)
    {
        for($i=0;$i<$generateNum;)
        {
            $randCode=rand(min($this->_checkUserIDList),max($this->_checkUserIDList));
            //如果在用户列表且不在分配列表中，那么加入进分配列表
            if(in_array($randCode,$this->_checkUserIDList)&&!in_array($randCode,$this->_distributeCheckers))
            {
                $this->_distributeCheckers[]=$randCode;
                $i++;
            }
        }
    }

    /**
     * 返回当前用户所管理的语料审核信息
     * @return array
     * @throws yii\db\Exception
     */
    public function getCheckMessage($config=['pageSize'=>5,'offSet'=>0])
    {
        $pageSize=$config['pageSize'];
        $offSet=$config['offSet'];
        $this->_currentUser=yii::$app->user->getId();
        $db=yii::$app->db;
        $dictionaryArrCount=(int)$this->getCount(CheckService::KINDDICTIONARY,CheckService::STATUSCHECKING);
        $textArrCount=(int)$this->getCount(CheckService::KINDTEXT,CheckService::STATUSCHECKED);
        $sql1="select A.*,B.corpus_name,B.created_at,B.updated_at from $this->_tableName as A,$this->_dictionaryName as B where  user_id=:user_id and status=:status and kind=:kind and A.corpus_id=B.corpus_id limit $offSet,$pageSize";
        //获取查询数组
         $dictionaryArr=$db->createCommand($sql1)->bindValues([':user_id'=>$this->_currentUser,
             ':status'=>CheckService::STATUSCHECKING,
             ":kind"=>CheckService::KINDDICTIONARY])->queryAll();
         $sql2= "select A.*,B.corpus_name,B.created_at,B.updated_at from $this->_tableName as A,$this->_textName as B where  user_id=:user_id and status=:status and kind=:kind and A.corpus_id=B.corpus_id limit $offSet,$pageSize";
         $textArr=$db->createCommand($sql2)->bindValues([':user_id'=>$this->_currentUser,
             ':status'=>CheckService::STATUSCHECKING,
             ":kind"=>CheckService::KINDTEXT])->queryAll();
         $datasArr= [
             "dictionaryCount"=>(int)$dictionaryArrCount,
             "dictionary"=>$dictionaryArr,
             "textCount"=>(int)$textArrCount,
             "text"=>$textArr,
             "code"=>1
         ];
         return $datasArr;
    }
    /**
     * @param int $kind
     * @param int $status
     * @return false|null|string
     * @throws yii\db\Exception
     */
    public function getCount($kind,$status)
    {
        $db=yii::$app->db;
        if($kind==self::KINDTEXT)
        {
            $otherTableName=$this->_textName;
        }
        else if($kind=self::KINDDICTIONARY)
        {
            $otherTableName=$this->_dictionaryName;
        }
        try {
            $count = $db->createCommand("select count(*) from $this->_tableName as A,$otherTableName as B where  user_id=:user_id and status=:status and kind=:kind and A.corpus_id=B.corpus_id")
                ->bindValues([':user_id' => $this->_currentUser,
                    ':status' => $status,
                    ":kind" =>$kind])->queryScalar();
            return $count;
        } catch (yii\db\Exception $e) {
            throw $e;
        }
    }

    /**
     *管理员提出用户请求删除操作
     * @param $kind
     * @param null $corpus_id
     * @throws \Exception
     */
    public function submitDeleteOp($kind,$corpus_id=null)
    {
        try
        {
            $this->distributeCheckers($corpus_id,$kind,self::OPDELETE);
        }catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * @param $corpus_id
     * @param $kind
     * @param $user_id
     * @throws \Exception
     */
    public function passCheck($corpus_id, $kind, $user_id)
    {
        $db=yii::$app->db;
        $updateTransaction=$db->beginTransaction();
        try{
            $db->createCommand()->update($this->_tableName,['status'=>0],['corpus_id'=>$corpus_id,'user_id'=>$user_id,'kind'=>$kind])->execute();
            $updateTransaction->commit();
        }catch (\Exception $e)
        {
            $updateTransaction->rollBack();
            throw  $e;
        }
        //判断审核进程
        $updateTransaction=$db->beginTransaction();
        $isPass=false;
        try{
            $count=(int)$db->createCommand("select count(*) from $this->_tableName where status='0' and corpus_id=:corpus_id and kind=:kind",['corpus_id'=>$corpus_id,'kind'=>$kind])->queryScalar();
            if($count>=2)//删除审核信息并修改语料库表审核
            {
                $isPass=true;
                if($kind==self::KINDTEXT)
                {
                    $db->createCommand()->update($this->_textName,['is_checking'=>0],['corpus_id'=>$corpus_id])->execute();
                }
                if($kind==self::KINDDICTIONARY)
                {
                    $db->createCommand()->update($this->_dictionaryName,['is_checking'=>0],['corpus_id'=>$corpus_id])->execute();
                }
            }
            $updateTransaction->commit();
        }catch (\Exception $e)
        {
            $updateTransaction->rollBack();
            throw  $e;
        }
        if($isPass)//查看是否为删除操作
        {
            $res=$db->createCommand("select op from $this->_tableName where corpus_id=:corpus_id and kind=:kind",['corpus_id'=>$corpus_id,'kind'=>$kind])->queryOne();
            $db->createCommand()->delete($this->_tableName,['corpus_id'=>$corpus_id,'kind'=>$kind])->execute();
            if($res)
            {
                $op=$res['op'];
                if($op==self::OPDELETE)
                {
                    if($kind==self::KINDTEXT)
                    {
                        $model=new TextCorpora(['corpus_id'=>$corpus_id]);
                        try{
                            $model->deleteById();
                        }catch (\Exception $e)
                        {
                            throw $e;
                        }
                    }
                    if($kind==self::KINDDICTIONARY)
                    {
                        $model=new DictionaryCorpus(['corpus_id'=>$corpus_id]);
                        try{
                            $model->deleteById();
                        }catch (\Exception $e)
                        {
                            throw $e;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $corpus_id
     * @param $kind
     * @param $user_id
     * @throws yii\db\Exception
     * @throws \Exception
     */
    public function unpassCheck($corpus_id, $kind, $user_id)
    {
        $db=yii::$app->db;
        $updateTransaction=$db->beginTransaction();
        try{
            //将审核状态改为2,表示未通过审核
            $db->createCommand()->update($this->_tableName,['status'=>2],['corpus_id'=>$corpus_id,'user_id'=>$user_id,'kind'=>$kind])->execute();
            $updateTransaction->commit();
        }catch (\Exception $e)
        {
            $updateTransaction->rollBack();
            throw  $e;
        }
        $updateTransaction=$db->beginTransaction();
        $upPass=false;//判断是否达到审核未通过状态标记
        try{
            $count=(int)$db->createCommand("select count(*) from $this->_tableName where status='2' and corpus_id=:corpus_id and kind=:kind",['corpus_id'=>$corpus_id,'kind'=>$kind])->queryScalar();
            if($count>=2)//删除审核信息并修改语料库表审核
            {
                $upPass=true;
                if($kind==self::KINDTEXT)
                {
                    $db->createCommand()->update($this->_textName,['is_checking'=>0],['corpus_id'=>$corpus_id])->execute();
                }
                if($kind==self::KINDDICTIONARY)
                {
                    $db->createCommand()->update($this->_dictionaryName,['is_checking'=>0],['corpus_id'=>$corpus_id])->execute();
                }
            }
            $updateTransaction->commit();
        }catch (\Exception $e)
        {
            $updateTransaction->rollBack();
            throw  $e;
        }
        if($upPass)
        {
            $res=$db->createCommand("select op from $this->_tableName where corpus_id=:corpus_id and kind=:kind",['corpus_id'=>$corpus_id,'kind'=>$kind])->queryOne();
            $db->createCommand()->delete($this->_tableName,['corpus_id'=>$corpus_id,'kind'=>$kind])->execute();
            //与通过审核相反，若为添加操作则进行删除信息
            if($res)
            {
                $op=$res['op'];
                if($op==self::OPADD)
                {
                    if($kind==self::KINDTEXT)
                    {
                        $model=new TextCorpora(['corpus_id'=>$corpus_id]);
                        try{
                            $model->deleteById();
                        }catch (\Exception $e)
                        {
                            throw $e;
                        }
                    }
                    if($kind==self::KINDDICTIONARY)
                    {
                        $model=new DictionaryCorpus(['corpus_id'=>$corpus_id]);
                        try{
                            $model->deleteById();
                        }catch (\Exception $e)
                        {
                            throw $e;
                        }
                    }
                }
            }
        }
    }
}