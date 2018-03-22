<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/19 0019
 * Time: 上午 9:48
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {
  
  public function scopeWithOnly($query, $relation, Array $columns)
  {
    return $query->with([$relation => function ($query) use ($columns){
      $query->select(array_merge(['id'], $columns));
    }]);
  }
}
