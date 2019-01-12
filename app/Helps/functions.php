<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/8 0008
 * Time: 下午 10:55
 */
/**
 * 创建订单号码
 * @return   [type]                   [description]
 * @Author:  slade
 * @DateTime :2017-05-13T16:54:42+080
 */
function careateTicket($prefix){
  $order_id = '';
  while (true) {
    // 订单号码主体（YYYYMMDDHHIISSNNNNNNNN）
    $order_id_main = rand(10000000, 99999999);
    // 订单号码主体长度
    $order_id_len = strlen($order_id_main);
    $order_id_sum = 0;
    for ($i = 0; $i < $order_id_len; $i ++) {
      $order_id_sum += (int) (substr($order_id_main, $i, 1));
    }
    // 唯一订单号码（YYYYMMDDHHIISSNNNNNNNNCC）
    $order_id = $prefix . $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
    return $order_id;
  }
}

/**
 * 获取客户端ip
 * @author: slide
 * @return bool
 *
 */
function getRealIp() {
  $ip=false;
  if(!empty($_SERVER["HTTP_CLIENT_IP"])){
    $ip = $_SERVER["HTTP_CLIENT_IP"];
  }
  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
    if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
    for ($i = 0; $i < count($ips); $i++) {
      if (!preg_match ("/^(10│172.16│192.168)./i", $ips[$i])) {
        $ip = $ips[$i];
        break;
      }
    }
  }
  return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

/**
 * 字符串转成array
 * @author: slide
 * @param $str
 * @return array
 *
 */
function StringToArray($str)
{
  //把字符串的每个字符转化为数组
  $ar=array();
  if(preg_match("/[".chr(0xb0)."-".chr(0xf7)."]+/",$str))
  {//如果是中文字符串则把每个字转化为数组
    $a=(mb_strlen($str))/2;
    for($i=0;$i<$a;$i++)
    {
      $ar[$i]=mb_substr($str, $i, 1, 'GB2312');
    }
  }
  else
  {
    //如果是英文字符串则把每个字转化为数组
    $a=(mb_strlen($str));
    for($i=0;$i<$a;$i++)
    {
      $ar[$i]=mb_substr($str, $i, 1, 'utf-8');
    }
  }
  return $ar;
}

/**
 * 输出不同的正则
 * @author: slide
 * @param $arry
 * @return string
 *
 */
function ZhengZeExpr($arry)
{
  //把刚才分割好的数组元素再次分割成新元素数组，并且组成正则表达式
  $array=StringToArray($arry);//把刚才分割好的数组元素再次分割成新元素数组
  $str="/(?:[\d\D]*";
  $count1=count($array);
  if(preg_match("/[".chr(0xb0)."-".chr(0xf7)."]+/",$array[0]))
  {
    //如果是中文敏感词，那么就生成每个字中间不能超过15个字的正则表达式
    for($j=0;$j<$count1;$j++)
    {
      $str=$str.$array[$j]."+";
      if($j<$count1-1)
        $str=$str."[\d\D]{0,15}";
      else
        $str=$str."[\d\D]*)/";
    }
  }
  else
  {
    //如果是英文敏感词，那么就生成每个字符中间不能超过15个字符的正则表达式

    for($j=0;$j<$count1;$j++)
    {
      $str=$str.$array[$j]."+";
      if($j<$count1-1)
        $str=$str."[\d\D]{0,5}";
      else
        $str=$str."[\d\D]*/";
    }
  }
  return $str;
}
/**
 * Filter the invalid JSON string.
 *
 * @param \Psr\Http\Message\StreamInterface|string $invalidJSON
 *
 * @return string
 */
function fuckTheWeChatInvalidJSON($invalidJSON)
{
  return preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', trim($invalidJSON));
}

function http_post($url, $data = null){
  $curl =curl_init();

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  $data = fuckTheWeChatInvalidJSON(json_encode($data, true));
  if(!empty($data) && $data){
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
  }

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  $output = curl_exec($curl);
  curl_close($curl);

  return $output;
}

/**
 * CURL请求
 * @param $url 请求url地址
 * @param $method 请求方法 get post
 * @param null $postfields post数据数组
 * @param array $headers 请求header信息
 * @param bool|false $debug  调试开启 默认false
 * @return mixed
 */
