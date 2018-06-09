<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/4
 * Time: 14:21
 */

namespace app\models\search;
use app\models\TableConfig;
use yii;

class searchModel
{
    public $conditionStr;
    public $keyWordList;
    public $unKeyList;
    public $resArr;
    public $searchResult;
    private $_tableName;
    private $_matchs;
    private $_condition;
    private $_offSet;
    private $_pageSize;
    private $_wordArr;
    private $keyWordArr;
    public function __construct($config=[])
    {
        foreach ($config as $key=>$value)
        {
            switch ($key){
                case 'condition':$this->conditionStr=$value;break;
                case 'offSet':$this->_offSet=$value;break;
                case 'pageSize':$this->_pageSize=$value;break;
            }
        }
        $this->_tableName=TableConfig::TextCorporaTable;
        $this->_condition="";
        $this->_wordArr=[];
        $this->searchResult=[];
    }

    /**
     * @throws \Exception
     */
    private function _processCondition()
    {
        $regx1="/![0-9\p{Han}]+/";
        $regx=[];
        $this->conditionStr=preg_replace("/\s+/","",$this->conditionStr);
        $regx[]="/^[\x{4e00}-\x{9fa5}0-9a-zA-Z%!]+\|[\x{4e00}-\x{9fa5}0-9a-zA-Z%!]+$/u";
        $regx[]="/^[\x{4e00}-\x{9fa5}0-9a-zA-Z%!]+\&[\x{4e00}-\x{9fa5}0-9a-zA-Z%!]+$/u";
        $regx[]="/^![\x{4e00}-\x{9fa5}0-9a-zA-Z%]+$/u";
        $regx[]="/^[\x{4e00}-\x{9fa5}0-9a-zA-Z%]+$/u";
        $i=0;
            foreach ($regx as $value)
            {
                preg_match($value,$this->conditionStr,$this->_matchs);
            if(count($this->_matchs)>0)
                break;
                $i++;
            }
            if(count($this->_matchs)<=0)
            {
                throw new \Exception("请按照规则输入检索值，且检索值不能超过2个关键字");
            }
            $this->_getCondition($i);
    }

    /**
     *
     * @throws \Exception
     */
    public function getRes()
    {
        try{
            $this->_processCondition();
            $db=yii::$app->getDb();
            $offSet=$this->_offSet*$this->_pageSize;
            $this->resArr=$db->createCommand("select corpus_id,content,resource from $this->_tableName".$this->_condition)->queryAll();
        }catch (\Exception $e)
        {
            throw $e;
        }
        //根据查询结果处理字符串位置
        if(count($this->keyWordArr)==0)
        {
            foreach ($this->resArr as $value)
            {
                    $this->searchResult[]=['content'=>substr($value['content'],0,30),'resource'=>$value['resource'],'corpus_id'=>$value['corpus_id']];
            }
        }
        else
        {//如果输入的非排除关键词，查找字符串位置并截取前后20个词作为返回结果

            foreach ($this->keyWordArr as $keyWord)
            {
                foreach ($this->resArr as $value)
                {
                    $pos=strpos($value['content'],$keyWord);
                    while ($pos!=false)
                    {
                        if($pos-15>=0) $start=$pos-15;
                        else $start=0;
                            $this->searchResult[]=['content'=>substr($value['content'],$start,strlen($keyWord)+60),'resource'=>$value['resource'],'corpus_id'=>$value['corpus_id']];
                        $pos=strpos($value['content'],$keyWord,$pos+strlen($keyWord));
                    }
                }
            }
        }
    }

    /**
     * @throws \Exception
     */
    private function _getCondition($i)
    {
        //先循环遍历
        $whereFlag=true;
        foreach ($this->_matchs as $match)
        {
            switch ($i)
            {
                case 0:
                    {
                        $match=explode("|", $match);
                        $whereFlag=true;
                        foreach ($match as $value)
                        {
                            if(strlen($value)<3)
                                throw new \Exception("关键词长度过小");
                            $value=str_replace("%","[%]",$value);
                            if($value[0]=="!")
                            {
                                $value=substr($value,1);
                                if($whereFlag)
                                {
                                    $this->_condition.=" where content not like '%$value%'";
                                    $whereFlag=false;
                                }
                                else $this->_condition.=" or content not like '%$value%'";
                            }
                            else
                            {
                                if($whereFlag)
                                {
                                    $this->_condition.=" where content  like '%$value%'";
                                    $whereFlag=false;
                                }
                                else $this->_condition.=" or content  like '%$value%'";
                                $this->keyWordArr[]=$value;
                            }
                        }
                        break;
                    }
                case 1:{
                    $match=explode("&", $match);
                    $whereFlag=true;
                    foreach ($match as $value)
                    {
                        if(strlen($value)<3)
                            throw new \Exception("关键词长度过小");
                        $value=str_replace("%","[%]",$value);
                        if($value[0]=="!")
                        {
                            $value=substr($value,1);
                            if($whereFlag)
                            {
                                $this->_condition.=" where content not like '%$value%'";
                                $whereFlag=false;
                            }
                            else $this->_condition.=" and content not like '%$value%'";
                        }
                        else
                        {
                            if($whereFlag)
                            {
                                $this->_condition.=" where content  like '%$value%'";
                                $whereFlag=false;
                            }
                            else $this->_condition.=" and content  like '%$value%'";
                            $this->keyWordArr[]=$value;
                        }
                    }
                    break;
                }
                default:{
                    $match=explode(" ",$match);
                    foreach ($match as $value)
                    {
                        if(strlen($value)<=4)
                            throw new \Exception("关键词长度过小");
                        $value=str_replace("%","[%]",$value);
                            if($value[0]=="!")
                            {
                                $value=substr($value,1);
                                $this->_condition.=" where content not like '%$value%'";
                            }
                            else
                            {
                                $this->_condition.=" where content like '%$value%'";
                                $this->keyWordArr[]=$value;
                            }
                    }
                    break;
                }
            }
        }
    }
    public function getContentById($corpus_id)
    {
        try {
            $res = yii::$app->db->createCommand("select content from $this->_tableName where corpus_id=$corpus_id")->queryOne();
            return $res['content'];
        } catch (yii\db\Exception $e) {
            /** @var TYPE_NAME $e */
            throw $e;
        }
    }
}