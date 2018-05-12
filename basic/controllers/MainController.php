<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/4/26
 * Time: 15:04
 */

namespace app\controllers;


use yii\web\Controller;
class MainController extends Controller
{
    public $layout='mainLayout';
    public function actionIndex()
    {
        return $this->render("mainPage");
    }
}