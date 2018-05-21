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

class createDictionarnForm
{
    public $levelNames;
    public $arrKeys;
    public $arrLevels;
    public $corpusName;
    public $corpusPre;

    /**
     * 验证输入是否符合规范
     * @return bool
     */
    private function validateForm()
    {
        if($this->levelNames==null||$this->arrKeys==null||$this->arrLevels==null||$this->corpusName==null||$this->corpusPre==null)
            return false;
        if(strlen($this->corpusName)>45)
            return false;
        if(strlen($this->corpusPre)>20||!preg_match("/^[a-zA-Z0-9]+$/"))
            return false;
        foreach ($this->levelNames as $value)
        {
            if(strlen($value)>40)
                return false;
        }
        foreach ($this->arrKeys as $value)
        {
            if(strlen($value)>30)
                return false;
        }
        return true;
    }
    public function createCorpus($checkDate=true)
    {
        if($checkDate)
            if($this->validateForm())
                return false;
        $db=Yii::$app->db;
        //插入表事务
        $insertCorpusTransaction=$db->beginTransaction();
        try{
            $level_id=Userlevel::getLevelId('普通用户');
            $insertArr=['corpus_name'=>$this->corpusName,
                'date_filename'=>yii::$app->session->get('DFile_path'),
                'all_level-count'=>yii::$app->session->get('all_level_count'),
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'is_checking'=>1,
                'open_level'=>$level_id,
                'pre_table'=>$this->corpusPre
                ];
            $db->createCommand()->insert("{{corpora_dictionary}}",$insertArr);
            $insertCorpusTransaction->commit();
        }catch (\Exception $e)
        {
            $insertCorpusTransaction->rollBack();
            throw $e;
        }
        catch (\Throwable $e)
        {
            $insertCorpusTransaction->rollBack();
            throw $e;
        }
        return true;
    }
}