function show_detail(obj,id) {
   $.post(
       "get-detail.html",
       {'id':id},
       function (data) {
           if(data=="0")
               alert("出现未知错误");
           $(".modal-title").html("");
           $(".modal-title").html("用户详情");
           $("#PublicModal .modal-body").html("");
           $("#PublicModal .modal-body").html(data);
       }
   );
}
function show_updateModal(id) {
    $.post(
        "update-modal.html",
        {'id':id},
        function (data) {
            if(data=="0")
                alert("出现未知错误");
            $(".modal-title").html("");
            $(".modal-title").html("修改信息");
            $("#PublicModal .modal-body").html("");
            $("#PublicModal .modal-body").html(data);
        }
    );
}