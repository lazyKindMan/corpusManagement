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
$this->registerJsFile('@web/js/loadMenu.js?'.$code,["depends"=>["app\assets\AppAsset"],"position"=> $this::POS_END]);
?>
<section id="main">
    <div class="container">
    <div class="col-lg-9 col-md-9 col-sm-9 ">
        <div id="myTab" class="tab-content">
            <!--        用户信息界面-->
            <div class="tab-pane fade in active" id="account">
                <H3>用户信息</H3>
                <HR style="FILTER: alpha(opacity=100,finishopacity=0,style=3)" width="80%" color=#987cb9 SIZE=3>
                <form class="form-horizontal" id="userMessage">
                    <div class="form-group">
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <label style="font-size: large" class="col-lg-6 control-label">用户名:</label>
                            <label style="font-size: large" class="form-control-static userName col-lg-6"></label>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4">
                            <label style="font-size: large" class="col-lg-6 control-label">用户等级:</label>
                            <label style="font-size: large" class="form-control-static level col-lg-6"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">工作地点:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="WorkPlace" placeholder="xx公司">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">真实姓名:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="realName" placeholder="请输入真实姓名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">性别:</label>
                        <div class="col-sm-8">
                            <label class="radio"><input type="radio" value=0 name="sex">男</label>
                            <label class="radio"><input type="radio" value=1 name="sex">女</label>
                            <label class="radio"><input type="radio" value=2 name="sex">才不告诉你</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">邮箱:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputEmail" placeholder="xx@eaxmple.com">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary">点击此处修改账户密码</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <a href="logout.html" class=" label label-danger"><strong>登出/注销</strong> </a>
                <div class="list-group tab-content" id="menu">
                        <a href="#account" class="list-group-item active" data-toggle="tab">帐户信息</a>
                </div>
                <div class="alert alert-danger text-center">
                    <h3>欢迎你</h3>
                    <?php echo yii::$app->user->identity->getUsername();?>
                </div>
            </div>
    </div>
</section>
