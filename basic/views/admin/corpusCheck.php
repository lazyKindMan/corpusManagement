<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/27
 * Time: 22:58
 */
use yii\grid\GridView;
 \yii\widgets\Pjax::begin(['id'=>"corpusCheck"]);
try {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'emptyText' => '当前没有审核任务',
        'emptyTextOptions' => ['style' => 'color:red;font-weight:bold'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'attribute'=>'corpus_name',
                'label'=>'语料库名',
            ],
            [
                'attribute'=>'updated_at',
                'label'=>'上次更新操作时间',
            ],
            [
                'attribute'=>'created_at',
                'label'=>'语料库创建时间',
            ],
            [
                'attribute'=>'op',
                'label'=>'审核的操作',
                'value'=>function($model){
                        if($model->op==\app\models\check\CorporaCheckModel::OPADD)
                            return "语料添加";
                    if($model->op==\app\models\check\CorporaCheckModel::OPDELETE)
                        return "删除语料";
                    else return "未知操作";
                }
            ],
            ]
    ]);
} catch (Exception $e) {
    echo $e->getMessage()." in ".$e->getLine().$e->getFile()."\n";
    echo $e->getTraceAsString();
}
\yii\widgets\Pjax::end();
?>

