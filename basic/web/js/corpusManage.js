$(document).ready(function () {
});
function addCorpus() {
    $(".fileUpload").empty();
    var element="<label class=\"control-label col-lg-2\">语料文件上传</label>\n" +
        "            <div class=\"col-lg-8\">\n" +
        "                <input type=\"file\" name=\"upload_txt\" id=\"uploadFile\" class=\"file\">\n" +
        "            </div>";
    $(".fileUpload").append(element);
    var path="upload-file.html";
    initFileInput("uploadFile",path);
    //监听表单提交
    $("#uploadCorpusForm").on('submit',function (event) {
        var form=this;
        $('#warning').text('正在处理，请稍等！');
        var formData=new FormData(form);
        $.ajax({
                url: this.action,
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
        return false;//阻止表单提交
    })
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
            "            <label class=\"control-label col-lg-2 col-md-2\">键值:</label>\n" +
            "            <label class='form-control-static col-lg-2 col-md-2'>" +keys[i]+"</label> \n"+
            "            <label class=\"control-label col-lg-2 col-md-2\">所在层级:</label>\n" +
            "            <div class=\"col-lg-3 col-md-3 col-sm-3\">\n" +
            "                <select class=\"level_num form-control\">\n" +
            "\n" +
            "                </select>\n" +
            "            </div>\n" +
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
        "        <button class=\"btn btn-success\" id=\"submitCreateForm\">确认提交</button>");
    //添加按钮事件
    $("#backPre").click(function () {
        $("#uploadCorpusForm").toggle('100');
        $("#createForm").toggle("100");
        return false;
    });
    //提交事件
    $("#submitCreateForm").click('submit',function (event) {
        var $keys=$("#createForm").find("label.form-control-static");//获取键值元素
        var keyIdx=0;
        var arrKeys=[];
        $keys.each(function () {
            arrKeys[keyIdx]=$(this).text();
            keyIdx++;
        })
        var levelIdx=0;
        var arrLevels=[]
        var $level=$("#createForm").find("select");//获取键值对应的层级元素;
        $level.each(function () {
            arrLevels[levelIdx]=$(this).val();
            levelIdx++;
        })
        //获取语料库名称
        var corpusName=$("#corpusName").val();
        var corpusPre=$("#corpusPre").val();
        //获取每一级名
        var levelNames=[]
        for(var i=1;i<levelNum;i++)
        {
            levelNames[i-1]=$("#level_"+(i+1)).val();
        }
        $.post(
            $this.action,
            {keys:arrKeys,levels:arrLevels,corpusName:corpusName,corpusPre:corpusPre,levelNames:levelNames},
            function (data) {
                console.log(data);
            },"json"
        );
        return false;
    })
}