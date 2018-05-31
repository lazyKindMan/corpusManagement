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
}