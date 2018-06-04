function loadTab(obj) {
   var menu=$(obj).attr('href');
    switch (menu)
    {
        case '#corpusManage':loadCorpusManage($(obj).text());
        case '#corpusCheck':loadCheckManage($(obj).text());
    }
}
function loadCorpusManage(authority_name) {
    $.get(
        "corpus-manage.html",
        {'authority_name':authority_name},
        function (data) {
            $("#corpusManage").html("");
            $("#corpusManage").html(data);
        });
}
function loadCheckManage(authority_name) {
    $.get(
        "corpus-check.html",
        {'authority_name':authority_name,'currentPage':1},
        function (data) {
            if(data['code']==1){
                createCheckTable(data['dictionary'],'dictionary');
                createCheckTable(data['text'],'text');
                createPage(data['dictionaryCount'],'dictionary',data['pageSize']);
                createPage(data['textCount'],'text',data['pageSize']);
            }
            else alert(data['message']);
        },"json");
}
function createCheckTable(datas,kind) {
    if(kind=='dictionary')
    {
        var $tableBody=$("#dictionaryCheckTable").find("tbody");
        buttonE="<button class=\"showDetail btn btn-primary\" onclick='showDictionaryDetail(:corpus_id,2)'>查看详情</button>";
    }
    else if(kind=='text')
    {
        var $tableBody=$("#textCheckTable").find("tbody");
        buttonE="<button class=\"showDetail btn btn-primary\" onclick=\"showTextCorpusDetail(this,:corpus_id,2)\">查看详情</button>";
    }
    $tableBody.html("");
    if(datas.length>0)
    {
        datas.forEach(function (item) {
            if(item['op']==0)
                item['OpName']="添加审核";
            if(item['op']==1)
                item['OpName']="删除审核";
            var data={
                corpus_id:item['corpus_id']
            };
            var content="<tr data-corpus_id=:corpus_id>"+
                "                                <td>:corpus_name</td>" +
                "                                <td>:created_at</td>" +
                "                                <td>:updated_at</td>" +
                "                                <td>:OpName</td>" +
                "                                <td>" +buttonE+
                "                                    <button class=\"passCheck btn btn-success\" onclick='check(this,:corpus_id,:kind,1)'>审核通过</button>" +
                "                                <button class=\"unpassCheck btn btn-danger\" onclick='check(this,:corpus_id,:kind,0)'>审核不通过</button>" +
                "                                </td>" +
                "                            </tr>";
            var content=content.format(item);
            $tableBody.append(content);
        })
    }
    else
    {
        $tableBody.append("<h3>目前没有审核任务</h3>");
    }
}
String.prototype.format = function() {
    if(arguments.length == 0) return this;
    var obj = arguments[0];
    var s = this;
    for(var key in obj) {
        var patt1=new RegExp(":"+key,"g");
        s=s.replace(patt1,obj[key]);
    }
    return s;
}
function createPage(sum,kind,pageSize) {
    if(kind=='dictionary')
    {
        $("#dictionaryCheck").find("ul").html("");
       var $firstPage=$("#dictionaryCheck").find("ul");
       if(sum%pageSize!=0)
       {
           var pages=parseInt(sum/pageSize+1);
       }
       else
       {
           var pages=parseInt(sum/pageSize);
           if(pages==0) pages++;
       }
        for(var i=1;i<=pages;i++)
            $firstPage.append("<li><a href='javascript:void(0)' onclick=\"changePage(this,"+i+",'dictionary',"+pages+")\">"+i+"</a></li>");
    }
    else if(kind=='text')
    {
        $("#textCheck").find("ul").html("");
       var $firstPage=$("#textCheck").find("ul");
        var pages=parseInt(sum/pageSize+1);
        for(var i=1;i<=pages;i++)
            $firstPage.append("<li><a href='javascript:void(0)' onclick='changePage(this,"+i+",'text',"+pages+")>"+i+"</a></li>");
    }
    //把前面翻页禁止
    $firstPage.find("li:first").addClass("active");
}
function changePage(obj,page,kind,lastPage) {
    if(kind='dictionary')
    {
        $("#dictionaryCheck").find("ul li").removeClass("active");
        $(obj).parent().addClass("active");
        $.get(
            "corpus-check.html",
            {'authority_name':"语料审核",'currentPage':page},
            function (data) {
                if(data['code']==1)
                {
                    createCheckTable(data['dictionary'],'dictionary');
                }
                else
                    alert(data['message']);
            },"json"
        );
    }
    return false;
}
//审核通过或不通过操作
function check(obj,coprus_id,kind,op) {
    if(op==1)
    {
        if(confirm("你确定批准该操作审核?"))
        {
            $.post(
                "pass-check.html",
                {'corpus_id':coprus_id,'kind':kind},
                function (data) {
                    if(data['code']==1)
                    {
                        alert(data['message']);
                        $(obj).attr("disabled",'true');
                        $(obj).next().attr("disabled",'true');
                    }
                    else
                        alert(data['message']);
                },"json"
            )
        }
    }
    else
    {
        if(confirm("你确定不批准该操作?"))
        {
            $.post(
                "unpass-check.html",
                {'corpus_id':coprus_id,'kind':kind},
                function (data) {
                    if(data['code']==1)
                    {
                        alert(data['message']);
                        $(obj).attr("disabled",'true');
                        $(obj).prev().attr("disabled",'true');
                    }
                    else
                        alert(data['message']);
                },"json"
            )
        }
    }
}
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
function showDictionaryDetail(corpus_id,from) {
    $.get(
        "show-dictionary-report.html",
        {'corpus_id':corpus_id},
        function (data) {
            if(data['code']==1)
            {
                console.log(data);
                if(from==1)
                {
                    $("#corpusManage").toggle();
                }
                else
                {
                    $("#corpusCheck").toggle();
                }
                $("#dictionaryCorpusDatail").toggle();
                addDetialContent(data['report']['corpus'],from);
                createDictionaryChart(data['report']['level']);
            }
            if(data['code']==0)
            {
                alert(data['message']);
            }
        },"json"
    )
}
function addDetialContent(datas,from) {
    $("#dictionaryCorpusDatail").html("");
    if(datas['is_checking']==1)
    {
        datas['check']="正在审核";
    }
    else datas['check']="正常状态";
    datas['from']=from;
    var content="<div class=\"col-md-6 col-lg-6 col-sm-6\">\n" +
        "                    <div>\n" +
        "                        <label style=\"font-size: large\">语料库名:</label>\n" +
        "                        <label style=\"font-size: large\" id=\"dictionaryCorpusName\">:corpus_name</label>\n" +
        "                    </div>\n" +
        "                    <div>\n" +
        "                        <label style=\"font-size: large\">分级数:</label>\n" +
        "                        <label style=\"font-size: large\">:all_level_count</label>\n"  +
        "</div><div>"+
        "                        <label style=\"font-size: large\">创建时间:</label>\n" +
        "                        <label style=\"font-size: large\">:created_at</label>\n" +
        "                    </div>\n" +
        "                    <div>\n" +
        "                        <label style=\"font-size: large\">审核状态:</label>\n" +
        "                        <label style=\"font-size: large\" id=\"textCheckStatus\">:check</label>\n" +
        "                    </div>"+
        "                    <div style=\"margin-top: 30px\">\n" +
        "                        <label style=\"font-size: large;padding-left: 0px\" class=\"col-md-3 col-lg-3 col-sm-3\">开放等级:</label>\n" +
        "                        <div class=\"col-md-6 col-lg-6 col-sm-6\">\n" +
        "                            <select id=\"dictioanryOpenLevel\" class=\"form-control\">\n" +
        "                                <option value=\"2\">管理员</option>\n" +
        "                                <option value=\"3\">普通用户</option>\n" +
        "                            </select>\n" +
        "                        </div>\n" +
        "                    </div>" +
        "</div>\n"+
        "<div class=\"col-md-6 col-lg-6 col-sm-6\">\n" +
        "                    <div id=\"dictionaryChart\" style=\"height:500px;width: 400px\" class=\"center-block\"></div>\n" +
        "                </div>"+
        "<div class=\"hr-div\"> <hr /></div>\n" +
        "                <div class=\"btnList\">\n" +
        "                    <button class=\"btn btn-primary\" onclick=\"dictionaryBack(:from)\">返回列表</button>\n" +
        "                </div>";
    content=content.format(datas);
    $("#dictionaryCorpusDatail").append(content);
    $("#dictionaryChart").html("");
    if(from!=1)
        $("#dictioanryOpenLevel").attr("disabled","true");
    $("#dictioanryOpenLevel").val(datas['open_level']);

}
function createDictionaryChart(datas) {
    $("#dictionaryChart").html("");
    var showData=[];
    for(var key in datas)
    {
        showData.push([datas[key]['levelname'],parseInt(datas[key]['levelCount'])]);
    }
    $.jqplot('dictionaryChart', [showData], {
        title:"词典语料词语数分布图",
        legend: { show: true, location: 'w' }, //提示工具栏--show：是否显示,location: 显示位置 (e:东,w:西,s:南,n:北,nw:西北,ne:东北,sw:西南,se:东南)
        series: [
            {
                label: '词数',
                // lineWidth: 8, //线条粗细
                markerOptions: { size: 9, style: "circle" }  // 节点配置
            }
        ], //提示工具栏
        seriesDefaults: {
            pointLabels: { show: true, ypadding: -1 }, //数据点标签
            renderer: $.jqplot.BarRenderer, //使用柱状图表示
            //柱状体组之间间隔
            rendererOptions: {barMargin: 25}
        },
        axes: {
            xaxis: {
                label: "分级种类",  //x轴显示标题
                pad: 5,
                renderer: $.jqplot.CategoryAxisRenderer, //x轴绘制方式
                tickOptions: {

                    fontSize: '10pt'
                },
                mark: 'cross'
            },
            yaxis: {
                label: "词数", // y轴显示标题
                min: 0,
                //tickInterval: 10,     //网格线间隔大小
            }
        }
    });
}
function dictionaryBack(to) {
    $("#dictionaryCorpusDatail").toggle();
    if(to==1) {
        $("#corpusManage").toggle();
    }
    else $("#corpusCheck").toggle();
}