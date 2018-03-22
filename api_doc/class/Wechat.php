<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/12 0012
 * Time: 下午 3:32
 */




use Illuminate\Http\Response;

class Wechat {

  protected $method = 'Wechat';

  protected $wechat_url = [
    'qrcode_ticket' => 'https://api.weixin.qq.com/cgi-bin/qrcode/create?',
    'qrcode_show'   => 'https://mp.weixin.qq.com/cgi-bin/showqrcode?',
    'longtoshort'   => 'https://api.weixin.qq.com/cgi-bin/shorturl?',
    'upload'        => 'http://file.api.weixin.qq.com/cgi-bin/media/upload?',
    'get'           => 'http://file.api.weixin.qq.com/cgi-bin/media/get?',
    'token'         => 'https://api.weixin.qq.com/cgi-bin/token?',
    'ip'            => 'https://api.weixin.qq.com/cgi-bin/getcallbackip?',
    'template_send' => 'https://api.weixin.qq.com/cgi-bin/message/template/send?',
    'getUserInfo'   => 'https://api.weixin.qq.com/cgi-bin/user/info?',
    'preview'       => 'https://api.weixin.qq.com/cgi-bin/message/mass/preview?',
  ];

  /**
   * 接口基础运行返回控制器数据
   * @author: slade
   * @param $params
   * @param $action
   * @return array data 数据
   */
  public function run(&$params, $action) {
    return [
      'status' => true,
      'code'   => '200',
      'data'   => [
        'controller' => 'Wechat',
        'apis' => [
          'getQrcode',
          'LangUrlToShort',
          'get',
          'getAccessToken',
          'getIp',
          'sendTemplate',
          'getUserInfo',
          'previewMsg'
        ]
      ]
    ];
  }

  /**
   * 获取微信公众号带参数的二维码
   * @method Weichat.getQrcode
   * @desc 通过access_token获取微信公众号带参数的二维码
   * @author: slide
   * @return array result 成功返回二维码地址失败返回错误信息
   */
  public function getQrcode(){
    $access_token = $this->getParams('access_token', 'string');
    $temporary = $this->getParams('temporary', 'boolean'); // 是否临时
    $expire_time = $this->getParams('expire_time', 'int');
    $expire_time = $expire_time ? $expire_time : 2592000;
    $scene = $this->getParams('scene', 'int');
    // 1、用access_token换取临时票据ticket
    $url = $this->wechat_url['qrcode_ticket'].'access_token='.$access_token;
    if($temporary){
      // 临时
      $scene_data = ['scene_id' => intval($scene)];
      $type = "QR_SCENE";
    }else{
      // 永久
      if(is_int($scene) && $scene > 0 && $scene < 100000){
        $sceneKey = 'scene_id';
        $type = "QR_LIMIT_SCENE";
      }else{
        $sceneKey = 'scene_str';
        $type = "QR_LIMIT_STR_SCENE";
      }
      $scene_data = [$sceneKey => $scene];
    }
    $params = [
      'action_name' => $type,
      'action_info' => ['scene' => $scene_data],
    ];

    if ($temporary) {
      ($expire_time !== null || $expire_time === 0) || $expire_time = 30 * 86400;
      $params['expire_seconds'] = (int) min($expire_time, 30 * 86400);
    }
    $postfile = fuckTheWeChatInvalidJSON(json_encode($params, true));
    $ticket_result = httpRequest($url, 'POST', $postfile);

    $result = json_decode($ticket_result, true);

    if(isset($result['ticket'])){
      $ticket = $result['ticket'];
      $qrcode_show_url = $this->wechat_url['qrcode_show'].'ticket='.$ticket;

      return $this->ajax(200, 'success', '获取二维码地址成功', $qrcode_show_url);
    }else{
      return $this->ajax(60001, 'error', '获取失败', $result);
    }
  }

