<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/8
 * Time: 16:22
 */
$postData=array(
    'username'=>"9eab4c82-aa98-4791-9f99-8a35cf5984d4",
    'password'=>'gcpdl6UbBENB'
);
$url="https://gateway.watsonplatform.net/discovery/api";
$ch = curl_init(); //初始化curl
curl_setopt($ch, CURLOPT_URL, $url);//设置链接
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
//curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置HTTP头
curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);//POST数据
curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
$response = curl_exec($ch);//接收返回信息
if(curl_errno($ch)){//出错则显示错误信息
    print curl_error($ch);
}
curl_close($ch);
echo $response;
