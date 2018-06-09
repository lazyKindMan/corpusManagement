<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/6/3
 * Time: 15:54
 */

namespace app\controllers;

use yii;
use app\models\search\searchModel;
use yii\web\Controller;

class SearchController extends Controller
{
    public $layout = "searchLayout";

    public function actionIndex()
    {
        return $this->render("index");
    }
    public function actionValidate()
    {
        $pageSize=12;
        $offSet=yii::$app->request->get('offSet')?(int)yii::$app->request->get('offSet'):0;
        $condition=yii::$app->request->get('condition')?yii::$app->request->get('condition'):"";
        $model=new searchModel(['pageSize'=>$pageSize,'offSet'=>$offSet,'condition'=>$condition]);
        try
        {
            $model->getRes();
            return json_encode(['code'=>1,'data'=>$model->searchResult]);
        }catch (\Exception $e)
        {
            return json_encode(['code'=>0,'message'=>$e->getMessage()]);
        }
    }
    public function actionSearch()
    {
        $pageSize=12;
        $offSet=yii::$app->request->get('offSet')?(int)yii::$app->request->get('offSet'):0;
        $condition=yii::$app->request->get('condition')?yii::$app->request->get('condition'):"";
        $model=new searchModel(['pageSize'=>$pageSize,'offSet'=>$offSet,'condition'=>$condition]);
        try
        {
            $model->getRes();
            $dataProvider=new yii\data\ArrayDataProvider([
                'allModels'=>$model->searchResult,
                'pagination'=>[
                    'pageSize'=>12,
                ]
            ]);
            return $this->render("search",['code'=>1,'dataProvider'=>$dataProvider]);
        }catch (\Exception $e)
        {
            return $this->render("search",['code'=>0,'message'=>$e->getMessage()]);
        }
    }
    public function actionGetContent()
    {
        $corpus_id=yii::$app->request->get('corpus_id');
        try{
            $model=new searchModel(['condition'=>""]);
            return json_encode(['code'=>1,'message'=>$model->getContentById($corpus_id)]);
        }catch (\Exception $e)
        {
            return json_encode(['code'=>0,'message'=>$e->getMessage()]);
        }
    }

    public function actionTest()
    {
        $model=new searchModel(['condition'=>'江泽民&!胡','offSet'=>0,'pageSize'=>12]);
        try
        {
            $model->getRes();
            var_dump($model->searchResult);
        }catch (\Exception $e)
        {
            echo $e->getMessage().$e->getLine();
        }
    }
}