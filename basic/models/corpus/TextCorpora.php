<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 10:15
 */

namespace app\models\corpus;

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
    public function __construct($config=[])
    {
        foreach ($config as $key=>$value)
        {
            switch ($key)
            {
                case 'filePath':$this->filePath=$value;break;
                case 'time':$this->time=$value;break;
                case 'title':$this->title=$value;break;
                case 'resource':$this->resource=$value;break;
            }
        }
        $this->fileClass=new TextFile($this->filePath);
        $this->content='';
        $this->ridContent;
    }
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->fileClass->closeFile();
    }
    public function getConetnt()
    {
        while(!$this->fileClass->isEnd())
        {
            $row=$this->fileClass->getRow();
            $this->content.=$row;
            if($row=="")
            {
                $this->_ridSign();
                $this->_splitWord();
                break;
            }
        }
    }
    private function _ridSign()
    {
        $regx1="/\s+/";
        $this->ridContent=preg_replace($regx1,"",$this->content);
        $regx2="/\/[a-zA-Z]+/";
        $this->ridContent=preg_replace($regx2,"",$this->ridContent);
    }
    private function _splitWord()
    {
        $regx1="/\/[a-zA-Z]+/";
        $tempContent=trim(preg_replace($regx1,"",$this->content,1));
        $this->wordArr=preg_split("/\s+/",$tempContent);
        $this->word_count=count($this->wordArr);
    }
}