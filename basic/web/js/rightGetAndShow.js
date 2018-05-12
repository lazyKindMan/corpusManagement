$(document).ready(function () {
    $("input[name='selection[]']").addClass("_check");
    //选中改变颜色
    $("._check").click(function(){
        var userId=$(this).val();
        if($("#tr-"+userId).hasClass("select_bg")){
            $("#tr-"+userId).removeClass("select_bg");
        }else{
            //$("#tr-"+id).css("background-color",'red');
            $("#tr-"+userId).addClass("select_bg");
        }
    });
    //绑定按钮，获取信息
    var id;
    $(".updateRight").click(function () {
        //获取id
        id=$(this).parent().prevAll(".userId").text();
        $.post(
            "get-authority.html",
            {'id':id},
            function (data) {
                if(data['code']==1)
                {
                    $("#updateRight-modal .modal-body").empty();
                    //超级管理员权限，所有复选框选中并不能更改
                    var content='';
                    for(var i=0;i<data['authorities'].length;i++)
                    {
                        content+="<div class=\"checkbox\">"+
                            "<label><input type=\"checkbox\" value='"+data['authorities'][i]['authority_id']+
                            "' disabled checked>"+data['authorities'][i]['authority_name']+"</label>";
                    }
                    content+="<p></p>";
                    content+="<div class='row'><div class='col-lg-4 col-sm-4 col-md-4 col-lg-offset-5 col-md-offset-5 col-sn-offset-5'><button class=\"btn btn-danger\" id=\"updateRightsCancel\" data-dismiss=\"modal\">取消</button></div></div>";
                    $("#updateRight-modal .modal-body").html(content);
                    addUpdateRightsCancel();
                }
                else if(data['code']==2)
                {
                    $("#updateRight-modal .modal-body").empty();
                    console.log(data);
                    var content='';
                    for(var i=0;i<data['authorities'].length;i++)
                    {
                        if(data['userAuthorities'].indexOf(data['authorities'][i]['authority_id'])!=-1)
                        {
                            content+="<div class=\"checkbox\">"+
                                "<label><input type=\"checkbox\" value='"+data['authorities'][i]['authority_id']+
                                "'checked>"+data['authorities'][i]['authority_name']+"</label>";
                        }
                        else
                        {
                            content+="<div class=\"checkbox\">"+
                                "<label><input type=\"checkbox\" value='"+data['authorities'][i]['authority_id']+
                                "'>"+data['authorities'][i]['authority_name']+"</label>";
                        }
                    }
                    content+="<p></p>";
                    content+="<div class='row'><div class='col-lg-2 col-sm-2 col-md-2 col-lg-offset-2 col-md-offset-2'><button class=\"btn btn-primary\" id=\"updateRightsOk\">修改</button></div>";
                    content+="<div class='col-lg-2 col-sm-2 col-md-5 col-lg-offset-5 col-md-offset-5 col-sn-offset-5'><button class=\"btn btn-danger\" id=\"updateRightsCancel\" data-dismiss=\"modal\">取消</button></div></div>";
                    $("#updateRight-modal .modal-body").html(content);
                    addUpdateRightsCancel();
                }
                else if(data['code']==3)
                {
                    $("#updateRight-modal .modal-body").empty();
                    //超级管理员权限，所有复选框选中并不能更改
                    var content='';
                    for(var i=0;i<data['authorities'].length;i++)
                    {
                        content+="<div class=\"checkbox\">"+
                            "<label><input type=\"checkbox\" value='"+data['authorities'][i]['authority_id']+
                            "' disabled>"+data['authorities'][i]['authority_name']+"</label>";
                    }
                    content+="<p></p>";
                    content+="<div class='row'><div class='col-lg-4 col-sm-4 col-md-4 col-lg-offset-5 col-md-offset-5 col-sn-offset-5'><button class=\"btn btn-danger\" id=\"updateRightsCancel\" data-dismiss=\"modal\">取消</button></div></div>";
                    $("#updateRight-modal .modal-body").html(content);
                    addUpdateRightsCancel();
                }
            },"json"
        )
    });
    $(".updatePassword").click(function () {
        //获取id
        id=$(this).parent().prevAll(".userId").text();
        $('#password').val('');
        $('#repetePassword').val('');
    });
    $(".updateAccount").click(function () {
        id=$(this).parent().prevAll(".userId").text();
        $.post(
            "get-status.html",
            {'id':id},
            function (data) {
                $('#userStatus').val(data);
            },"json"
        );
    });
    $("#updatePasswordOk").click(function () {
       //获取密码和重复密码
       var password=$('#password').val();
       var repeatPassword=$('#repetePassword').val();
       if(password!=repeatPassword)
           alert("请输入两次一样的密码");
       else $.post(
           "update-password.html",
           {'id':id,'password':password},
           function (data) {
               if(data==1)
               {
                   alert("修改成功");
                   $('#password').val('');
                   $('#repetePassword').val('');
               }
               else
               {
                   alert("请输入长度大于6位且18位的密码");
               }
           },"json"
       );
    });
    $('#updateCanlogin').click(function () {
        $.post(
            "update-status.html",
            {'id':id,'status':$('#userStatus').val()},
            function (data) {
                if(data==1)
                    alert("修改成功");
                else alert("修改失败");
            },"json");
    });
    //增加搜索功能
    $('#searchUser').click(function () {
       //获取数据
        var level_id=$(this).parents('#searchItem').find('.dropdownlist').val()=='0'?'':$(this).parents('#searchItem').find('.dropdownlist').val();
        var username=$(this).parents('#searchItem').find("#usernameSearch").val();
        $.post(
            "index.html",
            {'level_id':level_id,'username':username},
            function (data) {
                location.reload();
            }
        );
    });
    }
);
//更改权限
function addUpdateRight() {

}
function addUpdateRightsCancel() {
    $("#UpdateRightsCancel").modal('hide');
}
