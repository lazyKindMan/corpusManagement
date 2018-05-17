<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/13
 * Time: 16:17
 */

namespace app\models\users;


use app\models\MyUser;
use yii\base\Model;

class updateUserForm extends Model
{
    public $email;
    public $updated_at;
    public $realname;
    public $sex;

    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required', 'message' => '邮箱不可以为空'],
            ['email', 'email'],
            ['email', 'string', 'max' => 45],
            ['email', 'unique', 'targetClass' => '\app\models\MyUser', 'message' => 'email已经被使用了.'],
            ['updated_at', 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }
    public function update($id)
    {
        $user=MyUser::findOne($id);
        $user->email=$this->email?$this->email:$user->email;
        $user->sex=$this->sex?$this->sex:$user->sex;
        $user->realname=$this->realname?$this->realname:$user->realname;
        $user->updated_at=date("Y-m-d H:i:s");
        if (!$this->validate()) {
            return null;
        }
        $user->save(false);
        var_dump($user->errors);
    }
}