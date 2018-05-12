<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/1
 * Time: 17:57
 *
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//标题
$this->title='语料库管理系统';
?>
<section >
    <div class="container">
        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-4">
<!--                <img src="assets/img/up.png" class="img-responsive" />-->
                <?=HTML::img('@web/img/up.png',['class'=>'img-responsive'])?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
<!--                <img src="assets/img/moniter.png" class="img-responsive img-max-width" />-->
                <?=HTML::img('@web/img/moniter.png',['class'=>['img-responsive','img-max-width']])?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <h3>管理员登录 </h3>
<!--                <div class="form-group">-->
<!--                    <input type="text" name="username" id="username" class="form-control" required="required" placeholder="Username" />-->
<!--                </div>-->
<!--                <div class="form-group">-->
<!--                    <input type="text" name="password" id="password" class="form-control" required="required" placeholder="Your Password" />-->
<!--                </div>-->
<!--                <div class="form-group">-->
<!--                    <button name="login" id="login" class="btn btn-success">登入</button>-->
<!--                </div>-->
            <?php $form=ActiveForm::begin(['id'=>'form-login']);?>
                <?=$form->field($model,'username')->label('用户名')->textInput()?>
                <?=$form->field($model,'password')->label('密码')->passwordInput()?>
                <?=$form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
                    'template' => '<div class="row"><div class="col-lg-7">{input}</div><div class="col-lg-4">{image}</div></div>',
                    'imageOptions' => ['title' => '换一个', 'alt' => '验证码', 'style' => 'cursor:pointer;']
                ]); ?>
                <?=$form->field($model,'rememberMe')->label("记住我")->checkbox()?>
                <div class="form-group">
                    <?=Html::submitButton('登陆',['class'=>'btn btn-primary','name'=>'login-button'])?>
                </div>
                <?php ActiveForm::end();?>
            </div>
        </div>

    </div>
</section>