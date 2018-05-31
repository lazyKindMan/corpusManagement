<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/29
 * Time: 14:24
 */

namespace app\models\check;

/*
 * 对应语料库审核表
 * @property int $corpus_id
 * @property int $kind
 * @property int $user_id
 * @property int $status
 * @property int $op
 */
use app\models\corpus\CorporaDictionary;
use app\models\MyUser;
use yii\db\ActiveRecord;

class CorporaCheckModel extends ActiveRecord
{
    const OPDELETE=1;
    const OPADD=0;
    const STATUSCHECKING=1;
    const STATUSCHECKED=0;
    const KINDDICTIONARY=1;
    const KINDTEXT=0;
    protected $_transaction = null;
    public $corpus_id;
    public $corpus_name;
    public $created_at;
    public $updated_at;
    public static function tableName()
    {
        return "{{%corpora_check}}";
    }
    public function getMyUser()
    {
        return $this->hasMany(MyUser::className(),['id'=>'user_id']);
    }
    public function  getCorporaDictionary()
    {
        return $this->hasOne(CorporaDictionary::className(),['corpus_id'=>'corpus_id']);
    }
    /**
     * 如果主键id存在返回主键id，否则返回 true/false
     * @param array $insertDatas
     * @return bool
     * @throws \Throwable
     */
    public function insertCheck($insertDatas=[])
    {
        if(count($insertDatas)==0)
            return false;
        foreach ($insertDatas as $key=>$val)
        {
            $this->$key=$val;
        }
        $result=$this->insert();
        $insertId=$this->primaryKey;
        if($result==true&&!empty($insertId))
        {
            return $insertId;
        }
        return $result;
    }

    /**
     * 批量插入
     * @param array $datas
     * @throws \yii\db\Exception
     */
    public function insertAllChecks($datas=array())
    {
        $columns=array();
        $rows=array();
        foreach ($datas as $key=>$val)
        {
            $rows[$key]=array_values($val);
            if(empty($columns))
            {
                $columns=array_keys($val);
            }
        }
        $basic=self::getDb()->createCommand();
        $result=$basic->batchInsert("{{%corpora_check}}",$columns,$rows)->execute();
        return $result;
    }
    //更新方法
    public function updateLogic($data,$where){
        $result = $this->updateAll($data,$where);
        //var_dump($this->find()->createCommand()->getRawSql());
        return $result;
    }

    /**
     * update更新 是否带校验
     * @param $data
     * @param $where
     * @param bool $runValidation
     * @return false|int
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function updateLoginc2($data, $where, $runValidation=false){
        $model = $this->findOne($where);
        foreach($data as $key=>$val){
            $model->$key = $val;
        }
        $result = $model->update($runValidation);
        return $result;
    }

    /**
     * @param $where
     * @return int
     */
    public  function deleteLogic($where){
        $result = $this->deleteAll($where);
        return $result;
    }
    public  function begin(){
        $this->_transaction  = self::getDb()->beginTransaction();
    }
    /**
     *提交业务
     */
    public  function commit(){
        if(!empty($this->_transaction)){
            $this->_transaction->commit();
        }
    }

    /**
     *回滚事务
     */
    public  function rollback()
    {
        if (!empty($this->_transaction)) {
            $this->_transaction->rollBack();
        }

    }
}