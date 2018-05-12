<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/4/26
 * Time: 10:22
 */

namespace app\models;
use yii\base\Model;

class EntryForm extends Model
{
    public $name;
    public $email;

    public function rules()
    {
        return [
            [['name','email'],'required'],
            ['email','email'],
        ];
    }
}