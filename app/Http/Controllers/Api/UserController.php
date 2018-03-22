<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User as UserModel;

class UserController extends Controller
{

  public $successStatus = 200;

  /**
   * login api
   *
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request){
    if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
      $user = Auth::user();
      $success['token'] =  $user->createToken('MyApp')->accessToken;
      $success['user'] = $user;
      return response()->json(['status'=>2000,'success' => $success], $this->successStatus);
    }
    else{
      return response()->json(['status'=>4000,'error'=>'Unauthorised'], 401);
    }
  }

  /**
   * Register api
   *
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request)
  {
    // dump($request);exit;
    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email',
      'password' => 'required',
      'c_password' => 'required|same:password',
    ]);

    if ($validator->fails()) {
      return response()->json(['error'=>$validator->errors()], 401);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    $success['token'] =  $user->createToken('MyApp')->accessToken;
    $success['name'] =  $user->name;

    return response()->json(['status'=>2000,'success'=>$success], $this->successStatus);
  }

  /**
   * details api
   * @return \Illuminate\Http\Response
   */
  public function details()
  {
    $user = Auth::user();
    return response()->json(['success' => $user], $this->successStatus);
  }

  /**
   * 获取用户列表
   * @author: slide
   *
   */
  public function user_lists(){
    $list = new UserModel();
    $result = $list->get();
    return response()->json(['success' => $result], $this->successStatus);
  }

  /**
   * 更新会员信息
   * @author: slide
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   *
   */
  public function update_user(Request $request){
    if(!$request->input('id')){
      return response()->json(['error' => '缺少必要参数'], 503);
    }
    $data = $request->all();
    if(isset($data['s'])) {
      unset($data['s']);
    }
    $user_res = (new UserModel())->where('id', $request->input('id'))->update($data);

    return response()->json(['success' => $user_res], $this->successStatus);
  }
}

