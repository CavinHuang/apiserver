<?php
/*************************************************
 *                  light PHP                    *
 *                                               *
 *    A Lightweight Full-Stack PHP Framework     *
 *                                               *
 *                  cavinHuang                   *
 *        <https://github.com/TIGERB>            *
 *                                               *
 *************************************************/

namespace App\Services\ApiServer\Response;


class Ip extends BaseResponse implements InterfaceResponse{

  /**
   * 接口基础运行返回控制器数据
   * @author slide
   * @param $params
   * @param $action
   * @return array data 数据
   */
  public function run (&$params, $action) {
    return [
      'status' => true,
      'code'   => '200',
      'data'   => [
        'controller' => 'Ip',
        'apis' => [
          'getIp',
        ]
      ]
    ];
  }

  /**
   * 获取IP和地区
   * @return \Illuminate\Http\JsonResponse
   * @author cavinHUang
   * @date   2018/7/3 0003 下午 4:13
   *
   */
  public function getIp(){
    $clientIP = getIPaddress();
    $taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$clientIP;
    $IPinfo = json_decode(file_get_contents($taobaoIP));

    return $this->ajax(200, 'success', '获取IP成功', $IPinfo->data);
  }

  public static function getRules () {
    return [
      'getIp' => []
    ];
  }
}
