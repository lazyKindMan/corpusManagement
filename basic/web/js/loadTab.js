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
        var $tableBody=$("#dictionaryCheckTable").find("tbody");
    else if(kind=='text')
        var $tableBody=$("#textCheckTable").find("tbody");
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
                "                                <td>" +
                "                                    <button class=\"showDetail btn btn-primary\" onclick=\"showCorpusDetail(obj,:corpus_id)\">查看详情</button>" +
                "                                    <button class=\"passCheck btn btn-success\" onclick='check(obj,:corpus_id,:kind,1)'>审核通过</button>" +
                "                                <button class=\"unpassCheck btn btn-danger\" onclick='check(obj,:corpus_id,:kind,0)'>审核不通过</button>" +
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
    
}
