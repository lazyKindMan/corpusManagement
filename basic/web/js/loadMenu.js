$(document).ready(function () {
   //根据管理员权限动态生成菜单
    $.post(
        "get-authority.html",
        {},
        function (data) {
            if(data['code']==1)//全部菜单开放
            {
                $("#menu").append("<a href=\"#corpusManage\" class=\"list-group-item\" data-toggle=\"tab\" onclick=\"loadTab(this)\">"+data['authorities'][0]['authority_name']+"</a>\n" +
                    "<a href=\"#userManage\" class=\"list-group-item\" data-toggle=\"tab\">"+data['authorities'][1]['authority_name']+"</a>\n"+
                "<a href='#corpusCheck' class=\"list-group-item\" onclick=\"loadTab(this)\" data-toggle=\"tab\">"+data['authorities'][2]['authority_name']+"</a>");
            }
            if(data['code']==2)
            {
                for(var i=0;i<data['userAuthorities'].length;i++)
                {
                    var res=getNameAndHerf(data['authorities'],data['userAuthorities'][i]);
                    $("#menu").append("<a href=\""+res[0]+"\" class=\"list-group-item\" data-toggle=\"tab\" onclick=\"loadTab(this)\">"+res[1]+"</a>\n");
                }
            }
            changeTab();
        },"json"
    );
    //加载用户基本信息
    loadMeesage();
    //重置信息
    $("#reset").click(function () {
        var r=confirm("确认重置本信息页？");
        if(r==true)
            loadMeesage();
    });
    //前端验证
    validate();
    //提交修改
    $("#changeUserMessage").click(function () {
        // console.log($("input[name='sex']:checked").val());
            $.post(
                "update-message.html",
                {
                    'email':$("#inputEmail").val(),
                    'realname':$("#realName").val(),
                    'sex':$("input[name='sex']:checked").val(),
                    'workplace':$("#WorkPlace").val(),
                    'level_name':$("#userMessage .level").text()
                },
                function (data) {
                    if(data==1)
                    {
                        alert("修改成功")
                        window.location.reload();
                    }
                    else
                    {
                        if(data==2)
                        {
                            alert("邮箱重复");
                            $("#inputEmail").focus();
                        }
                        if(data==3)
                        {
                            alert("数据验证失败，请检查数据是否符合规范")
                        }
                    }
                }
            );
    });
});
function getNameAndHerf(authorities,id) {
    //获取authority_name
    var i=0;
    for(var j=0;j<authorities.length;j++)
    {
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
function loadMeesage() {
    $.post(
        "get-user-message.html",
        {},
        //返回的数据是一个json对象
        function (data) {
            //添加内容到页面中
            $("#userMessage .userName").text(data.username);
            $("#userMessage .level").text(data.level_name);
            $("#WorkPlace").val(data.workplace);
            $("#realName").val(data.realname);
            $("input[name=sex]").get(data.sex).checked=true;
            $("#inputEmail").val(data.email);
        },"json"
    );
}
function validate() {
    $("#userMessage").bootstrapValidator({
        message:'This value is not valid',
        feedbackIcons:{
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields:{
            workPlace:{
                validators:{
                    stringLength:{
                        max:30,
                        message:"不能超过30个字符"
                    },
                }
            },
            email:{
                validators:{
                    notEmpty:
                        {
                            message:'邮箱不能为空'
                        },
                    emailAddress:
                        {
                            message:'邮箱格式有误'
                        },
                    stringLength:{
                        max:40,
                        min:1,
                        message:"邮箱不能为空且不能超过40个字符"
                    }
                }
            },
            realname:
                {
                    validators:{
                        stringLength:{
                            max:10,
                            message:"名字不能超过10个字符"
                        }
                    }
                }
        }
    });

}
//菜单切换
function changeTab() {
    $("#menu a").click(function (e) {
        e.preventDefault();
        var href=$(this).attr('href');
        $("#myTab .tab").fadeOut(100);
        $("#menu a").removeClass('active');
        $(href).fadeIn(100);
        $(this).addClass("active");
    });
}