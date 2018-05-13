<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/7
 * Time: 14:49
 */

namespace app\controllers;
use yii;
use app\models\adminLoginForm;
use app\models\MyUser;
use app\models\MyUserSearch;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
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
     * @return string|yii\web\Response
     */
    public function actionUserManage()
    {
        //获取cookie值
        //如果游客没登录或者登录判定不为管理员
        if(!yii::$app->user->isGuest)
        {
            $id=yii::$app->user->id;
            if(MyUser::validateAdmin($id))
            {
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
                //增加条件
                $username=yii::$app->request->post("username")? trim(yii::$app->request->post("username")," "):'';
                $level_id=(yii::$app->request->post("level_id")&&yii::$app->request->post("level_id")!='0')? intval(yii::$app->request->post("level_id")):'';
                $query->andFilterWhere(['like','username',$username])->andFilterWhere(['level_id'=>$level_id]);
                return $this->render('userManage',['dataProvider'=>$dataProvider]);
            }
            return $this->redirect(['login']);
        };
        return $this->redirect(['login']);
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

    /**
     * 跟新用户登录状态信息
     * @return string  返回1表示修改成功，返回0表示修改失败
     */
    public function actionUpdateStatus()
    {
        $userId=yii::$app->request->post('id');
        $userInfo=MyUser::find()->where(['id'=>$userId])->one();
        $userInfo->canlogin=intval(yii::$app->request->post('status'));
//        return var_dump($userInfo->canlogin);
        if($userInfo->save())
            return json_encode(1);
        else return json_encode(0);
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
                    return $this->render('index');
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
}