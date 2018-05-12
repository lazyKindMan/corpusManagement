<?php
    $curl = curl_init("http://lib.hsesystem.com/lib_icd10/");
    curl_setopt($curl, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)");
    curl_exec($curl);
    curl_close($curl);
?>