<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/9
 * Time: 13:38
 */

namespace app\controllers;

use app\Curl;
use yii\web\Controller;

class CrawlerController extends Controller
{
    public $layout=null;
    public  function actionDoCrawler()
    {
        $crulObj=new Curl();
        $crulObj->do_crul(['url'=>'http://lib.hsesystem.com/icd-10.do/A00.000'],1,1000);
//        $crulObj->test(['url'=>'http://lib.hsesystem.com/icd-10.do/A00.000']);
    }
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionInfo()
    {
        return $this->render('info');
    }
}

//doajax({sdo:6,lvl:2,cid:$cid},"GET",window.$url,"html",s2_post,s2_load);
//function doajax(dataArr,doType,doUrl,dataType,loadfun,sucfun)