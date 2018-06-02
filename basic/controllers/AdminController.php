<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/7
 * Time: 14:49
 */

namespace app\controllers;
use app\models\Authority;
use app\models\check\CheckService;
use app\models\corpus\CorporaDictionary;
use app\models\corpus\CorporaDictionaryQuery;
use app\models\corpus\createDictionarnForm;
use app\models\corpus\DictionaryUploadFile;
use app\models\corpus\TextCorpora;
use app\models\corpus\TextCorporaSearch;
use app\models\corpus\TextCorpusReport;
use app\models\TableConfig;
use app\models\UserAthu;
use app\models\users\updateUserForm;
use yii;
use app\models\adminLoginForm;
use app\models\MyUser;
use app\models\MyUserSearch;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\check\CorporaCheckModel;
class AdminController extends Controller
{
    public $layout='mainLayout';
    public function actionLogin()
    {
        //判断是否为游客
        if(!\yii::$app->user->isGuest)
        {
            return $this->redirect(['index']);
        }
        $adminLoginForm=new adminLoginForm();
        if($adminLoginForm->load(Yii::$app->request->post()))
        {
            //验证登录
            if($adminLoginForm->login())
            {
                return $this->redirect(['index']);
            }
        }
        return $this->render("login",['model'=>$adminLoginForm]);
    }

    /**
     * 获取用户权限信息
     * @return string  超级管理员默认全部权限，普通用户默认没有任何权限 json
     */
    public function actionGetAuthority()
    {
        //验证身份
        if(!yii::$app->user->isGuest)
        {
            $id=yii::$app->user->id;
            if(MyUser::validateAdmin($id))
            {
                if(yii::$app->request->post("id"))
                {
                    $id=yii::$app->request->post("id");
                }
                $res=MyUser::find()->where(['id'=>$id])->asArray()->one();
                //判断用户级别，若为超级管理员或普通用户则返回失败数据
                $authorities=\app\models\Authority::find()->asArray()->all();
                if($res['level_id']==1)
                {
                    $code=1;
                    return json_encode(array('code'=>$code,'authorities'=>$authorities));
                }
                if($res['level_id']==3)
                {
                    $code=3;
                    return json_encode(array('code'=>$code,'authorities'=>$authorities));
                }
                //其余的查询数据库并返回值
                else{
                    $code=2;
                    $result=\app\models\UserAthu::find()->asArray()->where(['id'=>$id])->all();
                    $userAuthorities=array();
                    foreach ($result as $row)
                    {
                        array_push($userAuthorities,$row['authority_id']);
                    }
                    return json_encode(array('code'=>$code,'authorities'=>$authorities,'userAuthorities'=>$userAuthorities));
                }
                return json_encode(0);
            }
            return json_encode(0);
        }
        return json_encode(0);
    }

    /**
     * @return string json 1为修改成功
     * @throws yii\base\Exception
     */
    public function actionUpdatePassword()
    {
        if(!yii::$app->user->isGuest) {
            $id = yii::$app->user->id;
            if (MyUser::validateAdmin($id)) {
                $userId=yii::$app->request->post('id');
                $password=trim(yii::$app->request->post('password')," ");
                if($password&&strlen($password)>=6&&strlen($password)<=18)
                {
                    $userInfo=MyUser::find()->where(['id'=>$userId])->one();
                    $userInfo->password=Yii::$app->security->generatePasswordHash($password);
                    $userInfo->save();
                    return json_encode(1);
                }
                return json_encode(0);
            }
            return json_encode(0);
        }
        return json_encode(0);
    }

    /**
     * @return string json 返回用户的状态
     */
    public function actionGetStatus()
    {
        if(yii::$app->request->isPost)
        {
            $userId=yii::$app->request->post('id');
            $userInfo=MyUser::find()->where(['id'=>$userId])->one();
            return json_encode($userInfo->canlogin);
        }
    }

