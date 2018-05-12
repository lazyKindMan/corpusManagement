<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/1
 * Time: 14:46
 * 网站主页（检索和登录入口）
 */
use yii\helpers\Html;
//标题
$this->title='语料库管理系统';
?>
<section style="padding:100px 0px 0px 0px;" >
    <div class="container">
        <div class="row">

            <div class="col-lg-6 col-lg-offset-3  col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 ">
                <div class="alert alert-info">
                    <div class="media">
                        <div class="pull-left">
<!--                            <img src="assets/img/admin.png" class="img-responsive" />-->
                            <?=HTML::img('@web/img/admin.png',['class'=>'img-responsive'])?>
                        </div>
                        <div class="media-body">
                            <h3 class="media-heading">管理员登录</h3>
                            <p>

                            </p>
                            <?= Html::a('转向管理员登录界面', ['admin/login'], ['class' => 'btn btn-primary ']) ?>
                        </div>
                    </div>


                </div>
                <div class="alert alert-danger">
                    <div class="media">
                        <div class="pull-left">
<!--                            <img src="assets/img/admin.png" class="img-responsive" />-->
                            <?=HTML::img('@web/img/admin.png',['class'=>'img-responsive'])?>
                        </div>
                        <div class="media-body">
                            <h3 class="media-heading">用户登录</h3>
                            <p>

                            </p>
<!--                            <a href="userIndex.html" class="btn btn-danger " target="_blank">转向用户登录界面</a>-->
                            <?= Html::a('转向用户登录界面', ['my-user/signup'], ['class' => 'btn btn-danger ']) ?>
                        </div>
                    </div>


                </div>
            </div>

        </div>

    </div>

</section>
