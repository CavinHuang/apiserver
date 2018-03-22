<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/15 0015
 * Time: 上午 11:04
 */

namespace App\Services\ApiServer\Response;

use Intervention\Image\Facades\Image;
use Log;
class Qrcode extends BaseResponse implements InterfaceResponse {

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
   * 生成普通二维码
   * @method Qrcode.createQrcode
   * @desc 生成带参数的普通二维码
   * @author slide
   * @param bool $rpc
   * @return resource result 图片资源
   */
  public function createQrcode(){
    $background_color = $this->getParams('background_color') !== '' ? $this->getParams('background_color'): "ffffff";
    $color = $this->getParams('color') !== '' ? $this->getParams('color') : "000000";
    $margin = $this->getParams('margin', 'int') !== 0 ? $this->getParams('margin', 'int') : 1;
    $size = $this->getParams('size', 'int') != 0 ? $this->getParams('size', 'int') : 10; //生成图片大小 ：1到10
    $content = $this->getParams('content', 'string');
    $opcity = $this->getParams('opcity', 'int');
    $logo = $this->getParams('logo_url', 'string');
    $logo_size = $this->getParams('logo_size', 'string') != '' ? $this->getParams('logo_size', 'size') : '152,104';

    require_once(app_path()."/Services/Libs/Phpqrcode/phpqrcode.php");
    $query_str = parse_url(urldecode($content));

    if(isset($query_str['query'])){
      parse_str($query_str['query'], $output);
      // $output['uid'] = urlencode($output['uid']);

      $newQuery = http_build_query($output);
      $query_str['query'] = $newQuery;
      $content = $query_str['scheme'].'://'.$query_str['host'].$query_str['path'].'?'.$query_str['query'];
    }


    $errorCorrectionLevel = "M"; // 纠错级别：L、M、Q、H
    $result = \QRcode::png($content, false, $errorCorrectionLevel, $size, $margin, false, '#'.$color, '#'.$background_color, $opcity);

    $Img = Image::make($result);

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
      $Img->insert($logo,'center');
    }

    Log::error('qrcode size'.$size);
    Log::error('params'.var_export($this->parmas, true));
    Log::error('params size'.var_export($this->getParams('size', 'int'), true));

    if($this->getParams('rpc', 'boolean')){
      Log::error('qrcode result'.var_export($result, true));
      return $result;
    }else{
      // imagepng($result);
      // imagedestroy($result);
      // return response('png')->header('Content-type', 'image/png');
      return $Img->response('png');
    }
  }

  /**
   * @methods
   * @desc
   * @author slide
   * @return array
   *
   */
  public static function getRules () {
    // TODO: Implement getRules() method.
    return [
      'createQrcode' => [
        'background_color' => [
          'name'  => 'background_color',
          'type'    => 'string',
          'min'     => '',
          'default' => 'ffffff',
          'require' => false,
          'desc'    => '二维码背景颜色'
        ],
        'color' => [
          'name'  => 'color',
          'type'    => 'string',
          'min'     => '',
          'default' => '000000',
          'require' => false,
          'desc'    => '二维码线条颜色'
        ],
        'margin' => [
          'name'  => 'margin',
          'type'    => 'int',
          'min'     => '0',
          'default' => '0',
          'require' => false,
          'desc'    => '二维码间隔'
        ],
        'size' => [
          'name'  => 'size',
          'type'    => 'int',
          'min'     => '0',
          'default' => '400',
          'require' => false,
          'desc'    => '二维码大小'
        ],
        'content' => [
          'name'  => 'content',
          'type'    => 'string',
          'min'     => '0',
          'default' => '',
          'require' => true,
          'desc'    => '二维码包含的内容'
        ],
        'opcity' => [
          'name'  => 'opcity',
          'type'    => 'boolean',
          'min'     => '0',
          'default' => '0',
          'require' => false,
          'desc'    => '是否需要透明底部'
        ],
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
      ]
    ];
  }
}
