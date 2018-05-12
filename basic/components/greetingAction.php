<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/4/26
 * Time: 15:21
 */

namespace app\components;
use yii\base\Action;

class greetingAction extends Action
{
    public function run()
    {
        return "Greeting,This from GreetingAction";
    }
}