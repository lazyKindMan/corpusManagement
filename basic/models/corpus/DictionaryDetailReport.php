<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/4
 * Time: 10:21
 */

namespace app\models\corpus;

//查询并获得词典语料报表
use app\models\TableConfig;
use yii;
class DictionaryDetailReport
{
    private $_dictionaryTableName;
    private $_levelRelationTableName;
    private $_relationLink;
    public $report;
    public $corpus_id;

    public function __construct($config=[])
    {
        foreach ($config as $key=>$value)
        {
            switch ($key)
            {
                case 'corpus_id':$this->corpus_id=$value;break;
            }
        }
        $this->_dictionaryTableName=TableConfig::DictionaryTable;
        $this->_levelRelationTableName=TableConfig::DictionaryLevelRelationTable;
        $this->_relationLink=[];
    }

    /**
     * @throws \Exception
     */
    public function getReport()
    {
        if($this->corpus_id==null)
            throw new \Exception("no corpus_id are defined in class DictionaryDetailReport");
        $db=yii::$app->getDb();
        //获取词典语料基本信息
        $datas=$db->createCommand("select * from $this->_dictionaryTableName where corpus_id=:corpus_id",['corpus_id'=>$this->corpus_id])->queryOne();
        if($datas['all_level_count']==null)
            throw new \Exception("no corpus are selected by corpus_id=$this->corpus_id");
        $relations=$db->createCommand("select * from $this->_levelRelationTableName where idc=:corpus_id",['corpus_id'=>$this->corpus_id])->queryAll();
        if(count($relations)==0)
            throw new \Exception("no levelRelation are selected by corpus_id=$this->corpus_id");
        //获取等级关系
        for($i=1;$i<(int)$datas['all_level_count'];$i++)
        {
            foreach ($relations as $relation)
            {
                if($i==1&&$relation['pre_idx']==null)
                {
                    $this->_relationLink[$i-1]=$relation;
                    break;
                }
                else if($relation['pre_idx']==$this->_relationLink[$i-2]['idx'])
                {
                    $this->_relationLink[$i-1]=$relation;
                }
            }
        }
        $i=0;
        foreach ($this->_relationLink as $relation)
        {
            try {
                $this->_staticLevelCount($i);
                }catch (\Exception $e)
            {
                throw $e;
            }
            $i++;
        }
         $this->report['corpus']=$datas;
        $this->report['level']=$this->_relationLink;
        return $this->report;
    }
    private function _staticLevelCount($level)
    {
        //获取表名
        $db=yii::$app->getDb();
        $tableName=$this->_relationLink[$level]['tab_name'];
        try {
            $this->_relationLink[$level]['levelCount']=(int)$db->createCommand("select count(*) from $tableName")->queryScalar();
        } catch (yii\db\Exception $e) {
            /** @var TYPE_NAME $e */
            throw $e;
        }

    }
}