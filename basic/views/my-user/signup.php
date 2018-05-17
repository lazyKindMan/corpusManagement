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
$this->title='语料库管理系统';
?>
<section >
    <div class="container">
        <div class="row">

            <div class="col-lg-4 col-md-4 col-sm-4">
<!--                <img src="assets/img/premium.png" class="img-responsive" />-->
                <?=HTML::img('@web/img/premium.png',['class'=>'img-responsive'])?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
<!--                <img src="assets/img/moniter.png" class="img-responsive img-max-width" />-->
                <?=HTML::img('@web/img/moniter.png',['class'=>'img-responsive'])?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <h3>用户登录 </h3>
                <form>
                    <div class="form-group">
                        <input type="text" class="form-control" required="required" placeholder="Username / Email" />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" required="required" placeholder="Your Password" />
                    </div>
                    <div class="form-group">
                        <a href="user-dashboard.html" class="btn btn-success">点击登录</a>
                    </div>
                    <a href="#">忘记密码 ?</a>

                </form>
            </div>
        </div>

    </div>

</section>
<section>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
<!--                <img src="assets/img/award.png" class="img-responsive" />-->
                <?=HTML::img('@web/img/award.png',['class'=>'img-responsive'])?>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">

                <h3>在此处注册</h3>
                <div id="register">
<!--                    <div class="form-group">-->
<!--                        <input type="text" class="form-control username" required="required" placeholder="Desired Username" />-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <input type="text" class="form-control email" required="required" placeholder="Your Email" />-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <input type="text" class="form-control password password" required="required" placeholder="Desired Password" />-->
<!--                    </div>-->
<!---->
<!--                    <div class="form-group">-->
<!--                        <button  class="btn btn-primary ok">点击此处注册</button>-->
<!--                    </div>-->
<!--                    <strong>注意: </strong>在此处注册证明你同意我们的  <a href="#">条款和事项</a>-->
                    <?php $form=ActiveForm::begin(['id'=>'form-signup',
                        'enableAjaxValidation'=>true,
                        'validationUrl' => Url::toRoute(['signup']),
                        'validateOnChange'=>false,
                        ]);?>
                        <?=$form->field($model,'username')->label('注册用户名')->textInput()?>
                        <?=$form->field($model,'email')->label('注册邮箱')?>
                        <?=$form->field($model,'password')->label('登录密码')->passwordInput()?>
                        <?= $form->field($model, 'verifyCode')->widget(\yii\captcha\Captcha::className(), [
                        'template' => '<div class="row"><div class="col-lg-7">{input}</div><div class="col-lg-4">{image}</div></div>',
                        'imageOptions' => ['title' => '换一个', 'alt' => '验证码', 'style' => 'cursor:pointer;']
                        ]); ?>
                        <div class="form-group">
                            <?=Html::submitButton('注册',['class'=>'btn btn-primary','name'=>'signup-button'])?>
                        </div>
                    <?php ActiveForm::end();?>
                </div>
            </div>


            <div class="col-lg-4 col-md-4 col-sm-4">
<!--                <img src="assets/img/up.png" class="img-responsive" />-->
                <?=HTML::img('@web/img/up.png',['class'=>'img-responsive'])?>
            </div>

        </div>
    </div>

</section>
