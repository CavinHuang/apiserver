<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/8 0008
 * Time: 下午 5:43
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController{
  
  
  protected $successStatus=200;
  
  public function upload(Request $request){
    $dir = $request->input('dir') ? $request->input('dir') : 'upload';
    $path = $dir.'/'.date('Ymd',time());
    $path = $request->file('file')->store($path, 'uploads');
    return response()->json(['status'=>2000,'success'=>'upload/'.$path], $this->successStatus);
  }
}