function httpRequest($url, $method="GET", $postfields = null, $headers = array(), $debug = false, $file = false) {
  $method = strtoupper($method);
  $ci = curl_init();
  /* Curl settings */
  curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
  curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
  curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 60); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
  curl_setopt($ci, CURLOPT_TIMEOUT, 7); /* 设置cURL允许执行的最长秒数 */
  curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
  switch ($method) {
    case "POST":
      curl_setopt($ci, CURLOPT_POST, true);
      if (!empty($postfields)) {
        if(!$file){
          $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
        }else{
          $tmpdatastr = $postfields;
          curl_setopt($ci, CURLOPT_VERBOSE, 0);
        }
        curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
      }
      break;
    default:
      curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
      break;
  }
  $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
  curl_setopt($ci, CURLOPT_URL, $url);
  if($ssl){
    curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
    curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
  }
  //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
  curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
  curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ci, CURLINFO_HEADER_OUT, true);
  /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
  $response = curl_exec($ci);
  $requestinfo = curl_getinfo($ci);
  $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
  if ($debug) {
    echo "=====post data======\r\n";
    dump($postfields);
    echo "=====info===== \r\n";
    dump($requestinfo);
    echo "=====response=====\r\n";
    dump($response);
  }
  curl_close($ci);
  // return $response;
  return array($http_code, $response,$requestinfo);
}

/**
 * 腾讯优图鉴权生成
 * @methods
 * @desc
 * @author slide
 * @param $expired
 * @param $userid
 * @return string
 *
 */
function appSign($expired,$userid = '') {
  $secretId = config('youtu.secret_id');
  $secretKey = config('youtu.secret_key');
  $appid  =  config('youtu.app_id');
  $userid = $userid ? $userid : config('youtu.userId');
  if (empty($secretId) || empty($secretKey)) {
    return -1;
  }

  $now = time();
  $rdm = rand();
  $plainText = 'a='.$appid.'&k='.$secretId.'&e='.$expired.'&t='.$now.'&r='.$rdm.'&u='.$userid;
  $bin = hash_hmac("SHA1", $plainText, $secretKey, true);
  $bin = $bin.$plainText;
  $sign = base64_encode($bin);

  return $sign;
}

/**
 * 保存图像
 * @param  string  $imgname   图像保存名称
 * @param  string  $type      图像类型
 * @param  integer $quality   图像质量
 * @param  boolean $interlace 是否对JPEG类型图像设置隔行扫描
 */
function savePng($imgname, $type = null, $quality=80,$interlace = true){
  // if(empty($this->img)) E('没有可以被保存的图像资源');
  //自动获取图像类型
  if(is_null($type)){
    $type = 'png';
  } else {
    $type = strtolower($type);
  }
  //保存图像
  if('jpeg' == $type || 'jpg' == $type){
    //JPEG图像设置隔行扫描
    imageinterlace($imgname, $interlace);
    imagejpeg($this->img, $imgname,$quality);
  }elseif('gif' == $type && !empty($this->gif)){
    $this->gif->save($imgname);
  }else{
    // 保存透明色
    imagesavealpha($this->img, true);
    $fun  =   'image'.$type;
    $fun($this->img, $imgname);
  }
}

function array2haskey($array, $key, $value){

  foreach ($array as $k => $v){

    if(isset($v[$key]) && $v[$key] == $value){
      return $k;
    }
  }

  return false;
}

/**
 * 获取客户端ip
 * @return array|false|string
 * @author cavinHUang
 * @date   2018/7/3 0003 下午 4:04
 *
 */
function getIPaddress(){
  $IPaddress='';
  if (isset($_SERVER)){
    if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
      $IPaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
      $IPaddress = $_SERVER["HTTP_CLIENT_IP"];
    } else {
      $IPaddress = $_SERVER["REMOTE_ADDR"];
    }
  } else {
    if (getenv("HTTP_X_FORWARDED_FOR")){
      $IPaddress = getenv("HTTP_X_FORWARDED_FOR");
    } else if (getenv("HTTP_CLIENT_IP")) {
      $IPaddress = getenv("HTTP_CLIENT_IP");
    } else {
      $IPaddress = getenv("REMOTE_ADDR");
    }
  }
  return $IPaddress;
}

