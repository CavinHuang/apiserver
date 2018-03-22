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

      $result = array();
        if($res){
            foreach($res as $k=>$v){
                $str = file_get_contents($v);
                $result[] = parsing($str);
            }
        }
//        var_export($result);exit;
        $this->assign('data',$result);
        return $this->fetch();
    }
}
