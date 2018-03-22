<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * DEBUG调试
 * @author xing <fbiufo@vip.qq.com>
 */
function show_bug($data){
    dump($data);
    exit;
}

/**
 * 得到标题
 * @author 木瓦 <fbiufo@vip.qq.com>
 */
function get_class_name($str){
    $target = '/@class\s(.*)\s/';
    $res = preg_match($target,$str,$result);
//    dump($result);
    if($res == 1){
        return $result[1];
    }
}
//* @title 功能说明
function get_title($str){
    $target = '/\* (.*) *?/';
    $res = preg_match($target,$str,$result);
    if($res == 1){
        return $result[1];
    }
}
//* @param age 否 int 年龄参数的说明
function get_param($str){
    $target = '/@param\s(.*)\s/';
    $res = preg_match_all($target,$str,$result);
    if($res){
        $new_result = array();
        foreach($result[1] as $k=>$v){
            $new_array = explode(' ',$v);
            if(is_array($new_array) && !empty($new_array)){
                $new_result[] = $new_array;
            }
        }
        return $new_result;
    }
}
//* @return  返回数据实例
function get_return($str){
    $target = '/@return\s(.*)\s/';
    $res = preg_match($target,$str,$result);
    if($res == 1){
        return $result[1];
    }
}
//* @example 调用示例
function get_example($str){
    $target = '/@example\s(.*)\s/';
    $res = preg_match($target,$str,$result);
    if($res == 1){
        return $result[1];
    }
}
//* @method POST
function get_method($str){
    $target = '/@method\s(.*)\s/';
    $res = preg_match($target,$str,$result);
    if($res == 1){
        return $result[1];
    }
}
/**
 * 功能说明
 * @author 木瓦 <fbiufo@vip.qq.com>
 */
function parsing($str){
    $target = '/\/\*[\s\S]*?\*\//';
    $res= preg_match_all($target,$str,$result);
    if($res){
        $new_result = array();
        foreach($result[0] as $k=>$v){
            if($k == 0){
                $new_result['title'] = get_class_name($v);
            }else{
                $new_result['api'][$k]['title']  = get_title($v);
                $new_result['api'][$k]['param']  = get_param($v);
                $new_result['api'][$k]['return'] = get_return($v);
                $new_result['api'][$k]['example']= get_example($v);
                $new_result['api'][$k]['method'] = get_method($v);
            }
        }
        return $new_result;
    }
}
