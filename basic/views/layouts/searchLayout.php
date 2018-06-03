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
        <title>语料检索</title>
        <?php $this->head()?>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    </head>
    <body>
    <?php $this->beginBody()?>
    <!--页面头-->
    <?= $content?>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage()?>