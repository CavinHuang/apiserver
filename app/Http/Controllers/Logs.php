<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/9 0009
 * Time: 下午 9:35
 */

namespace App\Http\Controllers;


use App\Models\ApiCount;
use App\Models\AppLogs;
use Validator;

class Logs {

  protected static $appLogoMdl = NULL;

  protected static $apiCountMdl = NULL;

  public function __construct(){
    self::$appLogoMdl = is_null(self::$appLogoMdl) ? (new AppLogs()) : self::$appLogoMdl;
    self::$apiCountMdl = is_null(self::$apiCountMdl) ? (new ApiCount()) : self::$apiCountMdl;
  }

  /**
   * 日志总写入口
   * @author: slide
   * @param $type     计数或者写日志
   * @param $data     数据
   * @param $status   日志状态
   *
   */
  public static function write($data, $status, $note = 'success'){
    self::saveCount($data, $status);
    $data['status'] = $status == 'success' ? 1 : 0;
    $data['note'] = $note;
    self::saveAppLogs($data);
  }

  /**
   * 保存app日志
   * @author: slide
   * @param $data
   * @return bool
   *
   */
  public static function saveAppLogs($data){

    $validator = Validator::make($data, [
      'api_names' => 'required',
      'ip' => 'required',
      'app_id' => 'required',
      'status' => 'required',
      'note' => 'required'
    ]);
    self::$appLogoMdl = is_null(self::$appLogoMdl) ? (new AppLogs()) : self::$appLogoMdl;

    if ($validator->fails()) {
      $data['note'] = $validator->errors();
      $result = self::$appLogoMdl->create($data);
    }

    $result = self::$appLogoMdl->create($data);

    if($result){
      return true;
    }else{
      return false;
    }
  }

  /**
   * 记录api调用次数
   * @author: slide
   * @param int $id
   * @return bool
   *
   */
  public static function saveCount($data = [], $field='success'){
    if(!$data){
      return false;
    }
    self::$apiCountMdl = is_null(self::$apiCountMdl) ? (new ApiCount()) : self::$apiCountMdl;

    $res = self::$apiCountMdl->where(['app_id'=> $data['app_id'], 'api_names' => $data['api_names']])->limit(1)->get();
    if(count($res->all()) > 0){
      $result = self::$apiCountMdl->where('app_id', $data['app_id'])->increment($field, 1);
    }else{
      if($field == 'success'){
        $data['success']  = 1;
        $data['error'] = 0;
      }else{
        $data['success']  = 0;
        $data['error'] = 1;
      }
      // $field == 'success' ? $data['success'] = 1 : $data['error'] = 1;
      $result = self::$apiCountMdl->create($data);
    }

    return $result;
  }
}
