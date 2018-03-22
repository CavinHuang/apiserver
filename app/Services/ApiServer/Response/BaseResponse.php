<?php
namespace App\Services\ApiServer\Response;

/**
 * api基础类
 * @author Flc <2016-7-31 13:44:07>
 */
abstract class BaseResponse
{
  
    protected $parmas;
    public $request_result = [];
  
    public function __construct ($action, $params = '') {
      $this->parmas = $params;
      $this->request_result = $this->$action();
      $this->_initialiaze();
    }
  
  /**
   * 初始化发方法
   * @methods
   * @desc
   * @author slide
   *
   */
    protected function _initialiaze(){}
  
  /**
   * 获取一个参数
   * @author: slide
   * @param $key    参数
   * @param $type   参数的类型
   * @return array|string
   *
   */
    public function getParams ($key, $type = 'string') {
      switch ($type){
        case 'string':
          $res = '';
          break;
        case 'array':
          $res = [];
          break;
        case 'int':
          $res = 0;
          break;
        case 'date':
          $res = time();
          break;
        case 'boolean':
          $res = false;
          break;
      }
      return isset($this->parmas[$key]) ? $this->parmas[$key] : $res;
    }
  
  /**
     * 接口名称
     * 
     * @var [type]
     */
    protected $method;

    /**
     * 返回接口名称
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }
  
  /**
   * 输出结果
   * @param  array $result 结果
   * @return response
   */
  protected function response(array $result, array $data)
  {
    if (! array_key_exists('msg', $result) && array_key_exists('code', $result)) {
      $result['msg'] = $this->getError($result['code']);
    }
    
    if(isset($result['code']) && is_numeric($result['code'])){
      if($result['code'] == 200){
        Logs::write($data, 'success', $result['msg']);
      }else{
        Logs::write($data, 'error', $result['msg']);
      }
    }
    
    if ($this->format == 'json') {
      return response()->json($result);
    }
    
    return false;
  }
  
  /**
   * 返回ajax
   * @author: slide
   * @param int    $code
   * @param int    $status
   * @param string $msg
   * @param array  $result
   * @return \Illuminate\Http\JsonResponse
   *
   */
    public function ajax($code = 200,$status=2000, $msg = '', $result = []){
      $data = [
        'code' => $code,
        'status' => $status,
        'msg'  => $msg,
        'server_time' => time(),
        'result' => $result
      ];
      return $data;
    }
  
  /**
   * 捕捉错误并返回错误
   * @author: slide
   * @param $result
   * @return \Illuminate\Http\JsonResponse
   *
   */
    public function returnResult($result){
      $contents = json_decode($result, true);
      if (isset($contents['errcode']) && 0 !== $contents['errcode']) {
        if (empty($contents['errmsg'])) {
          $contents['errmsg'] = 'Unknown';
        }
      }
      return $this->ajax(600001, 'error', $contents['errmsg'], $contents);
    }
}
