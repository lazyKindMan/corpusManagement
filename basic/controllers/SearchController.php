<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/3
 * Time: 15:54
 */

namespace app\controllers;


use yii\web\Controller;

class SearchController extends Controller
{
    public $layout="searchLayout";
    public function actionIndex()
    {
        return $this->render("index");
    }
}