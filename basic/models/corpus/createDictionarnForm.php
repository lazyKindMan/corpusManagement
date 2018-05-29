<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/21
 * Time: 13:25
 */

namespace app\models\corpus;
use app\models\Userlevel;
use Yii;
use yii\db\Exception;
use yii\db\Schema;

class createDictionarnForm
{
    public $levelNames;
    public $levelKey;
    public $corpusName;
    public $corpusPre;
    public $levelCount;
    public $testCount;
    public $row;
    /**
     * 验证输入是否符合规范
     * @return bool
     * @throws \Exception
     */
    private function validateForm()
    {
        if($this->levelNames===null||$this->levelKey===null||$this->corpusName===null||$this->corpusPre===null)
            throw new \Exception("传入的值存在空值-1");
        if(strlen($this->corpusName)>45)
            throw new \Exception("语料库名字过长-2");
        if(strlen($this->corpusPre)>20||!preg_match("/^[a-zA-Z0-9]+$/",$this->corpusPre))
            throw new \Exception("表前缀名非英文或数字-3");
        foreach ($this->levelNames as $value)
        {
            if(strlen($value)>40)
                throw new \Exception("等级名过长-4");
        }
        foreach ($this->levelKey as $row)
        {
            foreach ($row as $key=>$value)
            {
                if(strlen($key)>30)
                    throw new \Exception("文档键值存在过长-5");
                if(!preg_match("/^[a-zA-Z0-9]+$/",$key))
                    throw new \Exception("文件键值存在非英文-6");
                if(strlen($value)>30)
                    throw new \Exception("别名至多30个英文或10个中文长度-7");
            }
        }
    }

    /**
     * @param bool $checkDate
     * @return bool
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function createCorpus($checkDate=true)
    {
        if($checkDate)
            try{
                $this->validateForm();

            }catch(\Exception $e)
            {
               throw $e;
            }
        $db=Yii::$app->db;
        $idc=-1;
        //插入表事务
        $insertCorpusTransaction=$db->beginTransaction();
        try{
            $level_id=Userlevel::getLevelId('普通用户');
            $insertArr=['corpus_name'=>$this->corpusName,
                'data_filename'=>yii::$app->session->get('DFile_path'),
                'all_level_count'=>yii::$app->session->get('all_level_count'),
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'is_checking'=>1,
                'open_level'=>$level_id,
                'pre_tbname'=>$this->corpusPre
                ];
            $db->createCommand()->insert("{{%corpora_dictionary}}",$insertArr)->execute();
//            echo $this->corpusName;
            $idc=(int)$db->getLastInsertID();
            $this->_addRelation($db,$idc);
            $this->_insertData();
            $insertCorpusTransaction->commit();
        }
        catch (\Exception $e)
        {
            $insertCorpusTransaction->rollBack();
            var_dump($e->getMessage());
            return false;
        }
        catch (\Throwable $e)
        {
            $insertCorpusTransaction->rollBack();
            var_dump($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 将每一层插入到关系表中
     * @param null $pre_idx
     * @param null $next_idx
     * @param $levelName
     * @param $idc
     * @param $currentLevel
     * @return int|string
     * @throws \yii\db\Exception
     */
    private function _insertRelation($levelName, $idc, $currentLevel,$pre_idx=null, $next_idx=null)
    {
        $tab_name=$this->corpusPre."_level".$currentLevel;
        $idx=-1;
            $query=yii::$app->db->createCommand()->insert("{{%level_relation}}",array(
                "idc"=>$idc,
                "levelname"=>$levelName,
                "next_idx"=>$next_idx,
                "pre_idx"=>$pre_idx,
                "tab_name"=>$tab_name
            ))->execute();
//            $query->execute();
//            var_dump($query->getRawSql());
            $idx=(int)yii::$app->db->getLastInsertID();

        return $idx;
    }

