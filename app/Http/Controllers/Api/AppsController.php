<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/8 0008
 * Time: 下午 2:39
 */

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\AppLogs;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class AppsController extends Controller {

  public $successStatus = 200;

  /**
   * 个人app列表
   * @author: slide
   */
  public function app_lists (Request $request) {
    $data = $request->all();
    if(!isset($data['userId']) || $data['userId'] == ''){
      return response()->json(['error'=> '参数错误'], 401);
    }
    $app = (new App())->where('user_id', $data['userId'])->get();
    return response()->json(['status'=>2000,'success'=>$app], $this->successStatus);
  }

  /**
   * 创建app
   * @author: slide
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function save_apps(Request $request){
    $validator = Validator::make($request->all(), [
      'app_name' => 'required',
      'app_secret' => 'required',
      'user_id' => 'required'
    ]);

    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()], 401);
    }

    $input = $request->all();
    $input['app_id'] = careateTicket('TY');
    if(isset($input['id'])){
      $result = (new App())->where('id', $input['id'])->update($input);
    }else{
      $result = App::create($input);
    }

    return response()->json(['status'=>2000,'success'=>$result], $this->successStatus);
  }

  /**
   * 删除应用
   * @author: slide
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   *
   */
  public function delete_apps(Request $request){
    if(!$request->input('id')){
      return response()->json(['error'=>'缺少必要参数'], 503);
    }

    $result = (new App())->where('id', $request->input('id'))->delete();

    return response()->json(['status'=>2000,'success'=>$result], $this->successStatus);
  }

  /**
   * 统计今日访问数据
   * @methods
   * @desc
   * @author slide
   *
   */
  public function countToday(){
    $user = Auth::user();
    $appMdl = App::where(['user_id'=>$user['id']])->select('app_id')->get();

    $app_ids = array_column($appMdl->toArray(), 'app_id');

    // 统计访问
    $time = date('Y-m-d 0:0:0', time());
    $appLogMdl = AppLogs::whereIn('app_id', $app_ids)->whereBetween('created_at', [$time, date('Y-m-d H:i:s', time())])->get();

    $methods = [];
    $data = [];

    foreach ($appLogMdl->toArray() as $k => $v){

      $class = explode('.',$v['api_names']);

      if(!in_array(config('methods.'.$class[0])[$v['api_names']], $methods)){
        array_push($methods, config('methods.'.$class[0])[$v['api_names']]);
      }
      $index = array2haskey($data, 'name', config('methods.'.$class[0])[$v['api_names']]);
      if($index !== false){
        $data[$index]['value'] ++;
        continue;
      }else{
        $temp = [
          'value' => 1,
          'name' => config('methods.'.$class[0])[$v['api_names']]
        ];
        $data[] = $temp;
      }
    }


    return response()->json(['status'=>2000,'success'=>['methods' => $methods, 'data' => $data]], $this->successStatus);
  }

  /**
   * 访问日志返回
   * @methods
   * @desc
   * @author slide
   * @return \Illuminate\Http\JsonResponse
   *
   */
  public function appLogs(){

    $pageSize = request()->input('pageSize') ? request()->input('pageSize') : 15;
    $user = Auth::user();

    $appMdl = App::where(['user_id'=>$user['id']])->select('app_id')->get();

    $app_ids = array_column($appMdl->toArray(), 'app_id');

    $appLogMdl = AppLogs::whereIn('app_id', $app_ids)->orderBy('id', 'desc')->paginate($pageSize);

    $data = [];
    $arr = $appLogMdl->toArray();
    foreach ($arr['data'] as $k => $v){
      $class = explode('.', $v['api_names']);
      $names = config('methods.'.ucfirst($class[0]))[ucfirst($v['api_names'])];
      $v['names'] = $names;
      $data[] = $v;
    }
     // $appLogMdl->setItems($data);
    $arr['data'] = $data;

    return response()->json(['status'=>2000,'success'=>$arr], $this->successStatus);
  }
}
