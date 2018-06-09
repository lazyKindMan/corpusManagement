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
$this->registerJsFile("https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js",["depends"=>["app\assets\AppAsset"]]);
?>
<div id="searchBar">
<header class="htmleaf-header">
    <h1>语料检索系统<span>点击下面按钮进行检索</span></h1>
    <div class="htmleaf-links">
        <a class="htmleaf-icon icon-htmleaf-home-outline" href="http://localhost/admin/login.html" title="进入管理员登录界面" target="_blank"><span> 进入后台管理</span></a>
        <a class="htmleaf-icon icon-htmleaf-arrow-forward-outline" href="javascript:" title="查询检索规则" target="_blank"><span> 检索规则</span></a>
    </div>
</header>
<section class="htmleaf-container">
    <form onsubmit="submitFn(this, event);" action="search.html" method="get">
        <div class="search-wrapper">
            <div class="input-holder">
                <input class="search-input" placeholder="输入关键词进行检索" name="condition"/>
                <button type="submit" class="search-icon" onclick="searchToggle(this, event);"><span></span></button>
            </div>
            <span class="close" onclick="searchToggle(this, event);"></span>
            <div class="result-container">

            </div>
        </div>
    </form>
</section>
</div>
<div class="modal fade" id="searchModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5>检索结果</h5></div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
</div>


