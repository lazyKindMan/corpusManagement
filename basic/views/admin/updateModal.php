<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/16
 * Time: 15:52
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
$form=ActiveForm::begin([
    'id'=>'updateUser',
    'enableAjaxValidation'=>true,
    'validationUrl' => Url::toRoute(['validateUpdateUser',
//        'options' => ['class'=>'form-horizontal']
        ])
]);
//$labelClass='col-lg-2 control-label';
//$inputClass='col-lg-4';
?>
    <?=$form->field($model,'realname')->label("真实姓名")->textInput();?>
    <?=$form->field($model,'email')->label("电子邮件")->textInput();?>
    <?=$form->field($model,'workplace')->label("工作地点")->textInput();?>
    <?=$form->field($model,'sex')->radioList([0=>'男',1=>'女'])->label("性别");?>
    <?php
            echo $form->field($model,'level_id')->dropDownList(\app\models\Userlevel::dropDownList(\app\models\MyUser::getCurrentUserLevel()))->label("用户等级");
            //echo $form->field($userAuthorities,'authority_id',['labelOptions' => ['class' => 'col-lg-1 control-label']])->checkbox($authorities)->label("用户权限");
    ?>
