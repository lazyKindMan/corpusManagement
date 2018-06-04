<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/4
 * Time: 13:26
 */

namespace app\models\corpus;
use app\models\check\CheckService;
use app\models\TableConfig;
use yii;

class DictionaryCorpus
{
    public $corpus_id;
    private $_tableName;
    private $_relationTableName;
    public function __construct($config=[])
    {
        foreach ($config as $key=>$value)
        {
            switch ($key){
                case 'corpus_id':$this->corpus_id=$value;break;
            }
        }
        $this->_tableName=TableConfig::DictionaryTable;
        $this->_relationTableName=TableConfig::DictionaryLevelRelationTable;
    }

    /**
     * 提交删除词典语料操作
     * @throws \Exception
     */
    public function submitDelete()
    {
        if($this->corpus_id==null)
            throw new \Exception("must have corpus_id in DictionaryCorpus");
        $db=yii::$app->getDb();
        $updateTransaction=$db->beginTransaction();
        try{
            $db->createCommand()->update($this->_tableName,['is_checking'=>1],['corpus_id'=>$this->corpus_id])->execute();
            $updateTransaction->commit();
        }catch (\Exception $e)
        {
            $updateTransaction->rollBack();
            throw $e;
        }
        $model=new CheckService();
        try {
            $model->submitDeleteOp(CheckService::KINDDICTIONARY, $this->corpus_id);
        }catch (\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * @throws yii\db\Exception
     * @throws \Exception
     */
    public function deleteById()
    {
        if($this->corpus_id==null)
            throw new \Exception("must have corpus_id in DictionaryCorpus");
        $db=yii::$app->getDb();
        $deleteTransaction=$db->beginTransaction();
        try
        {
            //查询所有表名并删除
            $tableNames=$db->createCommand("select tab_name from $this->_relationTableName where idc=:corpus_id",['corpus_id'=>$this->corpus_id])->queryAll();
            for($i=count($tableNames)-1;$i>=0;$i--)
            {
                $tableName=$tableNames[$i]['tab_name'];
               $db->createCommand("drop table if EXISTS $tableName")->execute();
            }
            //删除关系
            $db->createCommand()->delete($this->_relationTableName,['idc'=>$this->corpus_id])->execute();
            //删除库
            $db->createCommand()->delete($this->_tableName,['corpus_id'=>$this->corpus_id])->execute();
            $deleteTransaction->commit();
        }catch (\Exception $e)
        {
            $deleteTransaction->rollBack();
            throw $e;
        }
    }
}