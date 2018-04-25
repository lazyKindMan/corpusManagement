<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/4/23
 * Time: 13:41
 */
require_once "mysqlConfig/mysqlConfig.php";
if(!isset($_POST['username']) or  !isset($_POST['password']) or !isset($_POST['email']))
    die(json_encode(-1));//返回缺少函数代码
$psd=md5($_POST['password']);
try{
    $PDO = new PDO("mysql:host={$address};dbname={$DBName['user']}",$user,"{$password}");
//sql语句
    $PDO->exec("set names utf-8");
//    NSERT INTO `db_user`.`tb_user` (`username`, `password`, `level_id`, `canlogin`) VALUES ('testAccount', '123', '5', '1');
    $sql="insert into tb_user (`username`, `password`, `email`,`level_id`, `canlogin`) values ('{$_POST['username']}','{$psd}','{$_POST['email']}',4,1)" ;
    if($PDO->query($sql)!=false)
        die(json_encode(1));
    else die(json_encode(0));
}
catch (PDOException $e)
{
    die ("Error!: " . $e->getMessage() . "<br/>");
}