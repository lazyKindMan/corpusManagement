<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/8
 * Time: 13:01
 */

namespace app\models;

/**
 * This is the model class for table "{{%authority}}.
 *
 * @property int $authority_id
 * @property string $authority_name
 *
 *
 */
class Authority extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%authority}}';
    }
    public function rules()
    {
        return [
            [['authority_id','authority_name'], 'required'],
            [['authority_name'], 'string', 'max' => 30],
        ];
    }
    /*
     * @return array 查询所有权限结果
     */
    public static function getAuthorities()
    {
        return self::find()->asArray()->all();
    }
}