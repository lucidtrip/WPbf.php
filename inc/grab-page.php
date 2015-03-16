<?php

function grab_page($url, $ref_url = false, $data = false){

    ## config curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    //curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    if(parse_url($url, PHP_URL_SCHEME) == "https") {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    }
    if($data != false) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if($ref_url != false) {
        curl_setopt($ch, CURLOPT_REFERER, $ref_url);
    }
    
    if( false ) {
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
        curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:9150");
    }
    
    ## exec curl
    $exec = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    unset($ch);
    
    ## return
    return array($exec, $info);
}


?>
