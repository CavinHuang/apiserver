<?php
namespace App\Services\ApiServer;

use App\Http\Controllers\Logs;
use Request;
use Validator;
use App\Models\App;
/**
 * API服务端总入口
 * @author Flc <2016-7-31 11:27:09>
 */
class Server
{
    /**
     * 请求参数
     * @var array
     */
    protected $params = [];

    /**
     * API请求Method名
     * @var string
     */
    protected $method;

    /**
     * app_id
     * @var string
     */
    protected $app_id;

    /**
     * app_secret
     * @var string
     */
    protected $app_secret;

    /**
     * 回调数据格式
     * @var string
     */
    protected $format = 'json';

    /**
     * 签名方法
     * @var string
     */
    protected $sign_method = 'md5';

    /**
     * 是否输出错误码
     * @var boolean
     */
    protected $error_code_show = false;

    /**
     * 初始化
     * @param Error $error Error对象
     */
    public function __construct(Error $error)
    {
      $params = Request::all();
      if(isset($params['s'])) {
        unset($params['s']);
      }
      $this->params = $params;
      // dump($params);
      $this->error  = $error;
    }

    /**
     * api服务入口执行
     * @param  Request $request 请求参数
     * @return [type]           [description]
     */
    public function run()
    {
        // A.1 初步校验
        $rules    = [
            'app_id'      => 'required',
            'method'      => 'required',
            'format'      => 'in:,json',
            'sign_method' => 'in:,md5',
            'nonce'       => 'required|string|min:1|max:32|',
            'sign'        => 'required',
        ];
        $messages = [
            'app_id.required' => '1001',
            'method.required' => '1003',
            'format.in'       => '1004',
            'sign_method.in'  => '1005',
            'nonce.required'  => '1010',
            'nonce.string'    => '1011',
            'nonce.min'       => '1012',
            'nonce.max'       => '1012',
            'sign.required'   => '1006'
        ];

        $v = Validator::make($this->params, $rules, $messages);

        if ($v->fails()) {
            return $this->response(['status' => false, 'code' => $v->messages()->first()], []);
        }

        // A.2 赋值对象
        $this->format      = !empty($this->params['format']) ? $this->params['format'] : $this->format;
        $this->sign_method = !empty($this->params['sign_method']) ? $this->params['sign_method'] : $this->sign_method;
        $this->app_id      = $this->params['app_id'];
        $this->method      = $this->params['method'];

        $data = [
          'app_id' => $this->app_id,
          'api_names' => $this->method,
          'ip'  => getRealIp()
        ];

        // B. appid校验
        $app = App::where('app_id',$this->app_id)->first();
        //dd($app);
        if (! $app)
            return $this->response(['status' => false, 'code' => '1002'], $data);

        $this->app_secret = $app->app_secret;

        // C. 校验签名
        $signRes = $this->checkSign($this->params);
        if (! $signRes || ! $signRes['status']) {
            return $this->response(['status' => false, 'code' => $signRes['code']], $data);
        }

        // D. 校验接口名
        // D.1 通过方法名获取类名
        $className = self::getClassName($this->method);
        if(isset($className['module'])){
          $classPath = __NAMESPACE__ . '\\Response\\'.$className['module'].'\\' . $className['controller'];
        }elseif(isset($className['controller'])){
          $classPath = __NAMESPACE__ . '\\Response\\'.$className['controller'];
        }else{
          $classPath = __NAMESPACE__ . '\\Response\\' . $className['action'];
        }

        // D.2 判断类名是否存在
        if (!$className || !class_exists($classPath)) {
            return $this->response(['status' => false, 'code' => '1009'], $data);
        }

        // D.3 判断方法是否存在
        if (! method_exists($classPath, $className['action'])) {
            return $this->response(['status' => false, 'code' => '1008'], $data);
        }

        $this->classname = $classPath;

        // E. api接口分发
        $class = new $classPath($className['action'], $this->params);
        if(isset($this->params['resource'])){
          Logs::write($data, 'success','资源调用成功');
          return $class->request_result;
        }else{
          return $this->response((array) $class->request_result, $data);
        }
    }

    /**
     * 校验签名
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    protected function checkSign($params)
    {
        $sign = array_key_exists('sign', $params) ? $params['sign'] : '';

        if (empty($sign))
            return array('status' => false, 'code' => '1006');

        unset($params['sign']);
        if ($sign != $this->generateSign($params))
            return array('status' => false, 'code' => '1007');

        return array('status' => true, 'code' => '200');
    }

    /**
     * 生成签名
     * @param  array $params 待校验签名参数
     * @return string|false
     */
    protected function generateSign($params)
    {
        if ($this->sign_method == 'md5')
            return $this->generateMd5Sign($params);

        return false;
    }

    /**
     * md5方式签名
     * @param  array $params 待签名参数
     * @return string
     */
    protected function generateMd5Sign($params)
    {
        ksort($params);

        $tmps = array();
        foreach ($params as $k => $v) {
            $tmps[] = $k . urldecode($v);
        }

        $string = $this->app_secret . implode('', $tmps) . $this->app_secret;
        $string = str_replace('%', '%25', $string);
        return strtoupper(md5($string));
    }


    /**
     * 通过方法名转换为对应的类名
     * @param  string $method 方法名
     * @return string|false
     */
    protected function getClassName($method)
    {
        $methods = explode('.', $method);

        if (!is_array($methods))
            return false;

        $tmp = array();
        foreach ($methods as $value) {
            $tmp[] = $value;
        }

        // modules.controller.action
        $methods = [];
        if(count($tmp) === 3){
          $methods['module'] = ucwords($tmp[0]);
          $methods['controller'] = ucwords($tmp[1]);
          $methods['action'] = $tmp[2];
        }elseif(count($tmp) === 2){
          $methods['controller'] = ucwords($tmp[0]);
          $methods['action'] = $tmp[1];
        }elseif(count($tmp) === 1){
          $methods['action'] = ucwords($tmp[0]);
        }

        // $className = implode('', $tmp);
        return $methods;
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

        if(isset($result['code']) && is_numeric($result['code']) && !empty($data)){
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
     * 返回错误内容
     * @param  string $code 错误码
     * @return string
     */
    protected function getError($code)
    {
        return $this->error->getError($code, $this->error_code_show);
    }
}
