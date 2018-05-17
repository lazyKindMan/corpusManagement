<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/14
 * Time: 21:33
 */
function judge($i,$m,$A,$Anum)
{
    if($m==0)
        return true;
    if($i>=$Anum)
        return false;
    return judge($i+1,$m,$A,$Anum) or judge($i+1,$m-$A[$i],$A,$Anum);
}

$handle=fopen("php://stdin", "r");
$Anum=fgets($handle);
$Anum=str_replace("/n","",$Anum);
$S=explode(" ",str_replace("/n"," ",fgets($handle)));
$A=array();
for($i=0;$i<count($S);$i++)
    array_push($A,(int)$S[$i]);
$Bnum=fgets($handle);
$Bnum=str_replace("/n","",$Anum);
$S=explode(" ",str_replace("/n"," ",fgets($handle)));
for($i=0;$i<count($S);$i++)
{
    if(judge(0,(int)$S[$i],$A,$Anum))
        echo sprintf("yes\n");
    else echo sprintf("no\n");
}
?>