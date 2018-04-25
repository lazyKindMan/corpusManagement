<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/4/23
 * Time: 14:28
 */
require_once "../mysqlConfig/mysqlConfig.php";
try{
    $PDO = new PDO("mysql:host={$address};dbname={$DBName['user']}",$user,"{$password}");
//sql语句
    $sql="select id,username,level_id from tb_user";
    if(isset($_POST['userLevel']))
       $sql=$sql."where level_id={$_POST['userLevel']}";
    if(isset($_POST['username'])&&strpos($sql,"where"))
        $sql.="and username like '%{$_POST['username']}%'";
    else if(isset($_POST['username']))
        $sql.="where username like '%{$_POST['username']}%'";
    $res=$PDO->query($sql);
    $result=array();
    if($res!=false)
    {
        foreach ($res->fetchAll(PDO::FETCH_CLASS) as $row)
        {
            array_push($result,$row);
        }
        echo json_encode($result);
    }
    else
        echo json_encode(0);
}
catch (PDOException $e)
{
    die ("Error!: " . $e->getMessage() . "<br/>");
}