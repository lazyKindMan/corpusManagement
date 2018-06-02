<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 10:12
 */

namespace app\models\corpus;


class TextFile
{
    private $_filePath;
    private $_file;
    public function __construct($path='')
    {
        $this->_filePath=$path;
        try{
            $this->_file=fopen($this->_filePath,"r");
        }catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    }
    public function getRow()
    {
        $row=fgets($this->_file);
        $row=trim($row,"\xEF\xBB\xBF");//出去boom头
        $row=trim($row);
        return $row;
    }
    public function isEnd()
    {
        if(feof($this->_file))
            return true;
        return false;
    }
    public function closeFile()
    {
        if($this->_file)
            fclose($this->_file);
    }
}