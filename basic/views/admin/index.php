<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/3
 * Time: 14:11
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\captcha\Captcha;
use yii\grid\GridView;
$this->title='管理员用户';
$code=rand(1,100);
?>
<section id="main">
    <div class="container">
    <div class="col-lg-9 col-md-9 col-sm-9 ">
        
    </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <a href="logout.html" class=" label label-danger"><strong>登出/注销</strong> </a>
                <div class="list-group">
                    <a href="#" class="list-group-item active">账户资料管理
                    </a>

<!--                    <a href="" class="list-group-item">用户管理</a>-->
                    <?= Html::a('用户管理', ['user-manage'], ['class' => 'list-group-item ']) ?>
                    <a href="admin-user-list.html" class="list-group-item">语料库管理</a>
                    <a href="admin-open-tickets.html" class="list-group-item">自动爬虫管理</a>
            </div>
                <div class="alert alert-danger text-center">
                    <h3>欢迎你</h3>
                    <?php echo yii::$app->user->identity->getUsername();?>
                </div>
        </div>
    </div>
</section>
