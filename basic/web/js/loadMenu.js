$(document).ready(function () {
   //根据管理员权限动态生成菜单
    $.post(
        "get-authority.html",
        {},
        function (data) {
            if(data['code']==1)//全部菜单开放
            {
                $("#menu").append("<a href=\"#corpusManage\" class=\"list-group-item\" data-toggle=\"tab\">"+data['authorities'][0]['authority_name']+"</a>\n" +
                    "                        <a href=\"#corpusSubmit\" class=\"list-group-item\" data-toggle=\"tab\">"+data['authorities'][1]['authority_name']+"</a>\n" +
                    "                        <a href=\"#userManage\" class=\"list-group-item\" data-toggle=\"tab\">"+data['authorities'][2]['authority_name']+"</a>\n" +
                    "                        <a href=\"#corpusCheck\" class=\"list-group-item\" data-toggle=\"tab\">"+data['authorities'][3]['authority_name']+"</a>"+
                    "<a href=\"#cralwerMange\" class=\"list-group-item\" data-toggle=\"tab\">"+data['authorities'][4]['authority_name']+"</a>");
            }
            if(data['code']==2)
            {
                for(var i=0;i<data['userAuthorities'].length;i++)
                {
                    var res=getNameAndHerf(data['authorities'],data['userAuthorities'][i]);
                    $("#menu").append("<a href=\""+res[0]+"\" class=\"list-group-item\" data-toggle=\"tab\">"+res[1]+"</a>\n");
                }
            }
        },"json"
    );
    //加载用户基本信息
    $.post(
        "get-user-message.html",
        {},
        //返回的数据是一个json对象
        function (data) {
            //添加内容到页面中
            console.log(data);
            $("#userMessage .userName").text(data.username);
            $("#userMessage .level").text(data.level_name);
            $("#WorkPlace").val(data.workpalce);
            $("#realName").val(data.realname);
            $("input[name=sex]").get(data.sex).checked=true;
            $("#inputEmail").val(data.email);
        },"json"
    );
});
function getNameAndHerf(authorities,id) {
    //获取authority_name
    var i=0;
    console.log(authorities);
    for(var j=0;j<authorities.length;j++)
    {
        console.log(authorities[j]['authority_id']);
        console.log(id);
        if(authorities[j]['authority_id']==id)
        {
            break;
        }
        i++;
    }
    switch (authorities[i]['authority_name'])
    {
        case "语料库管理":return ['#corpusManage',authorities[i]['authority_name']];break;
        case "语料提交"  :return ['#corpusSubmit',authorities[i]['authority_name']];break;
        case "用户管理"  :return ['#userManage',authorities[i]['authority_name']];break;
        case "语料审核"  :return ['#corpusCheck',authorities[i]['authority_name']];break;
        case "爬虫管理"  :return ['#crawlerManage',authorities[i]['authority_name']];break;
    }
}