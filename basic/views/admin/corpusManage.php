<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/16
 * Time: 22:33
 */
$code=rand(1,100);
$this->registerJsFile('@web/js/corpusManage.js?'.$code,["depends"=>["app\assets\AppAsset"],"position"=> $this::POS_END]);
?>