  /**
   * 微信长连接转短连接
   * @method Wechat.LangUrlToShort
   * @desc 需要转换的长链接，支持http://、https://、weixin://wxpay 格式的url
   * @author slide
   * @return array $result 返回结果，错误时返回错误信息
   */
  public function LangUrlToShort(){
    $access_token = $this->getParams('access_token', 'string');
    $long_url = $this->getParams('url', 'string');

    $url = $this->wechat_url['longtoshort'].'access_token='.$access_token;

    $data = [
      'action'    => 'long2short',
      'long_url'  => $long_url
    ];

    $result = httpRequest($url, 'POST', fuckTheWeChatInvalidJSON(json_encode($data)));

    return $this->returnResult($result);
  }

  /**
   * 微信多媒体文件下载
   * @method Wechat.get
   * @desc 目前不支持视频下载
   * @author slide
   * @return Object result 返回资源地址
   */
  public function get(){
    // 参数
    $access_token = $this->getParams('access_token', 'string');
    $media_id = $this->getParams('media_id', 'string');

    $url = $this->wechat_url['get'].'access_token='.$access_token.'&media_id='.$media_id;

    /*$result = httpRequest($url, 'GET');
    $header = [
      'Content-Type' => 'image/jpg'
    ];*/
    return $this->ajax(200, 'success','成功',['url'=>$url]);
  }

  /**
   * 获取公众号全局票据
   * @method Wechat.getAccessToken
   * @desc 获取微信公众平台全局票据
   * @author slide
   * @return object result 返回结果
   */
  public function getAccessToken(){
    $appId = $this->getParams('appId', 'string');
    $appSecret = $this->getParams('appSecret');

    if(!$appId || !$appSecret){
      return $this->ajax(404, 'error', '缺少必要的参数');
    }

    $url = $this->wechat_url['token'];

    $data = [
      'grant_type'  => 'client_credential',
      'appid'       => $appId,
      'secret'      => $appSecret
    ];

    $result = httpRequest($url, 'GET', $data);

    return $this->returnResult($result);
  }

  /**
   * 获取微信服务器ip
   * @method Weichat.getIp
   * @desc 获取微信服务器ip用于判断是否是与微信服务器交互
   * @author slide
   * @return array ip_list 返回ip列表
   */
  public function getIp(){
    $access_token = $this->getParams('access_token', 'string');

    if(!$access_token){
      return $this->ajax(404, 'error', '缺少必要参数');
    }

    $url = $this->wechat_url['ip'].'access_token='.$access_token;

    $result = httpRequest($url, 'GET');

    return $this->returnResult($result);
  }

  /**
   * 发送模板消息
   * @method Wechat.sendTemplate
   * @desc 向指定的会员发送模板消息
   * @author slide
   * @return object result 成功消息
   */
  public function sendTemplate(){
    $access_token = $this->getParams('access_token');
    $touser = $this->getParams('touser');
    $template_id = $this->getParams('template_id');
    $template_url = $this->getParams('url');
    $menu_data = $this->getParams('menu_data');

    if(!$access_token || !$menu_data || !$touser || $template_id){
      return $this->ajax(404, 'error', '参数错误');
    }

    $url = $this->wechat_url['template_send'].'access_token='.$access_token;

    $menu_data = fuckTheWeChatInvalidJSON($menu_data);

    $data = [
      'touser' => $touser,
      'template_id' => $template_id,
      'url' => $template_url,
      'data' => $menu_data
    ];

    $data = fuckTheWeChatInvalidJSON(json_encode($data, true));

    $result = httpRequest($url, 'GET', $data);

    return $this->returnResult($result);
  }

  /**
   * 根据用户openid获取用户详细信息
   * @method Wechat.getUserInfo
   * @desc 根据用户openid获取用户详细信息
   * @author slide
   * @return int subscribe 用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
   * @return string openid 用户的标识，对当前公众号唯一
   * @return string nickname 用户的昵称
   * @return string sex 性别
   * @return string city 用户所在的城市
   * @return string country 用户所在的国家
   * @return string province 用户所在省份
   * @return string language 用户的语言，简体中文为zh_CN
   * @return string headimgurl 用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
   * @return int subscribe_time 用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
   * @return string unionid 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
   * @return string remark 公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
   * @return string groupid 用户所在的分组ID（兼容旧的用户分组接口）
   * @return string tagid_list 用户被打上的标签ID列表
   */
  public function getUserInfo(){
    $access_token = $this->getParams('access_token');
    $openid = $this->getParams('openid');

    if(!$access_token || !$openid){
      return $this->ajax(404, 'error', '参数不正确');
    }

    $url = $this->wechat_url['getUserInfo'].'access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

    $result = httpRequest($url, 'GET');

    return $this->returnResult($result);
  }

