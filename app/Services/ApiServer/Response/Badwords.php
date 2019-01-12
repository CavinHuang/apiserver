<?php
/**
 * @class 敏感词过滤
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/11 0011
 * Time: 下午 4:22
 */

namespace App\Services\ApiServer\Response;

use Illuminate\Support\Facades\Redis;

class Badwords extends BaseResponse implements InterfaceResponse {

  protected $method = 'Badwords';

  /**
   * 接口基础运行返回控制器数据
   * @author: slide
   * @param $params
   * @param $action
   * @return array data 数据
   */
  public function run(&$params, $action)
  {
    return [
      'status' => true,
      'code'   => '200',
      'data'   => [
        'controller' => 'Badwords',
        'apis' => [
          'getBadWords',
          'setBadWords',
          'checkBadWords',
          'checkArticle'
        ]
      ]
    ];
  }

  /**
   * 添加新的敏感词汇
   * @desc 统一添加新的敏感词汇
   * @method Badwords.setBadWords
   * @author: slide
   * @param array $badwords
   * @return Boolean result true/false
   */
  public function setBadWords($params = []){
    $badwords = isset($params['badwords']) ? $params['badwords'] : [];
    $badwords_old = $this->getBadWords();

    foreach ($badwords as $k => $v){
      if(!in_array($v, $badwords_old)){
        array_push($badwords_old, $v);
      }
    }
    $config = <<<str
<?php

return
str;
    $config.=var_export($badwords_old, true).';';

    $result = file_put_contents(CONFIG_PATH.'/badwords.php', $config);

    $res = Redis::set('badwords', json_encode($badwords_old, JSON_UNESCAPED_UNICODE));

    return $result && $res;
  }

  /**
   * 获取所有的敏感词汇
   * @method Badwords.getBadWords
   * @desc 获取敏感词汇库所有的词汇
   * @author: slide
   * @return array result 返回所有的敏感词汇
   */
  public function getBadWords($params = []){
    $format = isset($params['format']) ? $params['format'] : 'array';
    $badwords = Redis::get('badwords');

    if($format == 'json'){
      return $this->ajax(200, 'success', '获取词汇列表成功', $badwords);
    }

    return $badwords;
  }

  /**
   * 检测提交的词那些是敏感词汇
   * @method Badwords.checkBadwords
   * @desc 检测数组的元素，哪些包含敏感词汇
   * @author: slide
   * @param array $words
   * @return array result 包含铭感词汇的项
   *
   */
  public function checkBadWords($params = []){
    $words = isset($params['words']) ? $params['words'] : [];
    $result = [];
    $badwords = $this->getBadWords();
    foreach ($words as $k => $v){
      $tmp = str_split($v);
      $temp = array_intersect($badwords, $tmp);
      if(count($temp) > 0){
        $result[] = $v;
      }
    }
    return $result;
  }

  /**
   * 检测一段文本中包含的敏感词汇
   * @method Badwords.checkArticle
   * @desc 检测一段文本中包含的敏感词汇
   * @author: slide
   * @param string $text
   * @return array $result 包含的敏感词
   */
  public function checkArticle($res){
    $user = isset($res['text']) ? $res['text'] : '';
    $badwords = $this->getBadWords();
    $hei = json_decode($badwords, true);
    $response_result = [];
    //把$arr的数组元素和客户端提交的信息进行对比，便于发现有无敏感词
    foreach ($hei as $word)
    {
      $preg_letter = '/^[A-Za-z] $/';
      if (preg_match($preg_letter, $user))
      {//匹配中文
        $str = strtolower($user);
        $pattern_1 = '/([^A-Za-z] ' . $word . '[^A-Za-z] )|([^A-Za-z] ' . $word . '\s )|(\s ' . $word . '[^A-Za-z] )|(^' . $word . '[^A-Za-z] )|([^A-Za-z] ' . $word.'$)/';
        //敏感词两边不为空
        if (preg_match($pattern_1, $str, $result))
        {
          $response_result[] = $result[0];
          continue;
        }
        $pattern_2 = '/(^' . $word . '\s )|(\s ' . $word . '\s )|(\s ' . $word . '$)|(^' . $word . '$)/';
        //敏感词两边可以为空格
        if (preg_match($pattern_2, $str, $result))
        {
          $response_result[] = $result[0];
          continue;
        }
      }
      else
      {//匹配英文字符串，大小写不敏感
        $pattern = '/\s*' . $word . '\s*/';
        if (preg_match($pattern, $user, $result))
        {
          $response_result[] = $result[0];
          continue;
        }
      }
    }
    return $this->ajax(200, 'success', '检测完成', $response_result);
  }

  /**
   * 接口参数
   * @return array
   */
  public static function getRules() {
    return [
      'setBadWords' => [
        'badwords' => [
          'name'    => 'badwords',
          'type'    => 'array',
          'min'     => '',
          'default' => '',
          'require' => true,
          'desc'    => '需要添加的词汇'
        ],
      ],
      'getBadWords' => [
        'format' => [
          'name'    => 'format',
          'type'    => 'string',
          'min'     => '',
          'default' => 'array',
          'require' => true,
          'desc'    => '需要返回的格式，支持数组（array）/ json'
        ],
      ],
      'checkBadWords' => [
        'words' => [
          'name'    => 'words',
          'type'    => 'array',
          'min'     => '',
          'default' => '',
          'require' => true,
          'desc'    => '需要检测的数组'
        ],
      ],
      'checkArticle' => [
        'text' => [
          'name'    => 'text',
          'type'    => 'string',
          'min'     => '',
          'default' => '',
          'require' => true,
          'desc'    => '需要检测的文本'
        ],
      ],
      'run' => [],
    ];
  }
}
