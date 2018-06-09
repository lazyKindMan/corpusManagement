<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/5
 * Time: 20:31
 */
use yii\helpers\Html;
?>
<div class="table-responsive">
<?php
try
{
    if($code==1)
        echo \yii\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'emptyText'=>'当前搜索没有内容',
            'emptyTextOptions'=>['style'=>'color:red;font-weight:bold'],
            'columns'=>[
                [
                    'class' => 'yii\grid\SerialColumn',

                ],
                [
                    'attribute'=>'resource',
                    'format'=>'text',
                    'label'=>'语料来源',
                ],
                [
                    'attribute'=>'content',
                    'format'=>'text',
                    'label'=>'语料内容',
                ],
//                [
//                'attribute'=>'corpus_id',
//                'format'=>'text',
//                'label'=>'id',
//                ],
                [
                        'class'=>'yii\grid\ActionColumn',
                        'header'=>'操作',
                        'template'=>'{view}',
                        'buttons'=>[
                            'view'=>function($url,$model,$key){
                                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-eye-open"]);;
                                return Html::a($icon,'javascript:;',['onclick'=>'showContent(this,'.$model['corpus_id'].');','title'=>'查看详情','data-toggle' => 'modal',
                                    'data-target' => '#PublicModal',]);
                            },
                        ],
                ]
        ]
        ]);
    else{
        echo "<h3>$message</h3>";
    }
}catch (Exception $e)
{
   echo $e->getMessage();
}
?>
</div>
<button class="btn btn-primary" onclick="backTo()">返回检索界面</button>