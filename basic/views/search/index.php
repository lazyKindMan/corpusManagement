<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/3
 * Time: 15:59
 */
$this->registerCssFile("@web/css/default.css",["depends"=>["app\assets\AppAsset"]]);
$this->registerCssFile("@web/css/normalize.css",["depends"=>["app\assets\AppAsset"]]);
$this->registerCssFile("@web/css/search-form.css",["depends"=>["app\assets\AppAsset"]]);
?>
<header class="htmleaf-header">
    <h1>语料检索系统<span>点击下面按钮进行检索</span></h1>
    <div class="htmleaf-links">
        <a class="htmleaf-icon icon-htmleaf-home-outline" href="http://localhost/" title="进入管理员登录界面" target="_blank"><span> 进入后台管理</span></a>
        <a class="htmleaf-icon icon-htmleaf-arrow-forward-outline" href="javascript:" title="查询检索规则" target="_blank"><span> 检索规则</span></a>
    </div>
</header>
<section class="htmleaf-container">
    <form onsubmit="submitFn(this, event);">
        <div class="search-wrapper">
            <div class="input-holder">
                <input type="text" class="search-input" placeholder="输入关键词进行检索" />
                <button class="search-icon" onclick="searchToggle(this, event);"><span></span></button>
            </div>
            <span class="close" onclick="searchToggle(this, event);"></span>
            <div class="result-container">

            </div>
        </div>
    </form>
</section>

<script type="text/javascript">
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
        value = $(obj).find('.search-input').val().trim();

        _html = "Yup yup! Your search text sounds like this: ";
        if(!value.length){
            _html = "Yup yup! Add some text friend :D";
        }
        else{
            _html += "<b>" + value + "</b>";
        }

        $(obj).find('.result-container').html('<span>' + _html + '</span>');
        $(obj).find('.result-container').fadeIn(100);

        evt.preventDefault();
    }
</script>
