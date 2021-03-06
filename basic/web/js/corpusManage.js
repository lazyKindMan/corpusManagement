$(document).ready(function () {

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
        $("#backPre").attr('disabled',"true");
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
        $.post(
            "create-dic-corpus.html",
            {corpusName:corpusName,corpusPre:corpusPre,levelNames:levelNames,levelKey:levelKey},
            function (data) {
                console.log(levelKey);
                if(data==1)
                {
                    alert('添加成功，进入审核');
                    location.reload();
                }
                else
                {
                    alert(data);
                    $("#CreateButton").removeAttr("disabled");
                    $("#backPre").removeAttr("disabled");
                    $('#warning1').text('');
                }
                //重新加载管理界面
            },"json"
        );
        return false;
    })
}
function textCorporaManage() {
    $("#dictionary_manage").toggle();
    $("#text_manage").toggle();
    $.get(
        "show-text-corpora.html",
        {},
        function (data) {
            if(data['code']==1)
            {
                addConetent(data['dataArr']);
                createTextCorporaPage(data['allSum'],data['pageSize']);
            }
            if(data['code']==0)
            {
                alert(data['message']);
            }
        },"json"
    );
}
function addConetent(datas) {
    var $tableBody=$("#text_manage").find("#textCorporaTable").find("tbody");
    $tableBody.html("");
    if(datas.length>0)
    {
        datas.forEach(function (item) {
            var content="<tr data-corpus_id=:corpus_id>\n" +
                "                <td>:corpus_name</td>\n" +
                "                <td>:created_at</td>\n" +
                "                <td>:resource</td>\n" +
                "                <td>:title</td>\n" +
                "                <td>:word_count</td>"+
                "                <td>:word_kind_count</td>"+
                "                <td>:level_name</td>\n" +
                "                <td>"+
                "                    <button class=\"btn btn-success\" onclick=\"showTextCorpusDetail(this,:corpus_id,1)\">语料详情</button>\n" +
                "                    <button class=\"btn btn-danger\" onclick=\"deleteTextCorpus(this,:corpus_id)\">删除语料</button>\n" +
                "                </td>\n" +
                "            </tr>";
            content=content.format(item);
            $tableBody.append(content);
            if(item['is_checking']=='1')
            {
                $("#textCorporaTable").find("[data-corpus_id='"+item['corpus_id']+"']").find(".btn-danger").attr('disabled','true');
            }
        });
    }
    else
    {
        $tableBody.append("<h3>目前系统没有改类语料</h3>");
    }
}
//显示语料报表事件
function showTextCorpusDetail(obj,coprus_id,from) {
    $("#corpusDetail").toggle();
    if(from==1)
    {
        $("#text_manage").toggle();
        $("#textOpenLevel").removeAttr("disabled");
        $("#corpusDetail").find(".btnList").html("");
        $("#corpusDetail").find(".btnList").append("<button class=\"btn btn-primary\" onclick=\"backList(1)\">返回列表</button>");
    }
    else if(from==2)
    {
        $("#corpusCheck").toggle();
        $("#textOpenLevel").attr("disabled","true");
        $("#corpusDetail").find(".btnList").html("");
        $("#corpusDetail").find(".btnList").append("<button class=\"btn btn-primary\" onclick=\"backList(2)\">返回列表</button>");
    }
    $.get(
        "corpus-report.html",
        {'corpus_id':coprus_id},
        function (data) {
            console.log(data);
            if(data['code']==1)
            {
                var showData=[];
                var i=0;
                var count=0;
                for(var key in data['report'])
                {
                    showData.push([key,parseInt(data['report'][key])]);
                    count+=parseInt(data['report'][key]);
                    i++;
                    if(i>=5)
                        break;
                }
                showData.push(['其他词',parseInt(data['corpusData']['word_count'])-count]);
                $("#chartdiv").html("");
                $.jqplot('chartdiv', [showData], {
                    title:"前五词频统计分布图",
                    seriesDefaults: {
                        renderer: $.jqplot.PieRenderer,
                        rendererOptions: {
                            showDataLabels: true,
                            lineWidth:5,
                            shadowDepth: 5,     // 设置阴影区域的深度
                            shadowAlpha: 0.07   // 设置阴影区域的透明度
                        }
                    },
                    legend: {
                        show: true,
                        location: "e"
                    },
                    cursor: {
                        style: 'crosshair', //当鼠标移动到图片上时，鼠标的显示样式，该属性值为css类
                        show: true, //是否显示光标
                        showTooltip: true, // 是否显示提示信息栏
                        followMouse: false, //光标的提示信息栏是否随光标（鼠标）一起移动
                        }
                });
                $("#textCorpusName").text(data['corpusData']['corpus_name']);
                $("#textCorpusSource").text(data['corpusData']['resource']);
                $("#textWordCount").text(data['corpusData']['word_count']);
                $("#textWordKind").text(data['corpusData']['word_kind_count']);
                $("#textCreatedAt").text(data['corpusData']['created_at']);
                $("#textOpenLevel").val(data['corpusData']['open_level']);
                $("#textContent").val(data['corpusData']['content']);
                if(data['corpusData']['is_checking']==0)
                    $("#textCheckStatus").text("正常");
                else $("#textCheckStatus").text("正在审核");
            }
            if(data['code']==0)
            {
                alert(data['message']);
            }
        },"json"
    );
}
function deleteTextCorpus(obj,corpus_id) {
    $.get(
        "delete-text-corpus.html",
        {'corpus_id':corpus_id},
        function (data) {
            alert(data['message'])
            if(data['code']==1)
            {
                $(obj).attr('disabled','true');
            }
        },"json"
    );
}
function createTextCorporaPage(sum,pageSize,currentPage=1) {
    var $addElement=$("#text_manage").find("#currentPage");
    if(sum%pageSize!=0)
    {
        var pages=parseInt(sum/pageSize+1);
    }
    else
    {
        var pages=parseInt(sum/pageSize);
        if(pages==0) pages++;
    }
    //创建缩略版翻页
    $addElement.text("");
    $addElement.text(currentPage+"/"+pages);
    $("#text_manage").find("ul li").removeClass("disabled");
    if(currentPage==pages)
    {
        $("#text_manage").find("ul li:last").prev().addClass("disabled");
    }
    if(currentPage==1)
    {
        $("#text_manage").find("ul li:first").next().addClass("disabled");
    }
}
function jumpPage(obj) {
    var jumpPage=parseInt($("#jumpPage").val());
    var pages=parseInt($("#currentPage").text().split("/")[1]);
    if(jumpPage>pages)
        alert("请输入不大于"+pages+"的页数");
    if(!jumpPage)
        alert("请输入跳转页数");
    else {
        getPageData(jumpPage);
    }
}
function clickJumpPage(obj,flag) {
    var currentPage=parseInt($("#currentPage").text().split("/")[0]);
    var pages=parseInt($("#currentPage").text().split("/")[1]);
    switch (flag)
    {
        case 1:getPageData(1);break;
        case 2:{
            if(currentPage==1)
                return false;
            getPageData(currentPage-1);
        break;}
        case 3:{
            if(currentPage==pages)
                return false;
            getPageData(currentPage+1);
            break;
        }
        case 4:getPageData(pages);break;
    }
}
function getPageData(jumpPage) {
    $.get(
        "show-text-corpora.html",
        {'offSet':jumpPage},
        function (data) {
            console.log(data);
            if(data['code']==1)
            {
                addConetent(data['dataArr']);
                createTextCorporaPage(data['allSum'],data['pageSize'],jumpPage);
            }
            if(data['code']==0)
            {
                alert(data['message']);
            }
        },"json"
    )
}
function backDictionaryManage() {
    $("#dictionary_manage").toggle();
    $("#text_manage").toggle();
}
function backList(to) {
    $("#corpusDetail").toggle();
    if(to==1) {
        $("#text_manage").toggle();
    }
    else $("#corpusCheck").toggle();
}
//提交语料信息
function addTextCorpus() {
    var $inputs=$("#addTextCorpusForm").find("input");
    var dataArr={};
    $inputs.each(function () {
        dataArr[$(this).attr("name")]=$(this).val();
    });
    dataArr[$("#addTextCorpusForm").find("textarea").attr("name")]=$("#addTextCorpusForm").find("textarea").val();
    var currentPage=parseInt($("#currentPage").text().split("/")[1]);
    $.ajax({
        url:"add-text-corpus.html",
        type:"POST",
        dataType:'json',
        data:dataArr,
        cache:false,
        success:function (data) {
            alert(data['message']);
            $.get(
                "show-text-corpora.html",
                {'offSet':currentPage},
                function (data) {
                    if(data['code']==1)
                    {
                        addConetent(data['dataArr']);
                        createTextCorporaPage(data['allSum'],data['pageSize'],currentPage);
                        $("#addTextCorpusModal").modal("hide");
                    }
                    if(data['code']==0)
                    {
                        alert(data['message']);
                    }
                },"json"
            )
        }
    });
}
function deleteDictionaryCorpus(obj,corpus_id) {
    $.get(
        "delete-dictionary-corpus.html",
        {'corpus_id':corpus_id},
        function (data) {
            if(data['code']==1)
                $(obj).attr("disabled","true");
            alert(data['message']);
        },"json"
    );
}