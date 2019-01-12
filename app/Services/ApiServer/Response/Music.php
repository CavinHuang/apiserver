<?php
/**
 * @class 音乐接口
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/24 0024
 * Time: 下午 5:26
 */

namespace App\Services\ApiServer\Response;

class Music extends BaseResponse implements InterfaceResponse{

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
        'controller' => 'Music',
        'apis' => [
          'createQrcode',
        ]
      ]
    ];
  }

  /**
   * 获取榜单列表
   * @method Music.getTopList
   * @desc 获取榜单列表
   * @author slide
   * @return array result 成功返回获取榜单列表返回错误信息
   */
  public function getTopList(){
    $url = 'https://c.y.qq.com/v8/fcg-bin/fcg_v8_toplist_opt.fcg?page=index&format=html&tpl=macv4&v8debug=1&jsonCallback=jsonCallback';

    $result = httpRequest($url);

    $jsonpData = substr($result[1], 0, strlen($result[1]) - 1);
    $arrayStr = substr($jsonpData, 14);

    $res = json_decode($arrayStr, true);

    $result = [];

    foreach ($res as $key => $val) {
      $tmp_arr['GroupID'] = $val['GroupID'];
      $tmp_arr['GroupName'] = $val['GroupName'];
      foreach ($val['List'] as $k => $v) {
        $short_name = explode('·', $v['ListName']);
        $tmp_arr['List'][$k]['short_name'] = $short_name[count($short_name) - 1];
        $tmp_arr['List'][$k]['ListName'] = $v['ListName'];
        $tmp_arr['List'][$k]['pic'] = $v['MacDetailPicUrl'];
        $tmp_arr['List'][$k]['listennum'] = $v['listennum'];
        $tmp_arr['List'][$k]['showtime'] = $v['showtime'];
        $tmp_arr['List'][$k]['topID'] = $v['topID'];
        $tmp_arr['List'][$k]['type'] = $v['type'];
        $tmp_arr['List'][$k]['update_key'] = $v['update_key'];
      }
      $result[] = $tmp_arr;
    }

    return $this->ajax(200, 'success', '获取歌曲榜单列表成功', $result);
  }

  /**
   * 获取榜单歌曲列表
   * @method Music.getSongList
   * @desc 获取榜单歌曲列表
   * @author slide
   * @return array result 成功返回榜单歌曲列表返回错误信息
   */
  public function getSongList(){
    $topId = $this->getParams('topId', 'string');
    $songBegin = $this->getParams('songBegin', 'int');
    $songNum = $this->getParams('songNum', 'int') ? $this->getParams('songNum', 'int') : 30;
    $date = date('Y-m-d', strtotime('-1 day'));
    $url = "https://c.y.qq.com/v8/fcg-bin/fcg_v8_toplist_cp.fcg?tpl=3&page=detail&date={$date}&topid={$topId}&type=top&song_begin={$songBegin}&song_num={$songNum}&g_tk=5381&jsonpCallback=MusicList&loginUin=0&hostUin=0&format=jsonp&inCharset=utf8&outCharset=utf-8";

    $result = httpRequest($url);

    $jsonpData = substr($result[1], 0, strlen($result[1]) - 1);
    $arrayStr = substr($jsonpData, 11);

    $dataArr = json_decode($arrayStr, true);

    $responeRes = [];

    $responeRes['cur_song_num'] = $dataArr['cur_song_num'];
    $responeRes['date'] = $dataArr['date'];
    $responeRes['song_begin'] = $dataArr['song_begin'];
    $responeRes['total_song_num'] = $dataArr['total_song_num'];

    foreach ($dataArr['songlist'] as $k => $v) {

      $responeRes['songlist'][$k]['album']['desc'] = $v['data']['albumdesc'];
      $responeRes['songlist'][$k]['album']['id'] = $v['data']['albumid'];
      $responeRes['songlist'][$k]['album']['mid'] = $v['data']['albummid'];
      $responeRes['songlist'][$k]['album']['name'] = $v['data']['albumname'];
      $responeRes['songlist'][$k]['album']['pic'] = '//y.gtimg.cn/music/photo_new/T002R90x90M000'.$v['data']['albummid'].'.jpg?max_age=2592000';
      $responeRes['songlist'][$k]['songid'] = $v['data']['songid'];
      $responeRes['songlist'][$k]['songmid'] = $v['data']['songmid'];
      $responeRes['songlist'][$k]['songname'] = $v['data']['songname'];
      $responeRes['songlist'][$k]['strMediaMid'] = $v['data']['strMediaMid'];
      $responeRes['songlist'][$k]['vid'] = $v['data']['vid'];
      $responeRes['songlist'][$k]['singer'] = $v['data']['singer'];

    }
    return $this->ajax(200, 'success', '获取歌曲列表成功', $responeRes);
  }

  /**
   * 搜索音乐
   * @method Music.search_music
   * @desc 搜索音乐
   * @author slide
   * @return array result 成功返回获取歌曲搜索音乐失败返回错误信息
   */
  public function search_music () {
    $p = $this->getParams('p', 'init') ? $this->getParams('p', 'init') : 1; // 页码
    $n = $this->getParams('n', 'init') ? $this->getParams('n', 'init') : 20; // 每页多少条
    $keyword = $this->getParams('keyword', 'string') ? $this->getParams('keyword', 'string') : ''; // 每页多少条

    $url = "https://c.y.qq.com/soso/fcgi-bin/client_search_cp?new_json=1&remoteplace=txt.yqq.song&p={$p}&n={$n}&w={$keyword}&jsonpCallback=Musicurl&loginUin=0&hostUin=0&format=jsonp&inCharset=utf8&outCharset=utf-8&platform=yqq";

    $url = "https://c.y.qq.com/soso/fcgi-bin/client_search_cp?ct=24&qqmusic_ver=1298&new_json=1&remoteplace=txt.yqq.center&searchid=52836159427565787&t=0&aggr=1&cr=1&catZhida=1&lossless=0&flag_qc=0&p={$p}&n={$n}&w={$keyword}&g_tk=5381&jsonpCallback=Musicurl&loginUin=0&hostUin=0&format=jsonp&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq&needNewCode=0";

    $result = httpRequest($url);

    $jsonpData = substr($result[1], 0, strlen($result[1]) - 1);
    $arrayStr = substr($jsonpData, 9);

    $dataArr = json_decode($arrayStr, true);

    if ($dataArr['code'] == 0) {
      $data = $dataArr['data']['song'];
      return $this->ajax(200, 'success', '获取歌曲成功', $data);
    } else {
      return $this->ajax(404, 'not found');
    }
  }

  /**
   * 获取歌曲播放链接
   * @method Music.getMusicUrl
   * @desc 获取歌曲播放链接
   * @author slide
   * @return array result 成功返回获取歌曲播放链接返回错误信息
   */
  public function getMusicUrl() {
    $songmid = $this->getParams('songmid', 'string');
    $url = "https://c.y.qq.com/base/fcgi-bin/fcg_music_express_mobile3.fcg?g_tk=5381&jsonpCallback=Musicurl&loginUin=0&hostUin=0&format=json&inCharset=utf8&outCharset=utf-8&notice=0&platform=yqq&needNewCode=0&cid=205361747&callback=Musicurl&uin=0&songmid={$songmid}&filename=C400{$songmid}.m4a&guid=8905057784";

    $result = httpRequest($url);

    $jsonpData = substr($result[1], 0, strlen($result[1]) - 1);
    $arrayStr = substr($jsonpData, 9);

    $dataArr = json_decode($arrayStr, true);

    // http://dl.stream.qqmusic.qq.com/C400003tJCdP4QXEe8.m4a?vkey=DCE6B92071A7E7DE1FA09DBD3D50BFA2A7F95DA1535AD353B4CBC16740102BF7D77A24421DEAC60A496677477CCF17B6FF603408B34A24E8&guid=8905057784&uin=0&fromtag=66

    $info = $dataArr['data']['items'][0];

    $url = 'http://dl.stream.qqmusic.qq.com/'.$info['filename'].'?vkey='.$info['vkey'].'&guid=8905057784&uin=0&fromtag=66';

    return $this->ajax(200, 'success', '获取歌曲成功', $url);

  }

  /**
   * 接口参数
   * @return array
   */
  public static function getRules () {
    return [
      'getTopList' => [],
      'getSongList' => [
        'topId' => [
          'name'    => 'topId',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '获取歌曲列表',
          'require' => true
        ],
        'songBegin' => [
          'name' => 'songBegin',
          'type' => 'int',
          'min'  => '',
          'default' => 0,
          'require' => false,
          'desc' => '获取歌曲列表'
        ],
        'songNum' => [
          'name' => 'songNum',
          'type' => 'int',
          'min'  => 0,
          'default' => 30,
          'desc' => '每次获取多少条信息',
          'require' => false
        ]
      ],
      'getMusicUrl' => [
        'songmid' => [
          "name" => 'songmid',
          'type' => 'string',
          'min'  => '',
          'require' => true,
          'desc' => '歌曲mid',
        ]
      ]
    ];
  }
}
