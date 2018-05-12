function addLevelEvent(){
    //为修改用户级别添加时间
    $("#user-table").find(".user-level").change(function () {
        var changeVal=$(this).children("option:selected").text();
        if(confirm("确定要将此用户级别改成"+changeVal))
        {
            
        }
        else
        {

        }
    });
}
function getElement(level){
    switch (level)
    {
        case '1': {
            console.log(1);
            var res =
                "<select class='form-control user-level'>" +
                "<option value=1 selected>超级管理员</option>" +
                "<option value=2>普通管理员</option>" +
                "<option value=3>语料库管理员</option>" +
                "<option value=4>普通用户</option>" +
                "</select>";
            return res;
            break;
        }
        case '2': {
            var res = "<select class='form-control user-level'>" +
                "<option value=1>超级管理员</option>" +
                "<option selected value=2>普通管理员</option>" +
                "<option value=3>语料库管理员</option>" +
                "<option value=4>普通用户</option>" +
                "</select>";
            return res;
            break;
        }
        case '3': {
            var res = "<select class='form-control user-level'>" +
                "<option value=1>超级管理员</option>" +
                "<option value=2>普通管理员</option>" +
                "<option selected value=3>语料库管理员</option>" +
                "<option value=4>普通用户</option>" +
                "</select>";
            return res;
            break;
        }
        case '4':
        {
            console.log(4);
            var res = "<select class='form-control user-level'>" +
                "<option value=1>超级管理员</option>" +
                "<option value=2>普通管理员</option>" +
                "<option value=3>语料库管理员</option>" +
                "<option selected value=4>普通用户</option>" +
                "</select>";
            return res
            break;
        }
    }
}

$(document).ready(function () {
    $.post(
        "../php/admin/getUser.php",
        {},
        function (data) {
            var element="";
            for(var i=0;i<data.length;i++)
            {
                element+="<tr>"+
                    "<td class='id'>"+data[i]['id']+"</td>"+
                    "<td class='username'>"+data[i]['username']+"</td>"+
                    "<td>"+getElement(data[i]['level_id'])+"</td>"+
                "<td><button class=\"btn btn-default change-right\">更改权限</button></td>\n" +
                    "                            <td><button class=\"btn btn-info change-password\">修改密码</button></td>\n" +
                    "                            <td><button class=\"btn btn-danger count-manage\">封禁管理</button></td></tr>";

            }
            // language=JQuery-CSS
            $("#tbMain").html(element);
            addLevelEvent();
        },"json"
    );

});