  /**
   * 主动推送一条预览消息
   * @method Wechat.previewMsg
   * @desc 利用微信预览接口可以实现消息的主动推送
   * @author slide
   * @return object result 返回成功结果或者错误信息
   */
  public function previewMsg(){
    $access_token = $this->getParams('access_token');
    $send_data = $this->getParams('send_data', 'string');
    $touser = $this->getParams('touser');
    $msgtype = $this->getParams('msgtype') !== '' ? $this->getParams('msgtype') : 'text';

    if(!$access_token || !$send_data){
      return $this->ajax(404, 'error', '参数错误');
    }

    $url = $this->wechat_url['preview'].'access_token='.$access_token;

    $data = json_decode($send_data, true);

    $send_result = [
      'touser' => $touser,
      'msgtype' => $msgtype,
      $msgtype => $data[$msgtype]
    ];

    $result = httpRequest($url, 'POST', fuckTheWeChatInvalidJSON(json_encode($send_result, true)));

    return $this->returnResult($result);
  }

  /**
   * 接口参数
   * @return array
   */
  public static function getRules() {
    return [
      'getQrcode' => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
        'temporary' => [
          'name'    => 'temporary',
          'type'    => 'boolean',
          'min'     => '',
          'require' => false,
          'default' => 'true',
          'desc'    => '是否获取临时二维码'
        ],
        'expire_time' => [
          'name'    => 'expire_time',
          'type'    => 'int',
          'min'     => '0',
          'require' => false,
          'default' => '0',
          'desc'    => '二维码的过期时间,以秒为单位,最大30天，当temporary为true时，必须'
        ],
        'scene' => [
          'name'    => 'scene',
          'type'    => 'int',
          'min'     => '0',
          'require' => false,
          'default' => '0',
          'desc'    => '需要携带的值，当临时时为必须整形，永久可以为字符串或者整形，字符串最长64位'
        ],
      ],

      'LangUrlToShort'  => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
        'url' => [
          'name'    => 'url',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '需要转换的长连接'
        ],
      ],

      'getAccessToken' => [
        'appId' => [
          'name'    => 'appId',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信appid'
        ],
        'appSecret' => [
          'name'    => 'appSecret',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信appsecret'
        ],
      ],

      'get' => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
        'media_id' => [
          'name'    => 'media_id',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信媒体id'
        ],
      ],

      'sendTemplate' => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
        'touser' => [
          'name'    => 'touser',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '需要发送的openid'
        ],
        'template_id' => [
          'name'    => 'template_id',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '需要发送的模板id'
        ],
        'url' => [
          'name'    => 'url',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '点击模板时跳转的url地址'
        ],
        'menu_data' => [
          'name'    => 'menu_data',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '需要发送的模板数据（严苛模式的json字符串， 比如{
                   "first": {
                       "value":"恭喜你购买成功！",
                       "color":"#173177"
                   }}）注意转义字符'
        ],
      ],

      'getIp' => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
      ],

      'getUserInfo' => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
        'openid' => [
          'name'    => 'openid',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '用户在微信公众号中的唯一标识'
        ],
      ],

      'previewMsg' => [
        'access_token' => [
          'name'    => 'access_token',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '微信全局票据access_token'
        ],
        'send_data' => [
          'name'    => 'send_data',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '用于预览的json数据包，比如{
    "touser":"OPENID",
    "image":{
            "media_id":"123dsdajkasd231jhksad"
            },
    "msgtype":"image"
}图片（其中media_id与根据分组群发中的media_id相同）'
        ],
        'touser' => [
          'name'    => 'touser',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '需要发送的用户openid'
        ],
        'msgtype' => [
          'name'    => 'msgtype',
          'type'    => 'string',
          'min'     => '',
          'default' => 'text',
          'require' => true,
          'desc'    => '媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb），次数为news，即图文消息'
        ],
      ],

      'run' => [
      ],
    ];
  }
}
