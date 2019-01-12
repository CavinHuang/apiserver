<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
    /**
     * @author xing <fbiufo@vip.qq.com>
     */
    public function index()
    {
        //header('content-type:text/html;charset=utf-8');
        $path = '../../app/Services/ApiServer/Response/*.php';
        $res = glob($path);
        $barringFile = ['BaseResponse.php', 'InterfaceResponse.php'];

      $result = array();
        if($res){
            foreach($res as $k=>$v){
              $fileArr = explode('/', $v);
              if(in_array($fileArr[count($fileArr) - 1], $barringFile)) {
                continue;
              }
              $str = file_get_contents($v);
               $result[] = parsing($str);
            }
        }
//        var_export($result);exit;
        $this->assign('data',$result);
        return $this->fetch();
    }
}
