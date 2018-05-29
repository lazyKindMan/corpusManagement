<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/17
 * Time: 19:41
 */

namespace app\models\corpus;

class DictionaryUploadFile
{
    public $txtFile;
    public $all_level_count;
    public $spilt_character;
    public $file_path;
    /**
     * 检验和存储
     * @return bool
     */
    public function validateAndSave()
    {
        //获取其他表单值
        if(!array_key_exists('all_count_level',$_POST))
            return false;
        if(!array_key_exists('split_character',$_POST))
            return false;
        $this->all_level_count=$_POST['all_count_level'];
        $this->spilt_character=$_POST['split_character'];
        if(!is_int((int)$this->all_level_count))
        {
            return false;
        }
        if(!is_string($this->spilt_character)||strlen($this->spilt_character)!=1)
        {
            return false;
        }
        //验证文件数据
        if($this->txtFile['type']==='text/plain')
        {
            if ($this->txtFile["error"] > 0)
            {
               return false;
            }
            else{
                $path='uploaded/dictionaryDocument/';
                self::create_folders($path);
                if(move_uploaded_file($this->txtFile['tmp_name'],$path.date("Ymd_His")."-".$this->txtFile['name']))
                {
                    $this->file_path=$path.date("Ymd_His")."-".$this->txtFile['name'];
                    //存入session
                    $session=\Yii::$app->session;
                    $session->set('DFile_path',$this->file_path);
                    $session->set('spilt_character',$this->spilt_character);
                    $session->set('all_level_count',$this->all_level_count);
                    return true;
                }
            }
        }
        return false;


    }
    private static function create_folders($dir){
        return is_dir($dir) or (self::create_folders(dirname($dir)) and mkdir($dir, 0777));
    }

    /**
     * 获取文件中键值情况
     * @return array|null
     */
    public function getDetailSetting()
    {
        //读取一行数据
        $handle=fopen($this->file_path,'r');
        $firstRow=fgets($handle);
        $firstRow=trim($firstRow);
        $firstRow=trim($firstRow, "\xEF\xBB\xBF");
        if($firstRow!=null)
        {
            $key=[];
            $result=preg_split("/ +/",$firstRow);
            //获取键值
            foreach ($result as $value)
            {
                $vals=explode(":",$value);
                $key[]=$vals[0];
            }
            return $key;
        }
        return null;
    }
}