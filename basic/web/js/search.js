function searchToggle(obj, evt){
    var container = $(obj).closest('.search-wrapper');

    if(!container.hasClass('active')){
        container.addClass('active');
        evt.preventDefault();
    }
    else if(container.hasClass('active') && $(obj).closest('.input-holder').length == 0){
        container.removeClass('active');
        // clear input
        container.find('.search-input').val('');
        // clear and hide result container when we press close
        container.find('.result-container').fadeOut(100, function(){$(this).empty();});
    }
}

function submitFn(obj, evt){
    var value = $(obj).find('.search-input').val().trim();
    var flag=false;
    if(value.length<=0)
    {
        alert("请输入检索条件");
        evt.preventDefault();
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
function backTo() {
    window.location.href = "http://localhost";
}
function showContent(obj,corpus_id) {
    $.get(
        "get-content.html",
        {'corpus_id':corpus_id},
        function (data) {
            if(data['code']==1)
            {
                var content="<tr><td>"+data['message']+"</td></tr>";
                $(obj).parent().parent().after(content);
            }
            else
            {
                alert(data['message']);
            }
        },"json"
    )
}