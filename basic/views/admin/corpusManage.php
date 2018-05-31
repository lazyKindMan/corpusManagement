<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/16
 * Time: 22:33
 */
use yii\grid\GridView;
header("Content-type: text/html; charset=utf-8");
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
                      [
                           'attribute'=>'corpus_name',
                          'format'=>'text',
                          'label'=>'语料库名',
                      ],
                      [
                          'attribute'=>'all_level_count',
                          'format'=>'text',
                          'label'=>'词典分级数',
                      ],
                      [
                          'attribute'=>'created_at',
                          'format'=>'text',
                          'label'=>'语料创建时间',
                      ],
                      [
                          'attribute'=>'updated_at',
                          'format'=>'text',
                          'label'=>'最近修改时间',
                      ],
                      [
                          'label'=>'状态',
                          "value"=>function($model)
                          {
                              if($model->is_checking==1)
                                  return "正在审核";
                              if($model->is_checking==2)
                                  return "审核未通过";
                              if($model->is_deleting==1)
                                  return "正在审核删除";
                              if($model->is_updating==1)
                                  return "正在审核修改";
                              else return "正常";
                          }
                      ],
                      [
                          'class'=>'yii\grid\ActionColumn',
                          'template'=>'{showCorpus}  {deleteCorpus}',
                          'buttons'=>[
                                 "showCorpus"=>function($url,$model,$key){
                                        if($model->is_checking===1||$model->is_checking===2)
                                        return \yii\helpers\Html::button("查看语料库",[
                                            'title'=>'查看语料',
                                            'class'=>"btn btn-success",
                                            'disabled'=>"disabled"
                                        ]);
                                        return \yii\helpers\Html::button("查看语料库",[
                                         'title'=>'查看语料',
                                         'class'=>"btn btn-success",
                                     ]);
                                 },
                              "deleteCorpus"=>function($url,$model,$key)
                              {
                                  if($model->is_deleting===1||$model->is_checking===1||$model->is_checking===2||$model->is_updating===1)
                                      return \yii\helpers\Html::button("删除语料库",[
                                          'title'=>'删除语料',
                                          'class'=>"btn btn-danger",
                                          'disabled'=>"disabled"
                                      ]);

                                 return \yii\helpers\Html::button("删除语料库",[
                                  'title'=>'删除语料',
                                  'class'=>"btn btn-danger",
                              ]);
                              }
                          ]
                      ]
                  ],
              ]);
          }
          catch (Exception $exception)
          {
                echo $exception->getMessage();
          }
    ?>
    <?=\yii\helpers\Html::a("点击添加语料库","#addCorpusModal",['class'=>'col_lg-4 btn btn-success','onclick'=>'addCorpus()','data-toggle' => 'modal']);?>
    <?=\yii\helpers\Html::a("管理文本语料","javascript:void(0)",['class'=>'col_lg-4 btn btn-primary','onclick'=>'textCorporaManage()']);?>
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
<form class="form-horizontal" id="uploadCorpusForm" enctype="multipart/form-data" onsubmit="return false">
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
                    <button class="btn btn-primary" id="uploadCorpus" onclick="upload(this)">下一步</button>
                    <div id="warning"></div>
            </div>
        </div>
    </form>
    <div class="form-horizontal" id="createForm" style="display: none">

    </div>
    <div id="text_manage"></div>
<?php
\yii\bootstrap\Modal::end()?>
</div>
<div id="text_manage">

</div>
