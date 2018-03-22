<?php
namespace App\Services\ApiServer\Response;

/**
 * api测试类
 * @author Flc <2016-7-31 13:44:07>
 */
class Demo extends BaseResponse implements InterfaceResponse
{
    /**
     * 接口名称
     * @var string
     */
    protected $method = 'demo';
  
  /**
   * 接口基础运行
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
          'current_time' => date('Y-m-d H:i:s')
        ]
      ];
    }
  
  /**
   * 批量获取用户基本信息
   * @method Demo.getUserInfo
   * @desc 用于获取多个用户基本信息
   * @return int code 操作码，0表示成功
   * @return array list 用户列表
   * @return int list.id 用户ID
   * @return string list.name 用户名字
   * @return string list.note 用户来源
   * @return string msg 提示信息
   */
    public function getUserInfo(){
      return [
        'status'=> 'success',
        'code'=> 200,
        'data' => [
          'current_time' => date('Y-m-d H:i:s')
        ]
      ];
    }
  
  /**
   * 接口参数
   * @return array
   */
  public static function getRules() {
    return [
      'getUserInfo' => [
        'userId' => [
          'name'    => 'user_id',
          'type'    => 'int',
          'min'     => 1,
          'require' => true,
          'desc'    => '用户ID'
        ],
      ],
      
      'run' => [
      ],
    ];
  }
}
