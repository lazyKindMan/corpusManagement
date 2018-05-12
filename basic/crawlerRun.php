<?php
/**
 * Created by PhpStorm.
 * User: longs
 * Date: 2018/5/11
 * Time: 16:02
 */

class Curl
{
    private $curlobj;
    private $Header;

    /**
     * @param $config
     * @throws \Exception
     */
    private function curlInit($config)
    {
        $this->curlobj = curl_init();
//        $this->Header = array("content-type: application/x-www-form-urlencoded; charset=UTF-8");
        //配置爬虫参数
        if(!is_array($config))
        {
            throw new \Exception("parament config must be an array");
        }
        curl_setopt($this->curlobj, CURLOPT_HEADER, 0); // 不显示 Header
        curl_setopt($this->curlobj, CURLOPT_RETURNTRANSFER, 1); // 只是下载页面内容，不直接打印
        if(array_key_exists('request',$config))
        {
            if($config['request']=='post')
            {
                curl_setopt($this->curlobj, CURLOPT_POST, 1);//设置为post方式
            }
        }
        //浏览器伪装
        curl_setopt($this->curlobj, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36');
    }
    public function do_crul($config=[],$startPage=1,$endPage=23000)
    {
        var_dump($config);
        try
        {
            $this->curlInit($config);
        }
        catch (\Exception $e)
        {
            die('Caught exception: '.$e->getMessage()."\n");
        }
//        $output=curl_exec($this->curlobj);
//        curl_close($this->curlobj);
//        fwrite($file,$output);
//        fclose($file);
        $res=[];
        $rowDate="";
        for($i=$startPage;$i<=$endPage;$i++)
        {
            if(array_key_exists('url',$config))//配置路径
                curl_setopt($this->curlobj,CURLOPT_URL,$config['url']."_{$i}");
            $output=curl_exec($this->curlobj);
            //判断是否为404
            $httpCode = curl_getinfo($this->curlobj, CURLINFO_HTTP_CODE);
            if($httpCode==404)
            {
                break;
                echo sprintf("访问到404页面，终止循环程序");
            }
            else {
                $rowDate .= $output;
                echo sprintf("当前访问页面为{$i}页\n");
            }
        }
        $file = fopen("G:/wamp64/www/basic/crawler/ICD-10-RowData.txt","w") or die("!!!!");
        fwrite($file,$rowDate);
        curl_close($this->curlobj);
        fclose($file);
//        foreach ($res as $row)
//        {
//            foreach ($row as $key=>$value)
//            {
//                fwrite($file,$key.":".$value."\r\n");
//            }
//        }

    }

    /**
     * @param $filename
     * @return int 返回个数
     */
    private function count_ICDKind($filename)
    {
        $regex='/>(第[0-9]+章[\W]+)</';
        $content=file_get_contents($filename) or die("!!!");
        preg_match_all($regex,$content,$match);
//        foreach ($match[1] as $key=>$value)
//        {
//
//        }
    }
    private function getICDData($str,&$res=[])
    {
        //匹配疾病名称
        $regex1='/疾病名称:<\/dt><dd><h1 class="title">(.+)<\/h1>/';
        preg_match_all($regex1,$str,$sickname);
        $regex2='/<dt>主要编码：<\/dt><dd><strong>(.+)<\/strong><\/dd>/';
        preg_match_all($regex2,$str,$ICDCode);
        $res[]=array('sickname'=>$sickname[1][0],'code'=>$ICDCode[1][0]);
    }
    public function test($config=[])
    {
        var_dump($config);
        try
        {
            $this->curlInit($config);
        }
        catch (\Exception $e)
        {
            die('Caught exception: '.$e->getMessage()."\n");
        }
        if(array_key_exists('url',$config))//配置路径
            curl_setopt($this->curlobj,CURLOPT_URL,$config['url']."_23000");
        $output=curl_exec($this->curlobj);
        $httpCode = curl_getinfo($this->curlobj, CURLINFO_HTTP_CODE);
        if($httpCode==404)
            echo "404";
    }
    public function getICDDatas()
    {
        $res=[];
        $str=file_get_contents("G:/wamp64/www/basic/crawler/ICD-10-RowData.txt");
        $regex1='/疾病名称:<\/dt><dd><h1 class="title">(.+)<\/h1>/';
        preg_match_all($regex1,$str,$sickname);
        $regex2='/<dt>主要编码：<\/dt><dd><strong>(.+)<\/strong><\/dd>/';
        preg_match_all($regex2,$str,$ICDCode);
        $i=0;
        foreach ($sickname[1] as $value)
        {
            $res[]=array('sickname'=>$value,'ICDcode'=>$ICDCode[1][$i]);
            $i++;
        }
        $file=fopen("G:/wamp64/www/basic/crawler/ICD-10.txt",'w');
        foreach ($res as $row)
        {
            foreach ($row as $key=>$value)
            {
                fwrite($file,$key.":".$value."\r\n");
            }
        }

    }
}
$start = microtime(true);
echo "程序开始于:".$start;
$crulObj=new Curl();
$crulObj->do_crul(['url'=>'http://lib.hsesystem.com/icd-10.do/A00.000'],1,23000);
$end=microtime(true);
$crulObj->getICDDatas();
//$crulObj->getICDDatas();
echo "总计耗时".($end-$start).'s';
