<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/9 0009
 * Time: 下午 2:07
 */

namespace App\Models;

class User extends BaseModel {
  /**
   * 表名
   * @var string
   */
  protected $table = 'users';
  public $timestamps = true;
  
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'id', 'name', 'email', 'password','userimg', 'created_at', 'updated_at'
  ];
}
