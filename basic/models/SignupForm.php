<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/3
 * Time: 14:23
 */

namespace app\models;

use Yii;
use yii\base\Model;
use yii\captcha\Captcha;
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;
    public $canlogin;
    public $level_id;
    public $verifyCode;
    //校验规则
    public function rules()
    {
        return [
            // 对username的值进行两边去空格过滤
            ['username', 'filter', 'filter' => 'trim'],
            // required表示必须的，也就是说表单提交过来的值必须要有
            ['username', 'required', 'message' => '用户名不可以为空'],
            // unique表示唯一性，targetClass表示的数据模型 这里就是说UserBackend模型对应的数据表字段username必须唯一
            ['username', 'unique', 'targetClass' => '\app\models\MyUser', 'message' => '用户名已存在.'],
            // string 字符串，这里我们限定的意思就是username至少包含2个字符，最多255个字符
            ['username', 'string', 'min' => 2, 'max' => 30],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => '邮箱不可以为空'],
            ['email', 'email'],
            ['email', 'string', 'max' => 45],
            ['email', 'unique', 'targetClass' => '\app\models\MyUser', 'message' => 'email已经被使用了.'],
            ['password', 'required', 'message' => '密码不可以为空'],
            ['password', 'string', 'min' => 6,'max'=>18, 'tooShort' => '密码至少填写6位','tooLong'=>'密码不要超过18位'],
            [['created_at', 'updated_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            //允许登陆和用户创建默认等级
            [['canlogin'],'default','value'=>1],
            [['level_id'],'default','value'=>3],
            //框架ajax验证码bug，重写验证方法
            ['verifyCode', 'captcha'],
        ];
    }
  //关键一步，不用自动调用而重新生成验证码
//    public function codeVerify($attribute)
//    {
//        //参数：'captcha'，即控制器中actions()内的名称'captcha'；Yii::$app->controller，调用验证的当前控制器（必须设置）
//        $captcha_validate  = new \yii\captcha\CaptchaAction('captcha',Yii::$app->controller);
//        if($this->$attribute){
//            $code = $captcha_validate->getVerifyCode();
//            if($this->$attribute!=$code){
//                $this->addError($attribute, '验证码错误');
//            }
//        }
//    }
    public function signup()
    {
        // 调用validate方法对表单数据进行验证，验证规则参考上面的rules方法,如果不调用validate方法，那上面写的rules就完全是废的啦
        if (!$this->validate()) {
            return null;
        }
        $user=new MyUser();
        $user->username=$this->username;
        $user->email=$this->email;
//        $user->password=$this->password;
        $user->created_at=$this->created_at;
        $user->updated_at=$this->updated_at;
        $user->canlogin=$this->canlogin;
        $user->level_id=$this->level_id;
        //设置加密密码
        $user->setPassword($this->password);
        $user->generateAuthKey();
        // save(false)的意思是：不调用UserBackend的rules再做校验并实现数据入库操作
        // 这里这个false如果不加，save底层会调用UserBackend的rules方法再对数据进行一次校验，这是没有必要的。
        // 因为我们上面已经调用Signup的rules校验过了，这里就没必要再用UserBackend的rules校验了
        return $user->save(false);

    }
    /*
  * * @return array customized attribute labels
  */
    public function attributeLabels()
    {
        return [
            // 'verifyCode' => 'Verification Code',
            'verifyCode' => '验证码',//在官网的教程里是加上了英文字母，我这里先给去掉了,这里去 掉会不会产生影响因为我还没做接收验证，只做了验证码显示的功能，你们可以自己测试下
        ];
    }
}