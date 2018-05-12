<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/1
 * Time: 15:14
 * 页面头和尾布局
 */
use yii\helpers\Html;
use app\assets\AppAsset;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <?= Html::csrfMetaTags() ?>
    <title><?=Html::encode($this->title)?></title>
    <?php $this->head()?>
</head>
<body>
<?php $this->beginBody()?>
<!--页面头-->
<div id="head">
    <div class="container">
        <div class="row">

            <div class="col-lg-3 col-md-3 col-sm-3">
                <p href="index.html" style="font-size: 30px;font-family: tahoma,arial,sans-serif;">
                    语料库管理系统
                </p>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3 text-center" >

            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <input type="text" name="search" placeholder="请输入搜索语料" class="form-control">
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1">
                <button class="btn btn-danger ">搜索</button>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1">
                <button class="btn btn-info">进入高级搜索</button>
            </div>
        </div>
    </div>
</div>
<?= $content?>
<div id="footer">
    <div class="container">
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage()?>
