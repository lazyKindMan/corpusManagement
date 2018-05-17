function loadTab(obj) {
   var menu=$(obj).attr('href')
    switch (menu)
    {
        case '#corpusManage':loadCorpusManage($(obj).text());
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