$(document).ready(function() {
$("#register").find(".ok").click(function () {
    var username=$(this).parents().find(".username").val();
    var password=$(this).parents().find(".password").val();
    var email=$(this).parents().find(".email").val();
    if(username==""||password==""||email=="")
        alert("输入有误");
    else $.post(
        "php/register.php",
        {username:username,password:password,email:email},
        function (data){
            if(data==1)
            {
                $(this).parents().find(".username").val(" ");
                $(this).parents().find(".password").val(" ");
                $(this).parents().find(".email").val(" ");
                alert("注册成功");

            }
            else alert("用户名已经存在");
        },"json");

});
});