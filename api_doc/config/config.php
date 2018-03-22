<?php
//开启报错
ini_set('display_errors', 1);

//因CI需定义变量.
define('BASEPATH', 'API_DOC');

//设置项目根目录
define('API_DOC_PATH', dirname(dirname(dirname(__FILE__))));

//设置项目API目录
define('SYSTEM_CLASS_DIR', API_DOC_PATH.'/app/Services/ApiServer/Response/');

//设置当前API目录
define('CURRENT_CLASS_DIR', API_DOC_PATH.'/api_doc/class/');

//设置版权
define('COPYRIGHT', '© Powered By 拓源内部API服务平台 1.0');

//设置产品名称
define('PRODUCT_NAME', '拓源内部API服务平台');

$not_api_file = [
  'BaseResponse.php',
  'InterfaceResponse.php'
];

$class_to_en = [
  'Badwords'        => '敏感词汇接口',
  'Demo'            => '实例写法接口',
  'Ocr'             => '腾讯优图qcr接口',
  'Qrcode'          => '二维码接口',
  'Wallpaper'       => '壁纸接口',
  'Wechat'          => '微信相关接口',
  'ArticleCollect'  => '文章采集接口',
  'Music'           => '音乐接口'
];
