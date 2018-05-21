<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/16
 * Time: 22:33
 */
use yii\grid\GridView;
?>
<h3>语料库管理</h3>
<div class="hr-div"> <hr /></div>
<div id="dictionary_manage">
    <div class="table-responsive">
    <?php \yii\widgets\Pjax::begin(['id'=>"DCorpusManage"]);
          try{
              echo GridView::widget([
                 'dataProvider'=>$dataProvider,
                  'emptyText'=>'当前没有字典型语料库',
                  'emptyTextOptions'=>['style'=>'color:red;font-weight:bold'],
                  'rowOptions'=>function($model){
                      return ['id'=>"tr-".$model->corpus_id];
                  },
                  'columns'=>[
                      [
                              'class' => 'yii\grid\SerialColumn',
                      ],
                  ],
              ]);
          }
          catch (Exception $exception)
          {
                echo $exception->getMessage();
          }
    ?>
    <?=\yii\helpers\Html::a("点击添加语料库","#addCorpusModal",['class'=>'col_lg-4 btn btn-success','onclick'=>'addCorpus()','data-toggle' => 'modal']);?>

    <?php \yii\widgets\Pjax::end();?>

</div>
<!--    模态框-->
<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
\yii\bootstrap\Modal::begin([
    'id' => 'addCorpusModal',
    'header' => '<h4 class="modal-title">语料库添加</h4>',
    'footer'=>'<button class="btn btn-info" data-dismiss="modal">关闭</button>',
    'size'=>yii\bootstrap\Modal::SIZE_LARGE,
]);
?>
<form class="form-horizontal" id="uploadCorpusForm" action="upload-file.html" enctype="multipart/form-data">
<!--        <div class="form-group">-->
<!--                <label class="control-label col-lg-2">语料库名</label>-->
<!--                <div class="col-lg-8">-->
<!--                    <input class="form-control col-lg-6" type="text" id="corpus_name" placeholder="库建立的名称,建立后无法更改">-->
<!--                </div>-->
<!--        </div>-->
        <div class="form-group">
            <label class="control-label col-lg-2">词典分级等级:</label>
            <div class="col-lg-8">
                <select id="all_level_count" class="form-control" name="all_count_level">
                    <option value=2>2级</option>
                    <option value=3>3级</option>
                    <option value=4>4级</option>
                    <option value=5>5级</option>
                </select>
            </div>
        </div>
        <div class="form-group fileUpload">
            <label class="control-label col-lg-2">语料文件上传</label>
            <div class="col-lg-8">
                <input type="file" name="uploadFile" id="uploadFile" class="file">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-2">文件切割项的key和value值间隔符(一个字符)</label>
            <div class="col-lg-1">
                <input type="text" class="form-control col-lg-1" id="split_character" name="split_character">
            </div>
        </div>
        <div class="alert alert-warning">警告：本系统目前仅支持单文件上传，文件每行的项由空格分割，项以key（分隔符）value形式存储，不支持其他格式文件上传</div>
        <div class="form-group">
             <div class="col-lg-offset- col-md-offset-2 col-sm-offset-2">
                    <button class="btn btn-primary" id="uploadCorpus" type="submit" name="submit">下一步</button>
                    <div id="warning"></div>
            </div>
        </div>
    </form>
    <form class="form-horizontal" id="createForm" action="create-dic-corpus.html" style="display: none">

    </form>
<?php
\yii\bootstrap\Modal::end()?>