<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/1
 * Time: 10:55
 */
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
        fclose($this->_file);
    }
}
$fileClass=new TextFile("G:/wamp64/www/basic/crawler/199801.txt");
$file=fopen("G:/wamp64/www/basic/crawler/peopleNewsparper-199801.txt","w");
while(!$fileClass->isEnd()) {
    $row=$fileClass->getRow();
    $regx3="/[0-9]+\-[0-9]+\-[0-9]+\-[0-9]+/";
    $regx4="/\[/";
    $regx5="/\][a-z]+/";
    $row=preg_replace($regx3,"",$row);
    $row=preg_replace($regx4,"",$row);
    $row=preg_replace($regx5,"",$row);
    fwrite($file,$row."\n");
}
$fileClass->closeFile();
fclose($file);