    public function actionLogout()
    {
        if(!yii::$app->user->isGuest)
        {
            yii::$app->user->logout();
            return $this->redirect(['login']);
        }
    }
    public function actionIndex()
    {
        if(!yii::$app->user->isGuest) {
            $id = yii::$app->user->id;
            if (MyUser::validateAdmin($id)) {
                $query=MyUser::find();
                //创建dataprovider类
                $dataProvider = new ActiveDataProvider([
                    'query'=>$query,
                    'pagination'=>[
                        'pageSize'=>6,
                    ],
                ]);
//                $searchModel = new MyUserSearch();
//                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $query->joinWith(['level']);
                $query->select('tb_user.*,tb_userlevel.level_name');
                if(!MyUser::checkCurrrentUserManageUser('用户管理'))
                    $dataProvider=null;
                    return $this->render('index',['dataProvider'=>$dataProvider]);
            }
            return $this->redirect(['login']);
        }
        return $this->redirect(['login']);
    }
    //加载用户数据
    public function actionGetUserMessage()
    {
        //验证是否登录
        if(yii::$app->user->isGuest)
            return json_encode(0);
        $select='tb_user.username as username,tb_user.email as email,tb_user.workplace as workplace,tb_user.realname as realname,tb_user.sex as sex,';
        $select.='tb_user.level_id,tb_userlevel.level_name as level_name';
        $res=MyUser::find()->joinWith(['level'])->select($select)->where(['id'=>yii::$app->user->id])->asArray()->one();
        return json_encode($res);
    }
    /*
     * 用户提交修改信息
     */
    public function actionUpdateMessage()
    {
        if(yii::$app->user->isGuest)
            return json_encode(0);
        $user=MyUser::findOne(yii::$app->user->id);
        $user->realname=yii::$app->request->post('realname')?yii::$app->request->post('realname'):$user->realname;
        $user->email=yii::$app->request->post('email')?yii::$app->request->post('email'):$user->realname;
        $user->sex=(yii::$app->request->post('sex')!=null)?intval(yii::$app->request->post('sex')):$user->sex;
//        var_dump($user->sex);
        $user->workplace=yii::$app->request->post('workplace')?yii::$app->request->post('workplace'):$user->workplace;
        if(!yii::$app->request->post('level_name'))
            return json_encode(0);
        $user->level_name=yii::$app->request->post('level_name');
//        $user->level_name='超级管理员';
        if($user->save())
            return json_encode(1);
        return json_encode(2);
    }
    //用户详情
    public function actionGetDetail()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            return "0";
        if(($id=yii::$app->request->post('id'))!=null)
        {
            $select='tb_user.id as id,tb_user.username as username,tb_user.email as email,tb_user.workplace as workplace,tb_user.realname as realname,tb_user.sex as sex,';
            $select.='tb_user.level_id,tb_userlevel.level_name as level_name,tb_user.created_at as created_at,tb_user.updated_at as updated_at';
            $query=MyUser::find()->joinWith(['level'])->select($select)->where(['id'=>$id]);
            $dataProvider=new ActiveDataProvider([
                'query'=>$query
            ]);
            $dataProvider->setSort(false);
            return $this->renderAjax("getDetail",['dataProvider'=>$dataProvider]);
        }
        return "0";

    }
    public function actionUpdateModal()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            return "0";
        if(!MyUser::checkCurrrentUserManageUser('用户管理'))//若无权限管理用户
            return "0";
        if(($id=yii::$app->request->post('id'))!=null)
        {
            $user=MyUser::find()->joinWith(['level'])->where(['id'=>$id])->one();
            //权限复选框信息
            $authorities=yii\helpers\ArrayHelper::map(Authority::getAuthorities(),'authority_id','authority_name');
            $userAuthorities=UserAthu::find()->where(['id'=>$id])->all();
            return $this->renderAjax("updateModal",['model'=>$user,'authorities'=>$authorities,'userAuthorities'=>$userAuthorities]);
        }
    }

    /**
     * @return string
     */
    public function actionCorpusManage()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            return "0";
        if(!MyUser::checkCurrrentUserManageUser(yii::$app->request->get('authority_name')))
            return "0";
        $query=CorporaDictionary::find();
        $dataProvider=new ActiveDataProvider([
            'query'=>$query,
            'pagination'=>[
                'pageSize'=>6,
                ]
        ]);
        return $this->renderAjax("corpusManage",['dataProvider'=>$dataProvider]);

    }
    public function actionUploadFile()
    {
        $code=0;
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
//            $code=2;
        if(!MyUser::checkCurrrentUserManageUser('语料库管理'))
            $code=2;
        $res=array();
        if($_FILES['upload_txt'])
        {
            Yii::$app->response->format='json';
            $uploadFileModel=new DictionaryUploadFile();
            $uploadFileModel->txtFile=$_FILES['upload_txt'];
                if($uploadFileModel->validateAndSave())
                    $code=1;
        }
        $res['code']=$code;
        if($code===1)
        {
            $keys=$uploadFileModel->getDetailSetting();
            $res['keys']=$keys;
            $res['all_level_count']=$uploadFileModel->all_level_count;
        }
        echo json_encode($res);
    }
    public function actionDeleteFile()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            die(json_encode(0));
        if(!MyUser::checkCurrrentUserManageUser('语料库管理'))
            die(json_encode(2));
        if(!unlink(yii::$app->session->get("DFile_path")))
        {
            die(json_encode(3));
        }
            die(json_encode(1));

    }

    /**
     * @throws \Exception
     */
    public function actionCreateDicCorpus()
    {
        $createModel=new createDictionarnForm();
        $createModel->corpusName=yii::$app->request->post('corpusName')!=null?yii::$app->request->post('corpusName'):null;
        $createModel->corpusPre=yii::$app->request->post('corpusPre')!=null?yii::$app->request->post('corpusPre'):null;
        $createModel->levelKey=yii::$app->request->post('levelKey')!=null?yii::$app->request->post('levelKey'):null;
        $createModel->levelNames=yii::$app->request->post('levelNames')!=null?yii::$app->request->post('levelNames'):null;
        ini_set('memory_limit', '2048M');
        try {
            if ($createModel->createCorpus())
            {
                echo json_encode(1);
            }
        } catch (\Exception $e) {
            echo json_encode($e->getMessage()." ".$e->getFile()." in ".$e->getLine());
        }

    }

    /**
     * ajax获得审核信息及分页查询信息
     * @return string
     */
    public function actionCorpusCheck()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            return "0";
        if(!MyUser::checkCurrrentUserManageUser(yii::$app->request->get('authority_name')))
            return "0";
        $currentPage=yii::$app->request->get('currentPage')?(int)yii::$app->request->get('currentPage'):0;
        $pageSize=3;
        $checkModel=new CheckService();
        try {
            $datasArr=$checkModel->getCheckMessage(['pageSize'=>$pageSize,'offSet'=>($currentPage-1)*$pageSize]);
            $datasArr['pageSize']=$pageSize;
            return json_encode($datasArr);
        } catch (yii\db\Exception $e) {
            return json_encode(["code"=>0,"message"=>$e->getMessage()]);
        }
    }

    /**
     *显示语料库报表
     */
    public function showCorpusDetail()
    {
        //验证用户身份
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
           return json_encode(0);
        if(!MyUser::checkCurrrentUserManageUser("语料库管理")||!MyUser::checkCurrrentUserManageUser("语料审核"))
            return json_encode(0);

    }
    /*
     * ajax返回文本语料库信息信息
     */
    public function actionShowTextCorpora()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            return json_encode(0);
        if(!MyUser::checkCurrrentUserManageUser("语料库管理"))
            return json_encode(0);
        $pageSize=8;
        $offSet=yii::$app->request->get('offSet')?(int)yii::$app->request->get('offSet')-1:0;
        $condition=yii::$app->request->get('condition')?(int)yii::$app->request->get('condition'):[];
        $flag=1;
        $conditionArr=[];
        //生成条件数组(从一维数组转换为二维数组)
        foreach ($condition as $key=>$value)
        {
            if($flag%2==0)
                $conditionArr[$condition[$key-1]]=$value;
            $flag++;
        }
        try
        {
            $model=new TextCorporaSearch(['pageSize'=>$pageSize,'offSet'=>$offSet,'condition'=>$conditionArr]);
            $res=[
                'code'=>1,
                'pageSize'=>$pageSize,
                'allSum'=>$model->totalCount,
                'dataArr'=>$model->getTextCorpora()
            ];
        }catch (\Exception $e)
        {
            $res=[
              'code'=>0,
              'message'=>$e->getMessage()."<br> ".$e->getFile()."<br> ".$e->getFile()."<br>".$e->getTraceAsString(),
            ];
        }
        return json_encode($res);
    }
    public function actionCorpusReport()
    {
        if(yii::$app->user->isGuest||!MyUser::validateAdmin(yii::$app->user->id))
            return json_encode(0);
        if(!MyUser::checkCurrrentUserManageUser("语料库管理")||!MyUser::checkCurrrentUserManageUser("语料审核"))
            return json_encode(0);
        $corpus_id=yii::$app->request->get("corpus_id")?(int)yii::$app->request->get("corpus_id"):0;
        if($corpus_id)
        {
            $model=new TextCorpusReport($corpus_id);
            try{
                $model->getReport();
                return json_encode([
                    'code'=>1,
                    'report'=>$model->dataArr,
                    'corpusData'=>$model->getCorpusData()
                ]);
            }catch (\Exception $e)
            {
                return json_encode([
                    'code'=>0,
                    'message'=>$e->getMessage()
                ]);
            }
        }
    }
    //测试页面

    /**
     * @throws \Exception
     */
    public function actionTest()
    {
//        ini_set('memory_limit', '4096M');
//        $model=new TextCorpora(['filePath'=>"G:/wamp64/www/basic/crawler/peopleNewsparper-199801.txt",
//            'title'=>'人民日报98年1月',
//            'resource'=>'人民日报',
//            'corpus_name'=>"人民日报语料",
//            'tableName'=>'tb_corpora_text'
//            ]);
//        $model->getConetntFile();
//        $model=new TextCorpusReport(7);
//        try{
//            $model->getReport();
//            var_dump($model->dataArr);
//        }catch (\Exception $e)
//        {
//            echo $e->getMessage();
//        }
        for ($i=4004;$i<4364;$i++)
        {
            yii::$app->db->createCommand()->update("tb_wordlist",['corpus_id'=>14],['id'=>$i])->execute();
        }
    }
    private static function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

}