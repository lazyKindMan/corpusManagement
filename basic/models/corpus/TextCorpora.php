<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 10:15
 */

namespace app\models\corpus;
use app\models\check\CheckService;
use app\models\TableConfig;
use yii;
use app\models\Userlevel;
//文本语料业务处理和数据存储
class TextCorpora
{
    public $content;
    public $word_count;//统计每个语料总共多少个词
    public $word_kind_count;//统计每个语料有多少种类词
    public $resource;
    public $title;
    public $time;
    public $filePath;
    public $fileClass;
    public $ridContent;
    public $wordArr;
    public $corpus_id;
    private $_currentPos;
    private $_tableName;
    private $_kindArr;
    private $_corpusName;
    public function __construct($config=[])
    {
        $this->content="";
        $this->_tableName=TableConfig::TextCorporaTable;
        foreach ($config as $key=>$value)
        {
            switch ($key)
            {
                case 'filePath':$this->filePath=$value;break;
                case 'time':$this->time=$value;break;
                case 'title':$this->title=$value;break;
                case 'resource':$this->resource=$value;break;
                case 'content':$this->content=$value;break;
                case 'tableName':$this->_tableName=$value;break;
                case 'corpus_name':$this->_corpusName=$value;break;
                case 'corpus_id':$this->corpus_id=$value;break;
            }
        }
        if($this->content==""&&$this->corpus_id==null)
        {
            $this->fileClass=new TextFile($this->filePath);
            $this->content='';
        }
        $this->ridContent='';
    }
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        if($this->fileClass!=null)
            $this->fileClass->closeFile();
    }

    /**
     * @throws \Exception
     */
    public function getConetntFile()
    {
        $lastId=-1;
        while(!$this->fileClass->isEnd())
        {
            $row=$this->fileClass->getRow();
            $this->content.=$row;
            if($row=="")
            {
                $this->_kindArr=[];
                $this->_ridSign();
                $this->_splitWord();
                $this->_currentPos=0;
                try{
                    $lastId=$this->_insertItem();
                }catch (\Exception $e)
                {
                    throw $e;
                }
                if($lastId>0)
                {
                    foreach ($this->wordArr as $word)
                    {
                        $wordClass=new TextCorporaWord($word,$this->ridContent,$this->_currentPos);
                        $this->_currentPos=$wordClass->getCurrentPos();
                        $wordClass->save($lastId);
                        unset($wordClass);
                    }
                    $this->saveStaticalReport($lastId);
                }
                $this->content='';
                $this->ridContent='';
                $this->_kindArr=null;
            }
        }
    }

    /**
     *使用填写表单形式添加语料
     * @throws \Exception
     */
    public function getContextByText()
    {
        $lastId=-1;
        $this->_kindArr=[];
        $this->wordArr=[];
        $this->_ridSign();
        $this->_splitWord();
        try{
            $lastId=$this->_insertItem();
        }catch (\Exception $e)
        {
            throw $e;
        }
        if($lastId>0)
        {
            foreach ($this->wordArr as $word)
            {
                $wordClass=new TextCorporaWord($word,$this->ridContent,$this->_currentPos);
                $this->_currentPos=$wordClass->getCurrentPos();
                $wordClass->save($lastId);
                unset($wordClass);
            }
            $this->saveStaticalReport($lastId);
            //分配审核任务
            $checkModel=new CheckService();
            try{
                $checkModel->distributeCheckers($lastId,CheckService::KINDTEXT,CheckService::OPADD);
                return true;
            }catch (\Exception $e)
            {
                throw $e;
            }
        }
    }
    /**
     * @throws \Exception
     */
    private function _insertItem()
    {
        try
        {
            $this->_validate();
        }
        catch (\Exception $e)
        {
            throw $e;
        }
        $db=yii::$app->db;
        $insertTransaction=$db->beginTransaction();
        try{
            $date=date('Y-m-d H:i:s');
            $db=yii::$app->db;
            $db->createCommand()->insert($this->_tableName,[
               'resource'=>$this->resource,
                'title'=>$this->title,
                'content'=>$this->ridContent,
                'open_level'=>Userlevel::getLevelId('普通用户'),
                'created_at'=>$date,
                'updated_at'=>$date,
                'is_checking'=>CheckService::STATUSCHECKING,
                'word_count'=>$this->word_count,
                'word_kind_count'=>$this->word_kind_count,
                'corpus_name'=>$this->_corpusName
            ])->execute();
            $insertTransaction->commit();
            return (int)$db->createCommand("select corpus_id from $this->_tableName where created_at='$date'")->queryScalar();
        }catch (\Exception $e)
        {
            $insertTransaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    private function _validate()
    {
        if($this->title===null||$this->resource===null)
            throw new \Exception("请输入标题或者名称");
        if(strlen($this->title)>60)
            throw new \Exception("标题超过规定长度");
        if(strlen($this->resource)>60)
            throw new \Exception("来源输入超过规定长度");
        if($this->_tableName===null)
            throw new \Exception("请设定表名");
        if($this->content!=''&&strlen(preg_replace("/\s+|\n|\r/",'',$this->content))==0)
            throw new \Exception("请输入有效非空语料");
    }

    private function _ridSign()
    {
        $regx1="/\s+/";
        $this->ridContent=preg_replace($regx1,"",$this->content);
        $regx2="/\/[a-zA-Z]+/";
        $this->ridContent=preg_replace($regx2,"",$this->ridContent);
    }

    /**
     *
     */
    private function _splitWord()
    {
        $regx1="/\/[a-zA-Z]+/";
        $rowStrs=preg_split("/\r|\n/",$this->content);
        foreach ($rowStrs as $rowStr)
        {
            $rowStr=trim(preg_replace($regx1,"",$rowStr,1));//除去前面多余的/m等
            $rowStr=trim($rowStr);
            if(strlen($rowStr)>0)
            {
                $this->wordArr=array_merge($this->wordArr,preg_split("/\s+/",$rowStr));
                foreach (preg_split("/\s+/",$rowStr) as $value)
                {
                    $values=explode("/",$value);
                    if(count($values)>1)
                    {
                        if(array_key_exists($values[0],$this->_kindArr))
                            $this->_kindArr[$values[0]]+=1;
                        else $this->_kindArr[$values[0]]=1;
                    }

                }
        }
        }
        $this->word_count=count($this->wordArr);
        $this->word_kind_count=count($this->_kindArr);
    }

    /**
     * @throws yii\base\InvalidConfigException
     * @throws yii\db\Exception
     * @throws \Exception
     */
    private function saveStaticalReport($corpus_id)
    {
        $report='';
        foreach ($this->_kindArr as $key=>$value)
        {
            $report.=$key.":".$value;
            $report.=" ";
        }
        $db=yii::$app->getDb();
        $insertTransaction=$db->beginTransaction();
        try{
            $db->createCommand()->insert("tb_statistical_report",[
                'corpus_id'=>$corpus_id,
                'report_data'=>$report
            ])->execute();
            $insertTransaction->commit();
        }catch (\Exception $e)
        {
            $insertTransaction->rollBack();
            throw $e;
        }
    }
    public function getKindArr()
    {
        var_dump($this->_kindArr);
    }

    /**
     * 删除文本语料
     * @throws \Exception
     */
    public function deleteById()
    {

        $wordTable=TableConfig::TextWordTable;
        $reportTable=TableConfig::TextStatisticalTable;
        $db=yii::$app->db;
        if($this->corpus_id==null)
            throw new \Exception("no corpus_id in TextCorpora Class");
        $deleteTransaction=$db->beginTransaction();
        try{  //先删词表和报告表
            $db->createCommand()->delete($wordTable,['corpus_id'=>$this->corpus_id])->execute();
            $db->createCommand()->delete($reportTable,['corpus_id'=>$this->corpus_id])->execute();
            $db->createCommand()->delete($this->_tableName,['corpus_id'=>$this->corpus_id])->execute();
            $deleteTransaction->commit();
        }catch (\Exception $e)
        {
            $deleteTransaction->rollBack();
            throw $e;
        }
    }

    /**
     * 管理员提出删除审核请求
     * @throws \Exception
     */
    public function addDeleteCheck()
    {
        if($this->corpus_id==null)
            throw new \Exception("no corpus_id in TextCorpora Class");
        $model=new CheckService();
        try{
            yii::$app->db->createCommand()->update($this->_tableName,['is_checking'=>1],['corpus_id'=>$this->corpus_id])->execute();
            $model->submitDeleteOp(CheckService::KINDTEXT,$this->corpus_id);
        }catch (\Exception $e)
        {
            throw $e;
        }
    }
}