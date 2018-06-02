<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 18:48
 */

namespace app\models\corpus;

//检索文本语料模型
use app\models\TableConfig;
use yii;
class TextCorporaSearch
{
    public $offSet;
    public $pageSize;
    public $condition;
    public $totalCount;
    private $_db;
    private $_thisTableName;

    /**
     * TextCorporaSearch constructor.
     * @param array $config
     * @throws yii\db\Exception
     */
    public function __construct($config=['offSet'=>0,'pageSize'=>8,'condition'=>[]])
    {
        foreach ($config as $key=>$value)
        {
            switch ($key)
            {
                case 'offSet':$this->offSet=$value;break;
                case 'pageSize':$this->pageSize=$value;break;
                case 'condition':$this->condition=$value;break;
                case 'columns':$this->columns=$value;break;
            }
        }
        $this->_db=yii::$app->db;
        $this->_thisTableName=TableConfig::TextCorporaTable;
        $this->_getCount();
    }

    /**
     * 查询文本语料信息
     * @throws \Exception
     */
    public function getTextCorpora()
    {
        $levelTable=TableConfig::UserLevelTable;
        $sql="select A.*,B.level_name from $this->_thisTableName as A,$levelTable as B".$this->_createConditionSql(true)." limit $this->offSet,$this->pageSize";
        try{
            $resArr=$this->_db->createCommand($sql)->queryAll();
            return $resArr;
        }catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * @param $corpus_id
     * @return TYPE_NAME|array|false
     * @throws \Exception
     *
     */
    public function getTextCorpusById($corpus_id)
    {
        $levelTable=TableConfig::UserLevelTable;
        $sql="select A.*,B.level_id,B.level_name from $this->_thisTableName as A,$levelTable as B where corpus_id=$corpus_id and A.open_level=B.level_id";
        try{
            $res=$this->_db->createCommand($sql)->queryOne();
            /** @var TYPE_NAME $res */
            if (!empty($res)) {
                return $res;
            }
        }catch (\Exception $e)
        {
            throw $e;
        }
    }
    /**
     * @throws yii\db\Exception
     */
    private function _getCount()
    {
        $sql="select count(*) from $this->_thisTableName".$this->_createConditionSql();
        $this->totalCount=(int)$this->_db->createCommand($sql)->queryScalar();
    }
    //根据配置条件创建条件sql
    private function _createConditionSql($search=false)
    {
        $flag=1;
        $conditionStr="";
        if($search)
        {
            $conditionStr.=" where A.open_level=B.level_id";
        }
        foreach ($this->condition as $key=>$value)
        {
            if($flag===1&&!$search)
                $conditionStr.=" where $key='$value'";
            else $conditionStr.=" and $key='$value'";
            $flag++;
        }
        return $conditionStr;
    }
}