function createArticleParagraphData ($textResult) {
  $res = [];
  $idx = 1;
  $fontPattern = [
    "/font-family\:\'.+?(\s*)\';/im",
    "/\s+/im",
    "/\&nbsp;/im",
    "\"\s*\""
  ];
  $pattern = [
    "/data-*(\w+)\=\".*?\"\s/im",
    "/data-*\=\".*?\"/im",
    "/ style='.*?'/im",
    "/ style=\".*?\"/im",
    //"/style=.+?['|\">]/im",
    "/class=.+?['|\">]/im",
    "/powered\-by=.+?['|\"]/im",
    "/<br\s*\/?>/im",
    "/<section\s*?>/im",
    "/<\/section>/im",
    "/<span>\s*<\/span>/i",
    "/<strong>/i",
    "/<\/strong>/im",
    "/<section\s*>/im",
  ];
  // 处理段落
  foreach ($textResult as $key => $value) {
    $value = stripslashes($value);
    //$value = preg_replace('/ style=\'.*?\'/im', '', $value);
    //$value = preg_replace('/\s(?!(src|alt))[a-zA-Z]+=[^\s]*/iu','', $value);
    //$value =  preg_replace('/<([a-z]+)\s+[^>]*>/is', '<$1>', $value);;
    //$value = preg_replace($fontPattern, "", $value);
    $value = preg_replace($pattern, "", $value);
    if ($value == '' || empty($value)) continue;
    $tmpStr = '';
    $valueObj = \QL\QueryList::html('<div id="wrap">'. $value .'</div>');
    if (strpos($value, '<p') !== false) {
      $valueObjResult = $valueObj->find('#wrap')->children()->htmls();
  
      foreach ($valueObjResult as $item) {
        $idStr = "b" . $idx;
        if (strpos($item, '<p') === 0) {
          $item = preg_replace(['/<p\s*?>/i', '/<\/p>/i'], '', $item);
        }
        $tmpStr = createParagraphString($item, $idStr);
        
        if (!$tmpStr) continue;
        
        $imgText = parseImgText($tmpStr, $idx);
        if (is_array($imgText['string'])) {
          foreach ($imgText['string'] as $imgItem) {
            $res[] = $imgItem;
          }
          $idx = $imgText['idx'];
        } else {
          $res[] = $tmpStr;
          $idx ++;
        }
      }
      $valueObj->destruct();
    } else {
      $idStr = "b" . $idx;
      $tmpStr = createParagraphString($value, $idStr);
      
      if (!$tmpStr) continue;
      
      $imgText = parseImgText($tmpStr, $idx);
      if (is_array($imgText['string'])) {
        foreach ($imgText['string'] as $imgItem) {
          $res[] = $imgItem;
        }
        $idx = $imgText['idx'];
      } else {
        $res[] = $tmpStr;
        $idx ++;
      }
    }
  }
  return $res;
}

function parseImgText ($string, $idx) {
  $html = \QL\QueryList::html($string);
  $htmlStr = [];
  $flag = true;
  
  if ( $html->find( '.b' )->hasClass( 'image-container' ) ) {
    
    // 处理文字
    $texts = $html->find( '.b' )->texts()->toArray();
    if ( !empty( $texts ) ) {
      foreach ( $texts as $k => $v ) {
        if ( $v !== '' ) {
          $idStr     = "b" . $idx;
          $paragraph = createParagraphString( $v, $idStr );
          $htmlStr[] = $paragraph;
          $idx++;
          $flag = false;
        }
      }
    }
    
    if ( !empty( $texts ) && $texts[ 0 ] !== '' ) {
      $imgs = $html->find( '.b img' )->attrs( 'src' );
      $flag = false;
      foreach ( $imgs as $key => $img ) {
        $idStr     = "b" . $idx;
        $img       = '<img src="' . $img . '" />';
        $paragraph = createParagraphString( $v, $idStr );
        $htmlStr[] = $paragraph;
        $idx++;
      }
    }
  }
  $html->destruct();
  return $flag ? $flag : ['string' => $htmlStr, 'idx' => $idx];
}

/**
 * 拼合段落字符串
 *
 * @param $value
 * @param $idStr
 * @return string
 * @author cavinHUang
 * @date   2018/11/3 0003 下午 2:27
 *
 */
function createParagraphString ($value, $idStr) {
  
  if ($value == '') return false;
  $value = str_replace("\n", '', $value);
  $classStr = "b";
  $tmpStr = '<p id="'. $idStr .'"';
  
  $styleStr = '';
  
  if (strpos($value, 'img') !== false) {
    $classStr .= " image-container";
    $styleStr = "position: relative; display: block;";
  }
  
  $tmpStr .= ' class="'. $classStr . '"';
  
  if (!empty($styleStr)) {
    $tmpStr .= ' style="' .$styleStr .'"';
  }
  
  if ( strpos( $value, '<p' ) !== false ) {
    $reg = [
      "/<section\s*?>/im",
      "/<\/section>/im",
      "/<p\s*?>/im",
      "/<p>/im",
      "/<\/p>/im",
    ];
    $value = preg_replace(['/section/im'], 'span', $value);
    $value = preg_replace($reg, '', $value);
  }
  $value = htmlspecialchars_decode(str_replace('&nbsp;','', htmlentities(trim($value))));
  $tmpStr .= '>' . $value . "</p>";
  return $tmpStr;
}

function myTrim($str)
{
  $search = array(" ","　","\n","\r","\t","&nbsp;");
  $replace = array("","","","","", "");
  return str_replace($search, $replace, $str);
}
