<?php
/**
 * Created by PhpStorm.
 * User: liuhan
 * Date: 16/11/16
 * Time: 下午4:14
 */
// 全局的安全过滤函数
function safe($text, $type = 'html') {
    // 无标签格式
    $text_tags = '';
    // 只保留链接
    $link_tags = '<a>';
    // 只保留图片
    $image_tags = '<img>';
    // 只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    // 标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    // 兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    // 内容等允许HTML的格式
    $html_tags = $base_tags . '<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    // 全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    // 过滤标签
    $text = html_entity_decode ( $text, ENT_QUOTES, 'UTF-8' );
    $text = strip_tags ( $text, ${$type . '_tags'} );

    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while ( preg_match ( '/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat ) ) {
            $text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
        }
        while ( preg_match ( '/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat ) ) {
            $text = str_ireplace ( $mat [0], $mat [1] . $mat [3], $text );
        }
    }
    return $text;
}

//用正则过滤所有空白符
function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}


/**
 * 字符串截取，支持中文和其他编码
 *
 * @access public
 * @param string $str
 *          需要转换的字符串
 * @param string $start
 *          开始位置
 * @param string $length
 *          截取长度
 * @param string $charset
 *          编码格式
 * @param string $suffix
 *          截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
    if (function_exists ( "mb_substr" ))
        $slice = mb_substr ( $str, $start, $length, $charset );
    elseif (function_exists ( 'iconv_substr' )) {
        $slice = iconv_substr ( $str, $start, $length, $charset );
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re ['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re ['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re ['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re ['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all ( $re [$charset], $str, $match );
        $slice = join ( "", array_slice ( $match [0], $start, $length ) );
    }
    return $suffix ? $slice . '...' : $slice;
}



/**
 * 系统加密方法
 *
 * @param string $data
 *          要加密的字符串
 * @param string $key
 *          加密密钥
 * @param int $expire
 *          过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
    $data = base64_encode ( $data );
    $x = 0;
    $len = strlen ( $data );
    $l = strlen ( $key );
    $char = '';
    
    for($i = 0; $i < $len; $i ++) {
        if ($x == $l)
            $x = 0;
        $char .= substr ( $key, $x, 1 );
        $x ++;
    }
    
    $str = sprintf ( '%010d', $expire ? $expire + time () : 0 );
    
    for($i = 0; $i < $len; $i ++) {
        $str .= chr ( ord ( substr ( $data, $i, 1 ) ) + (ord ( substr ( $char, $i, 1 ) )) % 256 );
    }
    return str_replace ( array (
            '+',
            '/',
            '=' 
    ), array (
            '-',
            '_',
            '' 
    ), base64_encode ( $str ) );
}

/**
 * 系统解密方法
 *
 * @param string $data
 *          要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key
 *          加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = '') {
    $key = md5 ( empty ( $key ) ? C ( 'DATA_AUTH_KEY' ) : $key );
    $data = str_replace ( array (
            '-',
            '_' 
    ), array (
            '+',
            '/' 
    ), $data );
    $mod4 = strlen ( $data ) % 4;
    if ($mod4) {
        $data .= substr ( '====', $mod4 );
    }
    $data = base64_decode ( $data );
    $expire = substr ( $data, 0, 10 );
    $data = substr ( $data, 10 );
    
    if ($expire > 0 && $expire < time ()) {
        return '';
    }
    $x = 0;
    $len = strlen ( $data );
    $l = strlen ( $key );
    $char = $str = '';
    
    for($i = 0; $i < $len; $i ++) {
        if ($x == $l)
            $x = 0;
        $char .= substr ( $key, $x, 1 );
        $x ++;
    }
    
    for($i = 0; $i < $len; $i ++) {
        if (ord ( substr ( $data, $i, 1 ) ) < ord ( substr ( $char, $i, 1 ) )) {
            $str .= chr ( (ord ( substr ( $data, $i, 1 ) ) + 256) - ord ( substr ( $char, $i, 1 ) ) );
        } else {
            $str .= chr ( ord ( substr ( $data, $i, 1 ) ) - ord ( substr ( $char, $i, 1 ) ) );
        }
    }
    return base64_decode ( $str );
}




/**
 * 对查询结果集进行排序
 *
 * @access public
 * @param array $list
 *          查询结果
 * @param string $field
 *          排序的字段名
 * @param array $sortby
 *          排序类型
 *          asc正向排序 desc逆向排序 nat自然排序
 * @return array
 *
 */
function list_sort_by($list, $field, $sortby = 'asc') {
    if (is_array ( $list )) {
        $refer = $resultSet = array ();
        foreach ( $list as $i => $data )
            $refer [$i] = &$data [$field];
        switch ($sortby) {
            case 'asc' : // 正向排序
                asort ( $refer );
                break;
            case 'desc' : // 逆向排序
                arsort ( $refer );
                break;
            case 'nat' : // 自然排序
                natcasesort ( $refer );
                break;
        }
        foreach ( $refer as $key => $val )
            $resultSet [] = &$list [$key];
        return $resultSet;
    }
    return false;
}




