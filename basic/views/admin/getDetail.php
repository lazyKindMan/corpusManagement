<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/16
 * Time: 14:34
 */

\yii\grid\GridView::begin([
    'dataProvider' => $dataProvider,
    'emptyText'=>'当前没有内容',
    'emptyTextOptions'=>['style'=>'color:red;font-weight:bold'],
    'columns' => [
        //用户id
        [
            'attribute'=>'id',
            'format'=>'text',
            'label'=>'用户id',
            //给td添加class
            'contentOptions'=>['class'=>'id']
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
            'attribute'=>'workplace',
            'label'=>'工作地点',
            'value'=>function($model)
            {
                if($model->workplace)
                    return "";
                return $model->workplace;
            }
        ],
        [
            'attribute'=>'created_at',
            'label'=>'注册时间',
            'contentOptions'=>['class'=>'created_at']
        ],
        [
            'attribute'=>'updated_at',
            'label'=>'上次修改时间',
            'contentOptions'=>['class'=>'updated_at']
        ]
    ],
]);
\yii\grid\GridView::end();
?>