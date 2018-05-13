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
    private $outTimePage;
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
        if(array_key_exists("outTime",$config))
        {
            curl_setopt ( $this->curlobj, CURLOPT_TIMEOUT, $config['outTime']);
        }
        else curl_setopt ( $this->curlobj, CURLOPT_TIMEOUT, 60);
        //初始化outTimePage
        $this->outTimePage=[];
        //浏览器伪装
        curl_setopt($this->curlobj, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.139 Safari/537.36');
    }
    public function do_crul($config=[],$startPage=1,$endPage=23000)
    {
        try
        {
            $this->curlInit($config);
        }
        catch (\Exception $e)
        {
            die('Caught exception: '.$e->getMessage()."\n");
        }
        $res=[];
        for($i=$startPage;$i<=$endPage;$i++)
        {
            if(array_key_exists('url',$config))//配置路径
                curl_setopt($this->curlobj,CURLOPT_URL,$config['url']."_{$i}");
            echo sprintf("当前正在访问页面为{$i}页...  ");
            $output=curl_exec($this->curlobj);
            echo sprintf("访问页面{$i}页结束\n");
            //判断是否为404
            $httpCode = curl_getinfo($this->curlobj, CURLINFO_HTTP_CODE);
            if($httpCode==404)
            {
                break;
                echo sprintf("访问到404页面，终止循环程序");
            }
            if($output===false)//超时处理
            {
                if(curl_errno($this->curlobj) == CURLE_OPERATION_TIMEDOUT)
                {
                    echo sprintf("第{$i}页访问超时，重新访问页面\n");
                    if(array_key_exists("requestTime",$config))
                        $time=$config['requestTime'];
                    else $time=1;
                    for($t=1;$t<=$time;$t++)
                    {
                        $output=curl_exec($this->curlobj);
                        echo sprintf("重新访问{$t}次\n");
                        if($output!=false)
                        {
                            echo sprintf("重新访问成功\n");
                            break;
                        }
                    }
                    if($t>$time)//把页数记录数组中
                        $this->outTimePage[]=$i;
                }
            }
            if($output!=false) {
                $res[]=$this->getICDDatas($output);
                if($i%500==0)
                {
                    try{
                        //追加形式写入文件并情况字符串
                        echo sprintf("达到{$i}页，写入文件并清空内存\n");
                        $input='';
                        foreach ($res as $row)
                        {
                            foreach ($row as $key=>$value)
                            {
                                $input.=$key.":".$value." ";
                            }
                            $input.="\r\n";
                        }
                        file_put_contents("G:\wamp64\www\basic\crawler\ICD-10.txt", $input, FILE_APPEND);
                        $input=null;
                        $res=[];
                    }
                    catch (Exception $e)
                    {
                        die($e->getMessage());
                    }
                }
            }
        }
        curl_close($this->curlobj);
            try {
                //追加形式写入文件并情况字符串
                echo sprintf("爬取结束，存入数据\n");
                $input = '';
                foreach ($res as $row) {
                    foreach ($row as $key => $value) {
                        $input .= $key . ":" . $value . " ";
                    }
                    $input .= "\r\n";
                }
                file_put_contents("G:\wamp64\www\basic\crawler\ICD-10.txt", $input, FILE_APPEND);
            } catch (Exception $e) {
                die($e->getMessage());
            }

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
        try
        {
            $this->curlInit($config);
        }
        catch (\Exception $e)
        {
            die('Caught exception: '.$e->getMessage()."\n");
        }
        if(array_key_exists('url',$config))//配置路径
            curl_setopt($this->curlobj,CURLOPT_URL,$config['url']."_997");
        $output=curl_exec($this->curlobj);
        var_dump($this->getICDDatas($output));
    }
    public function getICDDatas($str)
    {
        $res=[];
        $regex1='/疾病名称:<\/dt><dd><h1 class="title">\s*(.+)\s*<\/h1>/';
        preg_match($regex1,$str,$sickname);
        $regex2='/<dt>主要编码：<\/dt><dd><strong>\s*(.+)\s*<\/strong><\/dd>/';
        preg_match($regex2,$str,$ICDCode);
        $regex3='/<dt>附加编码：<\/dt><dd><strong>(.+)<\/strong><\/dd>/';
        preg_match($regex3,$str,$attachCode);
        $regex4='/<dt>所属类目：<\/dt><dd><em class="code">\s*(.+)\s*<\/em>\s*(.+)\s*<\/dd>/';
        preg_match($regex4,$str,$category);
        $regex5='/<dt>所属亚目：<\/dt><dd><em class="code">\s*(.+)\s*<\/em>\s*(.+)\s*<\/dd>/';
        preg_match($regex5,$str,$suborder);
        if(count($attachCode)==0)//若没有附加编码，则认为附加编码为空字符串''
        {
            $attachCode[]=array();
            $attachCode[]='0';
        }
        if(count($suborder)==0)//没有亚类的话，亚类为0
        {
            $suborder[]=array();
            $suborder[]='0';
            $suborder[]='0';
        }
        if(count($category)==0)//没有亚类的话，亚类为0
        {
            $category[]=array();
            $category[]='0';
            $category[]='0';
        }
        $res['sickname']=$sickname[1];
        $res['ICDCode']=$ICDCode[1];
        $res['attachCode']=$attachCode[1];
        $res['categoryCode']=$category[1];
        $res['categoryName']=$category[2];
        $res['suborderCode']=$suborder[1];
        $res['suborderName']=$suborder[2];
        return $res;
    }
}
$start = microtime(true);
echo "程序开始于:".$start;
$crulObj=new Curl();
$crulObj->do_crul(['url'=>'http://lib.hsesystem.com/icd-10.do/A00.000','outTime'=>60,'requestTime'=>5],1,23000);
//$crulObj->test(['url'=>'http://lib.hsesystem.com/icd-10.do/A00.000','outTime'=>60,'requestTime'=>5]);
$end=microtime(true);
echo "总计耗时".($end-$start).'s';
//5000