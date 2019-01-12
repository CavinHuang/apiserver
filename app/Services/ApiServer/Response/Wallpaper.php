<?php
/**
 * @class 内部壁纸生成
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/15 0015
 * Time: 下午 2:04
 */

namespace App\Services\ApiServer\Response;

use Intervention\Image\Facades\Image;

class Wallpaper extends BaseResponse implements InterfaceResponse {

  /**
   * 接口基础运行返回控制器数据
   * @author slide
   * @param $params
   * @param $action
   * @return array data 数据
   */
  public function run(&$params, $action) {
    return [
      'status' => true,
      'code'   => '200',
      'data'   => [
        'controller' => 'Qrcode',
        'apis' => [
          'createQrcode',
        ]
      ]
    ];
  }

  /**
   * 生成墙纸
   * @method Wallpaper.createWallPaper
   * @desc 专门生成壁纸的方法
   * @author slide
   * @return resource result 图片资源（可显示可下载）
   */
  public function createWallPaper(){
    // 参数
    // ---------------------------
    $logo = $this->getParams('logo_url', 'string');
    $logo_size = $this->getParams('logo_size', 'string') != '' ? $this->getParams('logo_size', 'size') : '152,104';
    $type = $this->getParams('type', 'int') ? $this->getParams('type', 'int') : 1;
    $qrcode_content = $this->getParams('qrcode_content', 'string') != '' ? $this->getParams('qrcode_content') : 'Hello Qrcode';
    $qrcode_size = $this->getParams('qr_size', 'int') ? $this->getParams('qr_size', 'int') : 15;
    $qrcode_background_color = $this->getParams('qrcode_background_color', 'string') != '' ? $this->getParams('qrcode_background_color', 'string') : 'ffffff';
    $qrcode_color = $this->getParams('qrcode_color', 'string') != '' ? $this->getParams('qrcode_color', 'string') : '000000';
    $qrcode_opcity = $this->getParams('qrcode_opcity', 'boolean') == true ? 1 : 0;
    $qrcode_margin = $this->getParams('qrcode_margin', 'int') ? $this->getParams('qrcode_margin', 'int') : 1;
    $wallpaper = $this->getParams('wallpaper', 'string');
    // ----------------------------------

    $wall_size = [
      1 => [
        "logo_x" => 217,
        "logo_y" => 1006,
        "qrcode_x" => 324,
        "qrcode_y" => 1204,
        "qrcode_size" => "432,432"
      ],
      2 => [
        "logo_x" => 466,
        "logo_y" => 608,
        "qrcode_x" => 400,
        "qrcode_y" => 1326,
        "qrcode_size" => "298,298"
      ],
      3 => [
        "logo_x" => 798,
        "logo_y" => 638,
        "qrcode_x" => 384,
        "qrcode_y" => 1292,
        "qrcode_size"=>"322,322"
      ],
      4 => [
        "logo_x" => 82,
        "logo_y" => 246,
        "qrcode_x" => 390,
        "qrcode_y" => 1224,
        "qrcode_size"=>"300,300"
      ],
      5 => [
        "logo_x" => 72,
        "logo_y" => 132,
        "qrcode_x" => 710,
        "qrcode_y" => 1338,
        "qrcode_size"=>"284,284"
      ],
      6 => [
        "logo_x" => 260,
        "logo_y" => 604,
        "qrcode_x" => 394,
        "qrcode_y" => 910,
        "qrcode_size"=>"284,284"
      ],
      7 => [
        "logo_x" => 176,
        "logo_y" => 1184,
        "qrcode_x" => 394,
        "qrcode_y" => 800,
        "qrcode_size"=>"286,286"
      ],
      8 => [
        "logo_x" => 190,
        "logo_y" => 1220,
        "qrcode_x" => 398,
        "qrcode_y" => 1370,
        "qrcode_size"=>"282,282"
      ],
      9 => [
        "logo_x" => 118,
        "logo_y" => 288,
        "qrcode_x" => 318,
        "qrcode_y" => 1270,
        "qrcode_size"=>"430,430"
      ],
      10 => [
        "logo_x" => 230,
        "logo_y" => 778,
        "qrcode_x" => 350,
        "qrcode_y" => 1026,
        "qrcode_size"=>"432,432"
      ]
    ];

    // 尺寸
    // ---------------------------
    $logo_x = $this->getParams('logo_x', 'int') ? $this->getParams('logo_x', 'int') : $wall_size[$type]['logo_x'];
    $logo_y = $this->getParams('logo_y', 'int') ? $this->getParams('logo_y', 'int') : $wall_size[$type]['logo_y'];
    $qrcode_x = $this->getParams('qrcode_x', 'int') ? $this->getParams('qrcode_x', 'int') : $wall_size[$type]['qrcode_x'];
    $qrcode_y = $this->getParams('qrcode_y', 'int') ? $this->getParams('qrcode_y', 'int') : $wall_size[$type]['qrcode_y'];

    $qrcode_size = $this->getParams('qrcode_size', 'int') ? $this->getParams('qrcode_size', 'int') : $wall_size[$type]['qrcode_size'];
    // -------------------------------------


    // 底图画布
    $filePath = ROOT."/upload/wallpaper/{$type}.jpg";
    if(!file_exists($filePath) && $wallpaper == ''){
      return $this->ajax(404, 'file not exits', '没有这样的底图');
    }
    if($wallpaper == ''){
      $img = Image::make($filePath);
    }else{
      $img = Image::make($wallpaper);
    }

    // 资源是否存在
    if($logo != ''){
      $logo_resource_result = get_headers($logo);
      $statusCode=substr($logo_resource_result[0], 9, 3);
      if($statusCode != 200 && $statusCode != 304){
        return $this->ajax(404, 'logo exit', 'logo不存在');
      }


      // logo
      $logo_size_arr = explode(',',$logo_size);
      if(empty($logo_size_arr) || count($logo_size_arr) < 2){
        $logo_size_arr = [152, 104];
      }
      list($logo_width, $logo_height) = $logo_size_arr;
      $logo = Image::make(file_get_contents($logo));
      $logo->resize($logo_width, $logo_height);
      $img->insert($logo,'top-left', $logo_x, $logo_y);
    }


    // qrcode
    $params = [
      'content' => urldecode($qrcode_content),
      'size'  => 20,
      'opcity' => $qrcode_opcity,
      'background_color' => $qrcode_background_color,
      'color' => $qrcode_color,
      'margin' => $qrcode_margin,
      'rpc' => true
    ];
    $qrcode = Image::make((new Qrcode('createQrcode', $params))->createQrcode());
    $qrcode_size_arr = explode(',',$qrcode_size);
    if(empty($qrcode_size_arr) || count($qrcode_size_arr) < 2){
      $qrcode_size_arr = [430, 430];
    }
    list($qrcode_width, $qrcode_height) = $qrcode_size_arr;
    $qrcode->resize($qrcode_width, $qrcode_height);
    $img->insert($qrcode, 'top-left',$qrcode_x, $qrcode_y);

    return $img->response('jpg');
  }

