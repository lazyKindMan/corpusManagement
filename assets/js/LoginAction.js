$(document).ready(
    function () {
         console.log(2);
  $("#login").click(
     function () {
         var username= $("#username").val();
         var password=$("#password").val();
         if (username==''||password=='')
             alert("请输入用户名密码")
         else $.post(
             "php/admin/adminLogin.php",
             {username:username,password:password},
             function (data) {
                 if(data==0)
                     alert("用户名或者密码错误")
                 else
                 window.location.href="adminPage/index.html"

             },"json"
         );
     }

);
 }
);