<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/9 0009
 * Time: 下午 9:32
 */

namespace App\Models;

class AppLogs extends BaseModel {
  /**
   * 表名
   * @var string
   */
  protected $table = 'app_logs';
  const CREATED_AT = 'created_at';
  const UPDATED_AT = 'updated_at';
  public $timestamps = true;
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'app_id', 'api_names','ip', 'status', 'note','created_at', 'updated_at'
  ];
  
  /*public function getApiNamesAttribute($value)
  {
  }*/
}
