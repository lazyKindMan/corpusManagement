<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/2
 * Time: 10:29
 */

namespace app\models\corpus;

//获取文本语料报表信息
use app\models\TableConfig;
use yii;
class TextCorpusReport
{
    private $_textCorpusClass;
    public $corpus_id;
    public $dataStr;
    public $dataArr;
    private $_thisTableName;

    /**
     * TextCorpusReport constructor.
     * @param $corpus_id
     * @throws yii\db\Exception
     */
    public function __construct($corpus_id)
    {
        if($corpus_id==null)
            return null;
        $this->corpus_id=$corpus_id;
        $this->_thisTableName=TableConfig::TextStatisticalTable;
        $this->dataArr=[];
        $this->_textCorpusClass=new TextCorporaSearch();
    }

    /**
     * @throws \Exception
     */
    public function getReport()
    {
        $db=yii::$app->getDb();
        $sql="select report_data from $this->_thisTableName where corpus_id='$this->corpus_id'";
        try{
            $this->dataStr=$db->createCommand($sql)->queryScalar();
            $tempArr=explode(" ",trim($this->dataStr));
            foreach ($tempArr as $key=>$value)
            {
                $tempDatas=explode(":",$value);
                if(count($tempDatas)==2)
                    $this->dataArr[$tempDatas[0]]=$tempDatas[1];
            }
         arsort($this->dataArr);
        }catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * @throws \Exception
     */
    public function getCorpusData()
    {
        try {
            if (!empty($this->_textCorpusClass)) {
                return $this->_textCorpusClass->getTextCorpusById($this->corpus_id);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}