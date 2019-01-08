<?php
/*
 * 下载文件的时候对文件进行重命名，最好是网站本地文件，
 * 如果不是本地文件就需要先下载到本地
*/
$url = $_GET['url'];
$file_name = $_GET['name'];
actionDown($url, $file_name);

//var_dump($url);exit();
 function actionDown($url, $file_name)
{
    $file_base_url = 'http://XXXX.com';
    //替换域名为空字符，得到本地文件路径
    $file = ".".str_replace($file_base_url, "", $url);
    //获取文件后缀
    $suffix = substr(strrchr($file, '.'), 1);


    if (file_exists($file)){
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$file_name.".".$suffix);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}