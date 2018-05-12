<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/4
 * Time: 16:36
 * 重写验证码类的验证功能
 */
namespace app\controllers;

use Yii;
use yii\captcha\CaptchaAction;

class MyCaptchaController extends CaptchaAction
{
    /*
     * @params string $input 验证码输入参数
     * @params bool $caseSensitive 检测大小写是否敏感
     */
    public function validate($input, $caseSensitive)
    {
        $code=$this->getVerifyCode();
        //验证码有效
        $valid=$caseSensitive ? ($input===$code) :strcasecmp($input,$code) === 0;

        $session=Yii::$app->getSession();
        $session->open();

        $name=$this->getSessionKey()."cout";//增加一个验证次数session
        $session[$name]=$session[$name]+1;
        if($valid || $session[$name]>$this->testLimit &&$this->testLimit>0)
        {
            //修改为允许ajax的二次验证
            if(Yii::$app->request->isAjax === false)
                $this->getVerifyCode(true);//如果不为ajax请求，那么重新生成验证码
        }
        return $valid;//返回验证码是否正确
    }
}