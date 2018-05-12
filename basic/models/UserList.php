<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/8
 * Time: 9:52
 */

namespace app\models;


use yii\base\Model;

class UserList extends Model
{
    public $id;
    public $username;
    //联表查询
    public $level;
    public $update_at;
    public $canlogin;


}