<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/9 0009
 * Time: 下午 9:34
 */

namespace App\Models;

class ApiCount extends BaseModel {
  /**
   * 表名
   * @var string
   */
  protected $table = 'api_count';
  public $timestamps = true;
  
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'app_id', 'api_names','ip', 'success', 'error','created_at', 'updated_at'
  ];
}
