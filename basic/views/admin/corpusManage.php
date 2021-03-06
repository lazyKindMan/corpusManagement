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
                                        $corpus_id=$model->corpus_id;
                                        return \yii\helpers\Html::button("查看语料库",[
                                         'title'=>'查看语料',
                                         'class'=>"btn btn-success",
                                          'onclick'=>"showDictionaryDetail($corpus_id,1)"
                                     ]);
                                 },
                              "deleteCorpus"=>function($url,$model,$key)
                              {
                                  $corpus_id=$model->corpus_id;
                                  if($model->is_deleting===1||$model->is_checking===1||$model->is_checking===2||$model->is_updating===1)
                                      return \yii\helpers\Html::button("删除语料库",[
                                          'title'=>'删除语料',
                                          'class'=>"btn btn-danger",
                                          'disabled'=>"disabled",
                                          'onclick'=>"deleteDictionaryCorpus(this,$corpus_id)"
                                      ]);

                                 return \yii\helpers\Html::button("删除语料库",[
                                  'title'=>'删除语料',
                                  'class'=>"btn btn-danger",
                                     'onclick'=>"deleteDictionaryCorpus(this,$corpus_id)"
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
<?php
\yii\bootstrap\Modal::end()?>
</div>
<div id="text_manage" style="display: none">
    <table class="table table-striped" id="textCorporaTable">
        <thead>
        <tr>
            <th>语料库名称</th>
            <th>创建时间</th>
            <th>语料来源</th>
            <th>标题</th>
            <th>词语数</th>
            <th>词类数</th>
            <th>开放等级</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
    <div>
        <ul class="pagination col-lg-6 col-sm-6 col-md-6">
            <li><a href="javascript:void(0)" onclick="clickJumpPage(this,1)">首页</a></li>
            <li class="previous"><a href="javascript:void(0)" onclick="clickJumpPage(this,2)">前一页</a></li>
            <li class="next"><a href="javascript:void(0)" onclick="clickJumpPage(this,3)">后一页</a></li>
            <li><a href="javascript:void(0)" onclick="clickJumpPage(this,4)">末页</a></li>
        </ul>
    </div>
    <div style="margin: 30px 0px;">
        <label class="col-lg-1 col-md-1 col-sm-1" style="padding: 12px 0px">当前页:</label>
        <label class="currentPage col-lg-1 col-md-1 col-sm-1" style="padding: 12px 0px" id="currentPage"></label>
        <label class="col-lg-1 col-md-1 col-sm-1" style="padding: 12px 0px">跳转至:</label>
        <div class="col-lg-1 col-md-1 col-sm-1">
            <input class="form-control" id="jumpPage" style="margin:10px 0px;padding: 0px;height:30px" size="2">
        </div>
        <button class="btn btn-primary" style="margin:10px 0px" onclick="jumpPage(this)">跳转</button>
    </div>
    <div>
        <button class="btn btn-info" onclick="backDictionaryManage()">词典语料管理</button>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addTextCorpusModal">添加文本语料</button>
    </div>
</div>
<!--文本语料统计详情模态框-->
<!--<div id="chartdiv" style="height:500px;width: 400px"></div>-->
<div class="modal fade" id="addTextCorpusModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>文本语料添加</h5></div>
            <div class="modal-body">
                <form id="addTextCorpusForm">
                    <div class="form-group">
                        <label class="control-label">语料标题:</label>
                        <div >
                            <input type="text" class="form-control"  name="title">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">语料来源:</label>
                        <div>
                            <input type="text" class="form-control"  name="resource">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">语料库名称:</label>
                        <div>
                            <input type="text" class="form-control"  name="corpus_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">带标记语料内容:</label>
                        <div>
                            <textarea class="form-control" rows="15" name="content">

                            </textarea>
                        </div>
                    </div>
                </form>
                </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="addTextCorpus()">提交</button>
                <button class="btn btn-danger" data-dismiss="modal">关闭</button>
            </div>
            </div>
        </div>
    </div>
</div>