    /**
     * @param $idx
     * @param $next_idx
     * @return bool
     * @throws \yii\db\Exception
     */
    private function _updateRelation($idx, $next_idx)
    {
            $query=yii::$app->db->createCommand()->update("{{%level_relation}}",array('next_idx'=>$next_idx),"idx={$idx}")->execute();
    }

    /**
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    private function _insertData()
    {
        $sql="select R.idx,R.next_idx,R.pre_idx,R.tab_name from  {{%level_relation}} as R join {{%corpora_dictionary}} as D where R.idc=D.corpus_id and D.corpus_name='$this->corpusName'";
        $rows=yii::$app->db->createCommand($sql)->queryAll();
        if(count($rows)<=0)
            throw new \Exception("找不到对应的词典等级");
        //创建词典等级表
        $levelNum=0;
        $this->testCount=[];
        $this->row=0;
        //获取词典层数
        $sql="select all_level_count from {{%corpora_dictionary}} where corpus_name='$this->corpusName'";
        $res=yii::$app->db->createCommand($sql)->queryOne();
        $this->levelCount=(int)$res['all_level_count'];
        foreach ($this->levelKey as $row)
        {
            if($levelNum===0)
                $res=$this->_createTable($rows[$levelNum]['tab_name'],$row,$levelNum+2);
            else $res=$this->_createTable($rows[$levelNum]['tab_name'],$row,$levelNum+2,$rows[$levelNum-1]['tab_name']);
             if($res!=true)
                return $res;//返回错误结果
            $levelNum++;
        }
        //插入数据
        $this->_insertLevelData();
    }

    /**
     * 创建层级表
     * @param $tableName
     * @param $row
     * @param null $pre_tableName
     * @return bool|string
     */
    private function _createTable($tableName, $row,$levelNum,$pre_tableName=null)
    {
        $this->testCount=[];
        $columns=['id'=>Schema::TYPE_PK];
        foreach ($row as $key=>$value)
        {
            if($key%2==0) {
                $columns[$value] = Schema::TYPE_STRING;
            }
            else
                $columns[$row[$key-1]."_alias"]=Schema::TYPE_STRING."(30)";
        }
        $columns['pre_id']=Schema::TYPE_INTEGER;
        try {
            yii::$app->db->createCommand()->createTable($tableName, $columns)->execute();
            if($pre_tableName!=null)
                yii::$app->db->createCommand()->addForeignKey("preId",$tableName,['pre_id'],$pre_tableName,['id'])->execute();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return true;
    }
    //1.分级插入，2.存在值不插入

    /**
     * @throws Exception
     */
    private function _insertLevelData()
    {
        //获取文件路径
        $res=yii::$app->db->createCommand("select `data_filename` from {{%corpora_dictionary}} where `corpus_name`='$this->corpusName'")->queryOne();
        $path=$res['data_filename'];
        $handle=fopen($path,'r');
        $split_character=yii::$app->session->get("spilt_character");
        while (!feof($handle)) {
            $con = fgets($handle);
            $this->_processData($con,$split_character);
        }
        fclose($handle);
    }
    private function _addRelation($db,$idc)
    {
            //插入级别进入级别表
            $flag=1;
            $preIdx=0;
            if($idc>0)
            {
                foreach ($this->levelNames as $levelName)
                {
                    if($flag===1)//如果为最开始，设置pre_idx为空
                    {
                        $preIdx = $this->_insertRelation($levelName, $idc, $flag + 1);
                    }
                    else if($preIdx>0)
                    {
                        $tempIdx=$preIdx;//保存之前的preIdx
                        //插入并更新preIdx
                        $preIdx=$this->_insertRelation($levelName,$idc,$flag+1,$preIdx);
                        if($preIdx>0)
                        {
                            $this->_updateRelation($tempIdx,$preIdx);
                        }
                        else return false;
                    }
                    $flag++;
                }
            }
        return true;
    }

    /**
     * @param $row
     * @param $split_character
     * @throws Exception
     */
    private function _processData($row, $split_character)
    {
        $this->row++;
        $row=trim($row);
        if($this->row==1) {
            $row = trim($row, "\xEF\xBB\xBF");
        }
        $db=yii::$app->db;
        $sql="select R.tab_name from  {{%level_relation}} as R join {{%corpora_dictionary}} as D where R.idc=D.corpus_id and D.corpus_name='$this->corpusName'";
        $tableNames=$db->createCommand($sql)->queryAll();
        $arrDatas=preg_split("/ +/",$row);
        $insertDatas=[];
        foreach ($arrDatas as $value)
        {
            $res=preg_split("/$split_character/",$value);
            //判断是否异常，如果结果异常则终止插入该条
            if(count($res)!=2)
            {
                //添加入异常报告中
                $this->testCount[]=$this->row;
                return false;
            }
            if($res[1]!=''&&$res[1]!='0')
            {
                $insertDatas[$res[0]]=$res[1];
            }
        }
        //插入数据
        $levelNum=2;
        $pre_id=-1;
        foreach ($this->levelKey as $value)
        {
            $flag=false;
            $insertArr=[];
            foreach ($value as $key=>$val)
            {
                if($key%2==0)
                {
                    if(array_key_exists($val,$insertDatas))
                    {
                        $insertArr[$val]=$insertDatas[$val];
                        $flag=true;
                    }
                }
                else
                    if(array_key_exists($value[$key-1],$insertDatas))
                        $insertArr[$value[$key-1]."_alias"]=$val;
            }
            if($levelNum!=2&&$pre_id!=-1)
            {
                $insertArr['pre_id']=$pre_id;
            }
            $tableName=$tableNames[$levelNum-2]['tab_name'];
            //如果存在数据且
            if(count($insertArr)>0&&!$this->_checkExits($insertArr,$tableName))
            {
                //如果数据存在
                if($flag)
                {
                    $db->createCommand()->insert($tableNames[$levelNum-2]['tab_name'],$insertArr)->execute();
                    $pre_id=yii::$app->db->getLastInsertID();
                }
            }
            else
            {
                //获取pre_id
                //查出自己
                if(array_key_exists($this->levelKey[$levelNum-2][0],$insertDatas))
                    $pre_id=$this->_getPreId($db,$tableNames[$levelNum-2]['tab_name'],$this->levelKey[$levelNum-2][0],$insertDatas[$this->levelKey[$levelNum-2][0]]);
            }
            $levelNum++;

        }
    }

    /**
     * @param $db
     * @param $tableName
     * @param $filed
     * @param $value
     * @return int
     */
    private function _getPreId($db, $tableName, $filed, $value)
        {
            $res=$db->createCommand("select id from $tableName where $filed=\"$value\"")->queryOne();
            if($res!=false)
                return (int)$res['id'];
            else return -1;
        }
    /**
     * 检测值是否在表中存在
     * @param $insertArr
     * @param $tableName
     * @return bool
     * @throws Exception
     */
    private function _checkExits($insertArr, $tableName)
    {
        $ind=0;
        foreach ($insertArr as $key=>$value)
        {
            if(yii::$app->db->createCommand("select id from $tableName where $key=\"$value\"")->queryOne()!=false)
            {
                return true;
            }
            break;
        }
        return false;
    }
    //测试函数
    public function testFun()
    {
//        try{
//            $this->_insertData();
//        }catch (\Exception $e)
//        {
//            echo $e->getMessage();
//        }
        ini_set('memory_limit', '2048M');
        try {
//            $res=yii::$app->db->createCommand("select corpus_id from {{%corpora_dictionary}} where `corpus_name`='词典'")->queryOne();
//            var_dump($res);
            $this->_insertData();
        }catch (\Exception $e) {
            echo $e->getMessage()." ".$e->getLine()." in ".$e->getFile();
        }
    }
}