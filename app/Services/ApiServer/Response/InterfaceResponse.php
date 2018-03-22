<?php
namespace App\Services\ApiServer\Response;

/**
 * api接口类
 * @author Flc <2016-7-31 13:44:19>
 */
Interface InterfaceResponse
{
    /**
     * 执行接口
     * @return array 
     */
    public function run(&$params, $action);

    /**
     * 返回接口名称
     * @return string 
     */
    public function getMethod();
  
  /**
   * 接口参数配置
   * @methods
   * @desc
   * @author slide
   * @return mixed
   */
    public static function getRules();
}
