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
$this->registerJsFile('@web/js/rightGetAndShow?'.$code,["depends"=>["app\assets\AppAsset"],"position"=> $this::POS_END]);
?>

<section id="main">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 col-md-9 col-sm-9 ">

                <h3>用户列表 <span class="label label-danger"> New : 5</span></h3>
                <div class="row" id="searchItem">
                    <div class="col-lg-4 col-md-4 col-sm-4">
                        <?=Html::dropDownList('level_id', null,\app\models\Userlevel::dropDownList(), ['class' => 'dropdownlist form-comtrol'])?>
                    </div>
                    <div class="input-group col-lg-6 col-md-6 col-sm-6" id="user-search">
                        <input type="text" class="form-control" id="usernameSearch">
                        <span class="input-group-btn">
                            <button class="btn btn-info" id="searchUser">
                                搜索
                            </button>
                        </span>
                    </div>
                </div>
                <div class="hr-div"> <hr /></div>
                <?php \yii\widgets\Pjax::begin(['id'=>"userList"]);?>
                <div class="table-responsive">
                    <!--                --><?php
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
                                //用户权限
                                [
                                    'class'=>'yii\grid\ActionColumn',
                                    'template'=>'{updateRights}',
                                    'buttons'=>[
                                        'updateRights'=>function ($url,$model,$key)
                                        {
                                            return Html::a("修改权限",$url,[
                                                'title'=>'修改权限',
                                                'class'=>'btn btn-primary btn-update updateRight',
                                                'data-toggle'=>'modal',
                                                //目标模态框id
                                                'data-target'=>'#updateRight-modal'
                                            ]);
                                        }
                                    ],
                                ],
                                //修改密码
                                [
                                    'class'=>'yii\grid\ActionColumn',
                                    'template'=>'{updatePassword}',
                                    'buttons'=>[
                                        'updatePassword'=>function ($url,$model,$key)
                                        {
                                            return Html::a("修改密码",$url,[
                                                'title'=>'修改密码',
                                                'class'=>'btn btn-primary btn-danger updatePassword',
                                                'data-toggle'=>'modal',
                                                //目标模态框id
                                                'data-target'=>'#updatePassword-modal'
                                            ]);
                                        }
                                    ],
                                ],
                                //账号封禁
                                [
                                    'class'=>'yii\grid\ActionColumn',
                                    'template'=>'{updateAccount}',
                                    'buttons'=>[
                                        'updateAccount'=>function ($url,$model,$key)
                                        {
                                            return Html::a("账号封禁",$url,[
                                                'title'=>'账号封禁',
                                                'class'=>'btn btn-default btn-update updateAccount',
                                                'data-toggle'=>'modal',
                                                //目标模态框id
                                                'data-target'=>'#updateCanlogin-modal'
                                            ]);
                                        }
                                    ],
                                ],
                            ],
                        ]);
                    } catch (Exception $e) {
                        echo $e;
                    }
                    ?>
                    <?php \yii\widgets\Pjax::end();?>
                    <?php
                    use yii\bootstrap\Modal;
                    use yii\web\view;
                    Modal::begin([
                        'id'=>'updateRight-modal',
                        'header' => '<h4 class="modal-title">权限修改</h4>',
                    ]);
                    //ajax获取权限，增加保存和取消按钮
                    //加载动态js
                    ?>
                    <!--                    <div class="checkbox">-->
                    <!--                        <label><input type="checkbox" value="">选项 1</label>-->
                    <!--                    </div>-->
                    <!--动态加载js                    -->
                    <button class="btn btn-primary" id="updateRightsOk">修改</button>
                    <button class="btn btn-danger"  id="updateRightsCancel">取消</button>
                    <?php Modal::end();?>
                    <?php
                    Modal::begin(['id'=>'updatePassword-modal',
                        'header'=>'<h4 class="modal-title">密码修改</h4>',
                    ]);
                    ?>
                    <div class="form-group">
                        <label>新密码:</label><input id="password" type="password" class="form-control">
                        <label>请重复密码:</label><input id="repetePassword" type="password" class="form-control">
                    </div>
                    <button class="btn btn-primary" id="updatePasswordOk">确认</button>
                    <button class="btn btn-danger" data-dismiss="modal">取消</button>
                    <?php Modal::end();?>
                    <?php
                    Modal::begin(['id'=>'updateCanlogin-modal',
                        'header'=>'<h4 class="modal-title">账号禁封</h4>',
                    ]);?>
                    <div class="form-group">
                        <label>该用户当前账号状态为</label>
                        <select id="userStatus">
                            <option value=1>可以登录</option>
                            <option value=0>无法登录</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" id="updateCanlogin">确认</button>
                    <button class="btn btn-danger" data-dismiss="modal">取消</button>
                    <?php Modal::end();?>
                </div>

            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <a href="logout.html" class=" label label-danger"><strong>登出/注销</strong> </a>
                <div class="list-group">
                    <?= Html::a('账户资料管理', ['index'], ['class' => 'list-group-item']) ?>

                    <a href="#" class="list-group-item active">用户管理</a>

                    <a href="admin-user-list.html" class="list-group-item">语料库管理</a>
                    <a href="admin-open-tickets.html" class="list-group-item">自动爬虫管理</a>
                </div>
                <div class="alert alert-danger text-center">
                    <h3>欢迎你</h3>
                    <?php echo yii::$app->user->identity->getUsername();?>
                </div>
            </div>
        </div>
</section>         <style>
    .select_bg{ background:#BCC8D0;  }
</style>
