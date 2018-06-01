<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 11:28
 */

namespace app\models\corpus;

//文本语料词语处理和存储
class TextCorporaWord
{
    public $rawWord;//原始（带标记)词语内容
    public $wordContext;
    public $sign;
    public $startPos;
    public $endPos;
    public $textContext;
    public function __construct($rawWord,$text)
    {
        $this->rawWord=$rawWord;
        $this->textContext=$text;
    }
    public function save($corpus_id)
    {

    }
    private function _getWord()
    {
        $datas=str_split("/",$this->rawWord);
        $this->wordContext=$datas[0];
        $this->sign=datas[1];
    }
}