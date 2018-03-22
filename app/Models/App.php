<?php

namespace App\Models;

/**
 * Apps应用表模型
 *
 * @author Flc <2016-8-1 10:22:05>
 */
class App extends BaseModel
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'api_apps';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'app_id', 'user_id','app_secret', 'app_name', 'app_thumb','app_desc', 'status', 'created_at', 'updated_at'
    ];
    
    

}
