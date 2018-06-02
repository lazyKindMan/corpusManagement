<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 11:28
 */

namespace app\models\corpus;
use yii;
//文本语料词语处理和存储
class TextCorporaWord
{
    public $rawWord;//原始（带标记)词语内容
    public $wordContext;
    public $sign;
    public $startPos;
    public $endPos;
    public $textContext;
    public $currentStrPos;
    public function __construct($rawWord,$text,$currentPos)
    {
        $this->rawWord=$rawWord;
        $this->textContext=$text;
        $this->currentStrPos=$currentPos;
        $this->_getWord();
    }

    /**
     * @param $corpus_id
     * @throws yii\base\InvalidConfigException
     * @throws yii\db\Exception
     */
    public function save($corpus_id)
    {
        $db=yii::$app->getDb();
        $insertTransaction=$db->beginTransaction();
        try{
                $db->createCommand()->insert("tb_wordlist",[
                    'corpus_id'=>$corpus_id,
                    'start_pos'=>$this->startPos,
                    'end_pos'=>$this->endPos,
                    'word_content'=>$this->wordContext,
                    'anntion_context'=>$this->sign
                ])->execute();
            $insertTransaction->commit();
        }catch (\Exception $e)
        {
            $insertTransaction->rollBack();
            echo $e->getMessage();
        }

    }
    private function _getWord()
    {
        $datas=explode("/",$this->rawWord);
        $this->wordContext=$datas[0];
        $this->sign=$datas[1];
        $this->startPos=strpos($this->textContext,$this->wordContext,$this->currentStrPos);
        $this->endPos=$this->startPos+strlen($this->wordContext);
        $this->currentStrPos=$this->endPos;
    }
    public function getCurrentPos()
    {
        return $this->currentStrPos;
    }
}