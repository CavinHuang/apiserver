<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // return view('home');
      $swagger = \Swagger\scan(dirname(ROOT).'/app/Services/ApiServer/Response/');
      file_put_contents(dirname(ROOT).'/swagger.json', $swagger);
      return $swagger;
    }

    public function wechatImage(Request $request){
      $url = $request->input('url');
      $ch = curl_init();
      $httpheader = array(
        'Host' => 'mmbiz.qpic.cn',
        'Connection' => 'keep-alive',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'no-cache',
        'Accept' => 'textml,application/xhtml+xml,application/xml;q=0.9,image/webp,/;q=0.8',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36',
        'Accept-Encoding' => 'gzip, deflate, sdch',
        'Accept-Language' => 'zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4'
      );
      $options = array(
        CURLOPT_HTTPHEADER => $httpheader,
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true
      );
      curl_setopt_array( $ch , $options );
      $result = curl_exec( $ch );
      curl_close($ch);
      header('Content-type: image/jpg');
      echo $result;
      exit;
    }
}
