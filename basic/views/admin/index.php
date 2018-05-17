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
$this->registerJsFile('@web/js/modal.js?'.$code,["depends"=>["app\assets\AppAsset"],"position"=> $this::POS_END]);
$this->registerJsFile('@web/js/loadTab.js?'.$code,["depends"=>["app\assets\AppAsset"],"position"=> $this::POS_END]);
?>
<section id="main">
    <div class="container">
    <div class="col-lg-9 col-md-9 col-sm-9 ">
        <div id="myTab">
            <!--        用户信息界面-->
            <div id="account" class="tab">
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
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">工作地点/单位:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="WorkPlace" placeholder="xx公司" name="workPlace">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">真实姓名:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="realName" placeholder="请输入真实姓名" name="realname">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label" name="sex">性别:</label>
                        <div class="col-sm-8">
                            <label class="radio"><input type="radio" value=0 name="sex">男</label>
                            <label class="radio"><input type="radio" value=1 name="sex">女</label>
                            <label class="radio"><input type="radio" value=2 name="sex">才不告诉你</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="font-size: large" for="inputWorkPlace" class="col-sm-2 col-lg-2 control-label">邮箱:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="inputEmail" placeholder="xx@eaxmple.com" name="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3 col-sm-offset-2">
                            <button class="btn btn-info" id="changeUserMessage">保存修改</button>
                        </div>
                        <div class="col-sm-3 ">
                            <button class="btn btn-danger" id="reset">重置信息</button>
                        </div>
                        <div class="col-sm-4">
                            <button class="btn btn-primary" id="changePassword">点击此处修改账户密码</button>
                        </div>
                    </div>
                </form>
            </div>
<!--            用户管理页面-->
            <div id="userManage" style="display: none" class="tab">
                <h3>用户列表 <span class="label label-danger"> New : 5</span></h3>
                <div class="hr-div"> <hr /></div>
                <?php \yii\widgets\Pjax::begin(['id'=>"userList"]);?>
                <div class="table-responsive">
                    <?php
                    try {
                        echo GridView::widget([
                            'dataProvider' => $dataProvider,
//                        'filterModel' => $searchModel,
                            'emptyText'=>'当前没有内容',
                            'emptyTextOptions'=>['style'=>'color:red;font-weight:bold'],
                            'rowOptions'=>function($model){
                                return ['id'=>"tr-".$model->id];
                            },
                            'columns' => [
                                [
                                    'class' => 'yii\grid\CheckboxColumn',
                                    //'cssClass'=>'_check',//不能用？？？？后面有js实现的
                                    //底部第一列占6格，其他列隐藏，形成合并1个单元格效果
                                    'footerOptions'=>['colspan'=>6],
                                    'footer'=>'<a href="javascript:;" class="_delete_all" data-url="'.Yii::$app->urlManager->createUrl(['/attend/delete_all']).'">删除全部</a>',

                                ],
                                //行号
                                [
                                    'class' => 'yii\grid\SerialColumn',

                                ],
                                //用户id
                                [
                                    'attribute'=>'id',
                                    'format'=>'text',
                                    'label'=>'用户id',
                                    //给td添加class
                                    'contentOptions'=>['class'=>'userId']
                                ],
                                //用户账号名
                                [
                                    'attribute'=>'username',
                                    'format'=>'text',
                                    'label'=>'用户名',
                                    'contentOptions'=>['class'=>'username']
                                ],
                                //用户等级
                                [
                                    'attribute'=>'level_id',
                                    'format'=>'text',
                                    'value'=>'level_name',
                                    'label'=>'用户等级',
                                ],
                                //真实姓名
                                [
                                        'attribute'=>'realname',
                                        'label'=>'真实姓名',
                                       'value'=>function($model){
                                            if($model->realname==null||$model->realname=='')
                                                return '';
                                            return $model->realname;
                                       }
                                ],
                                //账号封禁
                                [
                                   'attribute'=>'canLogin',
                                   'label'=>'账号状态',
                                   'value'=>function($model){
                                        if($model->canlogin==1)
                                            return '允许登录';
                                        return '禁止登录';
                                    },
                                ],
                                //电子邮箱
                                [
                                      'attribute'=>'email',
                                      'label'=>'邮箱',
                                    'contentOptions'=>['class'=>'email']
                                ],
                                //性别
                                [
                                    'attribute'=>'sex',
                                    'label'=>'性别',
                                    'value'=>function($model)
                                    {
                                        if($model->sex===2||$model->sex===null)
                                            return "";
                                        if($model->sex===1)
                                            return "女";
                                        return "男";
                                    }
                                ],
                                [
                                        'class'=>'yii\grid\ActionColumn',
                                        'header'=>'操作',
                                        'template'=>'{view},{update}{delete}',
                                    'buttons'=>[
                                            'view'=>function($url,$model,$key){
                                            $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-eye-open"]);
                                            return Html::a($icon,'javascript:;',['onclick'=>'show_detail(this,'.$model->id.');','title'=>'查看详情','data-toggle' => 'modal',
                                                'data-target' => '#PublicModal',]);
                                            },
                                            'update'=>function($url,$model,$key){
                                                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon glyphicon-pencil"]);
                                                return Html::a($icon,'javascript:;',['onclick'=>'show_updateModal('.$model->id.');','title'=>'修改用户信息','data-toggle' => 'modal',
                                                    'data-target' => '#PublicModal',]);
                                            }

                                    ],
                                ]
                            ],
                        ]);
                    } catch (Exception $e) {
                        echo $e;
                    }
                    ?>
                    <?php \yii\widgets\Pjax::end();?>
            </div>
        </div>
<!--            语料库管理界面-->
            <div id="corpusManage" style="display: none" class="tab">

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
<!--    模态框-->
</section>
<?php
\yii\bootstrap\Modal::begin([
    'id' => 'PublicModal',
    'header' => '<h4 class="modal-title"></h4>',
    'footer'=>'<button class="btn btn-info" data-dismiss="modal">关闭</button>',
    'size'=>yii\bootstrap\Modal::SIZE_LARGE,
]);
?>
<?php \yii\bootstrap\Modal::end()?>
