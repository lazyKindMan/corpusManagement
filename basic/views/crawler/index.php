<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/11
 * Time: 10:19
 */
$code=rand(1,100);
$this->registerJsFile('@web/js/cralwerAjax?'.$code,["depends"=>["app\assets\AppAsset"],"position"=> $this::POS_END]);