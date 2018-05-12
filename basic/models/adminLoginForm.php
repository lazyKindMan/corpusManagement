<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/3
 * Time: 9:49
 */

namespace app\models;

use yii;
use yii\base\Model;

class adminLoginForm extends Model
{
    public $username;
    public $password;
    public $verifyCode;
    public $rememberMe = true;

    private $_user = false;
    public function rules()
    {
        return [
            // 对username的值进行两边去空格过滤
            ['username', 'filter', 'filter' => 'trim'],
            [['username','password'],'required','message'=>""],
            ['password', 'validatePassword'],
            ['username','checkUser'],
            ['verifyCode','captcha','message'=>'验证码不正确！'],
            ['rememberMe', 'boolean'],
        ];
    }
    /*
     * @return
     */
    public function checkUser($attribute, $params)
    {
        if(!$this->hasErrors()) {
            if (!$this->getUser()->validateIsAdmin())
                $this->addError($attribute, '账号为非管理员账号或者账号已被封停');
        }
    }
    public function validatePassword($attribute, $params)
    {
        // hasErrors方法，用于获取rule失败的数据
//        if(!$this->hasErrors())
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名或者密码不正确');
            }
        }

    }
    public function login()
    {
        $user=$this->getUser();
        if($this->validate())
        {
            //判断是否为能登陆的管路员
            //保存用户session信息
            return Yii::$app->user->login($user,$this->rememberMe?3600 * 24 *7: 0);
        }
        else {
            return false;
        }
    }
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = MyUser::findByUsername($this->username);
        }

        return $this->_user;
    }
    public function attributeLabels()
    {
        return [
            // 'verifyCode' => 'Verification Code',
            'verifyCode' => '验证码',//在官网的教程里是加上了英文字母，我这里先给去掉了,这里去 掉会不会产生影响因为我还没做接收验证，只做了验证码显示的功能，你们可以自己测试下
        ];
    }
}