<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/4/22
 * Time: 20:09
 */
session_start();
require_once "../mysqlConfig/mysqlConfig.php";
if(!isset($_POST['username']) or  !isset($_POST['password']))
    die(json_encode(-1));//返回缺少函数代码

try{
    $PDO = new PDO("mysql:host={$address};dbname={$DBName['user']}",$user,"{$password}");
//sql语句
    $sql="select id,password,level_id from tb_user where username='{$_POST['username']}'";
    foreach ($PDO->query($sql) as $row)
    {
        if(strcmp($row['password'],md5($_POST['password']))==0)
        {
            die(json_encode(1));
            $_SESSION['username']=$_POST['username'];
            $_SESSION['level']=$row['level_id'];
            $_SESSION['userId']=$row['id'];
        }
        else die(json_encode(0));
    }
}
catch (PDOException $e)
{
    die ("Error!: " . $e->getMessage() . "<br/>");
}