/**
 * 格式化字节大小
 *
 * @param number $size
 *          字节数
 * @param string $delimiter
 *          数字和单位分隔符
 * @return string 格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array (
            'B',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB' 
    );
    for($i = 0; $size >= 1024 && $i < 5; $i ++)
        $size /= 1024;
    return round ( $size, 2 ) . $delimiter . $units [$i];
}


/**
 * 时间戳格式化
 *
 * @param int $time         
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL, $format = 'Y-m-d H:i') {
    if (empty ( $time ))
        return '';
    
    $time = $time === NULL ? NOW_TIME : intval ( $time );
    return date ( $format, $time );
}
function day_format($time = NULL) {
    return time_format ( $time, 'Y-m-d' );
}
function hour_format($time = NULL) {
    return time_format ( $time, 'H:i' );
}




// 基于数组创建目录和文件
function create_dir_or_files($files) {
    foreach ( $files as $key => $value ) {
        if (substr ( $value, - 1 ) == '/') {
            mkdir ( $value );
        } else {
            @file_put_contents ( $value, '' );
        }
    }
}

if (! function_exists ( 'array_column' )) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array ();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values ( $input );
            } else {
                foreach ( $input as $row ) {
                    $result [] = $row [$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ( $input as $row ) {
                    $result [$row [$indexKey]] = $row;
                }
            } else {
                foreach ( $input as $row ) {
                    $result [$row [$indexKey]] = $row [$columnKey];
                }
            }
        }
        return $result;
    }
}



// 阿拉伯数字转中文表述，如101转成一百零一
function num2cn($number) {
    $number = intval ( $number );
    $capnum = array (
            "零",
            "一",
            "二",
            "三",
            "四",
            "五",
            "六",
            "七",
            "八",
            "九" 
    );
    $capdigit = array (
            "",
            "十",
            "百",
            "千",
            "万" 
    );
    
    $data_arr = str_split ( $number );
    $count = count ( $data_arr );
    for($i = 0; $i < $count; $i ++) {
        $d = $capnum [$data_arr [$i]];
        $arr [] = $d != '零' ? $d . $capdigit [$count - $i - 1] : $d;
    }
    $cncap = implode ( "", $arr );
    
    $cncap = preg_replace ( "/(零)+/", "0", $cncap ); // 合并连续“零”
    $cncap = trim ( $cncap, '0' );
    $cncap = str_replace ( "0", "零", $cncap ); // 合并连续“零”
    $cncap == '一十' && $cncap = '十';
    $cncap == '' && $cncap = '零';
    // echo ( $data.' : '.$cncap.' <br/>' );
    return $cncap;
}



function week_name($number = null) {
    if ($number === null)
        $number = date ( 'w' );
    
    $arr = array (
            "日",
            "一",
            "二",
            "三",
            "四",
            "五",
            "六" 
    );
    
    return '星期' . $arr [$number];
}
// 日期转换成星期几
function daytoweek($day = null) {
    $day === null && $day = date ( 'Y-m-d' );
    if (empty ( $day ))
        return '';
    
    $number = date ( 'w', strtotime ( $day ) );
    
    return week_name ( $number );
}


// 获取随机的字符串，用于token，EncodingAESKey等的生成
function get_rand_char($length = 6) {
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $strLength = 61;
    
    for($i = 0; $i < $length; $i ++) {
        $res .= $str [rand ( 0, $strLength )];
    }
    
    return $res;
}
/**
 * 根据两点间的经纬度计算距离
 *
 * @param float $lat
 *          纬度值
 * @param float $lng
 *          经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6367000; // approximate radius of earth in meters
                            
    // Convert these degrees to radians to work with the formula
    $lat1 = ($lat1 * pi ()) / 180;
    $lng1 = ($lng1 * pi ()) / 180;
    
    $lat2 = ($lat2 * pi ()) / 180;
    $lng2 = ($lng2 * pi ()) / 180;
    
    // Using the Haversine formula http://en.wikipedia.org/wiki/Haversine_formula calculate the distance
    
    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow ( sin ( $calcLatitude / 2 ), 2 ) + cos ( $lat1 ) * cos ( $lat2 ) * pow ( sin ( $calcLongitude / 2 ), 2 );
    $stepTwo = 2 * asin ( min ( 1, sqrt ( $stepOne ) ) );
    $calculatedDistance = $earthRadius * $stepTwo;
    
    return round ( $calculatedDistance );
}


/**
 *判断是否为微信浏览器
*/
function is_weixin_browser(){ 
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
    }   
    return false;
}

function checkEmail($mail){
	return preg_match('/^[\w.\-]+@(?:[a-z0-9]+(?:-[a-z0-9]+)*\.)+[a-z]{2,6}$/', $mail);
}

function checkPhone($phone){
	return preg_match('/^1\d{10}$/', $phone);
}


    /**
    把用户输入的文本转义（主要针对特殊符号和emoji表情）
     */
      function userTextEncode($str){
        if(!is_string($str))return $str;
        if(!$str || $str=='undefined')return '';

        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[ed][0-9a-f]{3})/i",function($str){
            return addslashes($str[0]);
        },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }
    /**
    解码上面的转义
     */
     function userTextDecode($str){
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i',function($str){
            return '\\';
        },$text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }

	 public  function GetPageContent($url){
		$ch = curl_init(); 
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
		$contents = curl_exec($ch); 
		curl_close($ch); 
		return $contents;
	}
