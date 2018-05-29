$(document).ready(function () {
    //监听表单提交
});
function upload(obj) {
        var $form=document.getElementById('uploadCorpusForm');//不能为jquery对象
        // console.log($form.find(".control-label"));
        $('#warning').text('正在处理，请稍等！');
        var formData=new FormData($form);
        console.log(formData);
        $.ajax({
                url: "upload-file.html",
                type: "POST",
                data:formData,
                dataType:"json",
                async:true,
                processData:false,
                contentType:false,
                beforeSend: function(xhr){
                    $("#importForm :submit").attr("disabled",true);
                },
                complete: function(xhr,status){
                    $("#importForm :submit").attr("disabled", false);
                },
                error: function(xhr,status,error){
                    $('#warning').text('');
                    alert("请求出错!");
                },
                success:function (res) {
                    $('#warning').text('');
                    if(res['code']==1)
                    {
                        $("#uploadCorpusForm").toggle('100');
                        $("#createForm").toggle("100");
                        createForm(res['all_level_count'],res['keys']);
                    }
                    else alert("输入有误");
                }
            }
        );
}
function addCorpus() {
    $(".fileUpload").empty();
    var element="<label class=\"control-label col-lg-2\">语料文件上传</label>\n" +
        "            <div class=\"col-lg-8\">\n" +
        "                <input type=\"file\" name=\"upload_txt\" id=\"uploadFile\" class=\"file\">\n" +
        "            </div>";
    $(".fileUpload").append(element);
    var path="upload-file.html";
    initFileInput("uploadFile",path);
}
function initFileInput(ctrlName,uploadUrl) {
    var control=$("#"+ctrlName);
    control.fileinput({
        language: 'zh', //设置语言
        uploadUrl: uploadUrl,  //上传地址
        showUpload: false, //是否显示上传按钮
        showRemove:true,
        dropZoneEnabled: false,
        showCaption: false,//是否显示标题
        allowedPreviewTypes: ['text'],
        allowedFileTypes: ['text'],
        allowedFileExtensions:  ['txt'],
        maxFileSize : 1024*30,
        maxFileCount: 1,
        msgSizeTooLarge:"文件大小不能超过30M",
        uploadAsync:false,
    });
}
function createForm(levelNum,keys) {
    //创建表单
    $("#createForm").html('');//清空内容
    for(var i=1;i<levelNum;i++)
    {
        var content="\n" +
            "        <div class=\"form-group\">\n" +
            "            <label class=\"control-label col-lg-2 col-md-2\">第"+(i+1)+"级中文名</label>\n" +
            "            <div class=\"col-lg-4 col-md-4 col-sm-4\">\n" +
            "                <input type=\"text\" class=\"form-control\" id=\"level_"+(i+1)+"\">\n" +
            "            </div>\n" +
            "        </div>";
        $("#createForm").append(content);
    }
    //添加select内容
    for (var i=0;i<keys.length;i++)
    {
        var content="<div class=\"form-group\">\n" +
            "            <label class=\"control-label col-lg-1 col-md-1\">键值:</label>\n" +
            "            <label class='form-control-static col-lg-2 col-md-2'>" +keys[i]+"</label> \n"+
            "            <label class=\"control-label col-lg-2 col-md-2\">所在层级:</label>\n" +
            "            <div class=\"col-lg-2 col-md-2 col-sm-2\">\n" +
            "                <select class=\"level_num form-control\">\n" +
            "\n" +
            "                </select>\n" +
            "            </div>\n" +
            "<label class=\"control-label col-lg-2 col-md-2\">键值别名:</label>\n"+
            "<div class='col-lg-3 col-md-2 col-sm-2  alias'><input type='text' class='form-control'></div>\n"+
            "        </div>";
        $("#createForm").append(content);
    }
    for(var i=1;i<levelNum;i++)
    {
        $(".level_num").append("<option value=\""+(i+1)+"\">"+(i+1)+"</option>");
    }
    //语料库名
    $("#createForm").append("<div class='form-group'>" +
        "<label class='control-label col-lg-4 col-sm-4 col-md-4'>语料库名称</label>" +
        "<div class='col-lg-6 col-sm-6 col-md-6'><input class='form-control' id='corpusName'></div>" +
        "</div>");
    $("#createForm").append("<div class='form-group'>" +
        "<label class='control-label col-lg-4 col-sm-4 col-md-4'>语料库表名前缀(纯英文和数字)</label>" +
        "<div class='col-lg-6 col-sm-6 col-md-6'><input class='form-control' id='corpusPre'></div>" +
        "</div>");
    //添加按钮
    $("#createForm").append("<button class=\"btn btn-primary\" id=\"backPre\">上一步</button>\n" +
        "        <button class=\"btn btn-success\" id=\"CreateButton\">确认提交</button><div id=\"warning1\"></div>");
    //添加按钮事件
    $("#backPre").click(function () {
        $(this).attr('disabled',"true");
        $.post(
            "delete-file.html",
            {},
            function (data) {
                if(data==1)
                {
                    $("#uploadCorpusForm").toggle('100');
                    $("#createForm").toggle("100");
                    $(this).attr('disabled',"false");
                }
                else alert("文件撤销失败，请重试");
            }
        )
        return false;
    });
    //提交事件
    $("#CreateButton").click(function () {
        $(this).attr('disabled',"true");
        $('#warning1').text('正在处理，请稍等！');
        //获取语料库名称
        var corpusName=$("#corpusName").val();
        var corpusPre=$("#corpusPre").val();
        //获取每一级名
        var levelNames=[];
        for(var i=1;i<levelNum;i++)
        {
            levelNames[i-1]=$("#level_"+(i+1)).val();
        }
        var levelKey=new Array();
        //获取每一层的键值和其别名
        $("#createForm").find("select").each(function () {
            var levelNum=$(this).val()-2;
            if(!levelKey.hasOwnProperty(levelNum))
            {
                levelKey[levelNum]=new Array();
            }
                var $key=$(this).parent().siblings(".form-control-static").text();
                console.log($key);
                var $alias=$(this).parent().siblings(".alias").find("input").val()  ;
                levelKey[levelNum].push($key);
                levelKey[levelNum].push($alias);
        });
        console.log(levelKey);
        $.post(
            "create-dic-corpus.html",
            {corpusName:corpusName,corpusPre:corpusPre,levelNames:levelNames,levelKey:levelKey},
            function (data) {
                console.log(levelKey);
                if(data==1)
                {
                    alert('添加成功，进入审核');
                    //关闭模态框并清除词典
                    $("#addCorpusModal").modal("hide");
                    $("#uploadCorpusForm").toggle();
                    $("#createForm").toggle();
                    $(".fileUpload").empty();
                    var element="<label class=\"control-label col-lg-2\">语料文件上传</label>\n" +
                        "            <div class=\"col-lg-8\">\n" +
                        "                <input type=\"file\" name=\"upload_txt\" id=\"uploadFile\" class=\"file\">\n" +
                        "            </div>";
                    $(".fileUpload").append(element);
                }
                else alert(data);
                $(this).attr('disabled',"false");
                $('#warning1').text('');
                window.reload();
            },"json"
        );
        return false;
    })
}