  /**
   * 接口参数
   * @return array
   */
  public static function getRules () {
    // TODO: Implement getRules() method.
    return [
      'createWallPaper' => [
        'logo_url' => [
          'name'  => 'logo_url',
          'type'    => 'string',
          'min'     => '',
          'default' => '',
          'require' => false,
          'desc'    => 'logo小图的地址'
        ],
        'logo_size' => [
          'name'  => 'logo_size',
          'type'    => 'string',
          'min'     => '',
          'default' => '152,104',
          'require' => false,
          'desc'    => 'logo小图的长宽'
        ],
        'logo_x' => [
          'name'  => 'logo_x',
          'type'    => 'int',
          'min'     => '',
          'default' => '已经保存初始的位置',
          'require' => false,
          'desc'    => 'logo小图的x坐标'
        ],
        'logo_y' => [
          'name'  => 'logo_y',
          'type'    => 'int',
          'min'     => '',
          'default' => '已经保存初始的位置',
          'require' => false,
          'desc'    => 'logo小图的y坐标'
        ],
        'qrcode_x' => [
          'name'  => 'qrcode_x',
          'type'    => 'int',
          'min'     => '',
          'default' => '已经保存初始的位置',
          'require' => false,
          'desc'    => '二维码的x坐标'
        ],
        'qrcode_y' => [
          'name'  => 'qrcode_y',
          'type'    => 'string',
          'min'     => '',
          'default' => '已经保存初始的位置',
          'require' => false,
          'desc'    => '二维码的y坐标'
        ],
        'type' => [
          'name'  => 'type',
          'type'    => 'int',
          'min'     => '1',
          'default' => '1',
          'require' => false,
          'desc'    => '需要生成那张壁纸1~10'
        ],
        'qrcode_background_color' => [
          'name'  => 'qrcode_background_color',
          'type'    => 'string',
          'min'     => '',
          'default' => '#ffffff',
          'require' => false,
          'desc'    => '二维码背景颜色'
        ],
        'qrcode_color' => [
          'name'  => 'qrcode_color',
          'type'    => 'string',
          'min'     => '',
          'default' => '#000000',
          'require' => false,
          'desc'    => '二维码线条颜色'
        ],
        'qrcode_margin' => [
          'name'  => 'qrcode_margin',
          'type'    => 'int',
          'min'     => '0',
          'default' => '0',
          'require' => false,
          'desc'    => '二维码间隔'
        ],
        'qrcode_size' => [
          'name'  => 'qrcode_size',
          'type'    => 'int',
          'min'     => '0',
          'default' => '400',
          'require' => false,
          'desc'    => '二维码大小'
        ],
        'qrcode_content' => [
          'name'  => 'qrcode_content',
          'type'    => 'string',
          'min'     => '0',
          'default' => 'Hello qrcode',
          'require' => false,
          'desc'    => '二维码包含的内容'
        ],

        'wallpaper' => [
          'name'  => 'wallpaper',
          'type'    => 'string',
          'min'     => '0',
          'default' => '',
          'require' => false,
          'desc'    => '底图地址'
        ],

      ]
    ];
  }
}
