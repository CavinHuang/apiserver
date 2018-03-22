<?php
    include_once('config/config.php'); //加载配置文件
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WIKI<?php echo ' | '.PRODUCT_NAME;?></title>
    <link rel="stylesheet" href="assets/css/semantic.min.css">
</head>
<body>
    <div class="ui large top fixed menu transition visible" style="display: flex !important;">
        <div class="ui container">
            <div class="header item">拓源内部API<code>(1.0)</code></div>
            <a class="item" href="list_class.php">控制器列表</a>
            <a class="item">接口列表</a>
            <a class="item">文档详情</a>
            <a class="active item">使用说明</a>
			<a class="item" href="http://api.tywebs.cn/phpdemo.7z">实例</a>
        </div>
    </div>

    <div class="ui text container" style="max-width: none !important; margin-top: 50px;">
        <div class="ui floating message">
            <span class='ui teal tag label'>目录说明</span>
            <div class="ui message">
                <p>1. API核心目录       app/Services/ApiServer/</p>
                <p>2. API接口目录       app/Services/ApiServer/Response/</p>
                <p>3. apps数据库模型     app/Models/App.php</p>
	              <p>4. 路由配置	         app/Http/routes.php</p>
	              <p>5. API入口控制器      app/Http/Controllers/Api/RouterController.php</p>
            </div>

            <span class='ui teal tag label'>公共参数</span>
            <div class="ui message">
	            <table class="ui black celled striped table">
		            <thead>
		            <tr>
			            <th>参数名</th>
			            <th>类型</th>
			            <th>是否必须</th>
			            <th>描述</th>
		            </tr>
		            </thead>
		            <tbody>
			            <tr>
				            <td>app_id</td>
				            <td>string</td>
				            <td>是</td>
				            <td>应用的ID（平台自动生成）</td>
			            </tr>
			            <tr>
				            <td>method</td>
				            <td>string</td>
				            <td>是</td>
				            <td>接口名称</td>
			            </tr>
			            <tr>
				            <td>format</td>
				            <td>string</td>
				            <td>否</td>
				            <td>回调格式，默认：json（目前仅支持）</td>
			            </tr>
			            <tr>
				            <td>sign_method</td>
				            <td>string</td>
				            <td>否</td>
				            <td>签名类型，默认：md5（目前仅支持）</td>
			            </tr>
			            <tr>
				            <td>nonce</td>
				            <td>string</td>
				            <td>是</td>
				            <td>随机字符串，长度1-32位任意字符（建议每次调用都重新生成）</td>
			            </tr>
			            <tr>
				            <td>sign</td>
				            <td>string</td>
				            <td>是</td>
				            <td>签名字符串，<a href="#sign">参考签名规则</a></td>
			            </tr>
			            <tr>
				            <td>resource</td>
				            <td>Boolean</td>
				            <td>否</td>
				            <td>标识此次请求是否返回一个资源，比如二维码资源时，设置为1</td>
			            </tr>
		            </tbody>
	            </table>
            </div>

            <span class='ui teal tag label'>业务参数</span>
            <div class="ui message">
	            API调用除了必须包含公共参数外，如果API本身有业务级的参数也必须传入，每个API的业务级参数请考API文档说明。
            </div>

			<span class='ui teal tag label'>实例下载</span>
            <div class="ui message">
	            <a href="http://api.tywebs.cn/phpdemo.7z">点击下载实例demo</a>
            </div>

	        <span class='ui teal tag label' id="sign">签名规则</span>
	        <div class="ui message">
		        对所有API请求参数（包括公共参数和请求参数，但除去sign参数），根据参数名称的ASCII码表的顺序排序。如：foo=1, bar=2, foo_bar=3, foobar=4排序后的顺序是bar=2, foo=1, foo_bar=3, foobar=4。
		        将排序好的参数名和参数值拼装在一起，根据上面的示例得到的结果为：bar2foo1foo_bar3foobar4。
		        把拼装好的字符串采用utf-8编码，使用签名算法对编码后的字节流进行摘要。如果使用MD5算法，则需要在拼装的字符串前后加上app的secret后，再进行摘要，如：md5(secret+bar2foo1foo_bar3foobar4+secret)
		        将摘要得到的字节结果使用大写表示
	        </div>
	        <span class='ui teal tag label'>接口写法实例</span>
	        <div class="ui message">
		        方法注释
		        <br />
		        事例一：
		        <pre>
/**
* 批量获取用户基本信息
* @desc 用于获取多个用户基本信息
* @return int    code 操作码，0表示成功
* @return array  list 用户列表
* @return int    list[].id 用户ID
* @return string list[].name 用户名字
* @return string list[].note 用户来源
* @return string msg 提示信息
*/
public function getMultiBaseInfo()
{
	return [];
}
			        </pre>
		        事例二：
		        <pre>
/**
* 获取用户基本信息
* @desc 用于获取单个用户基本信息
* @return int    code 操作码，0表示成功， 1表示用户不存在
* @return object info 用户信息对象
* @return int    info.id 用户ID
* @return string info.name 用户名字
* @return string info.note 用户来源
* @return string msg 提示信息
*/
public function getBaseInfo()
{
	return [];
}
			        </pre>
	        </div>

	          <span class="ui teal tag label">方法传递参数</span>
	          <div class="ui message">
		          <pre>
/**
 * API_DOC 设置方法传参
 * @return array
 */
public function getRules()
{
    return [
        'getBaseInfo' => [
            'userId' => [
                'name'    => 'user_id',
                'type'    => 'int',
                'min'     => 1,
                'require' => true,
                'desc'    => '用户ID'
            ],
        ],

        'getMultiBaseInfo' => [
            'userIds' => [
                'name'    => 'user_ids',
                'type'    => 'array',
                'format'  => 'explode',
                'require' => true,
                'default' => '10',
                'range'   => [10,100],
                'desc'    => '用户ID，多个以逗号分割'
            ],
        ],
    ];
}
                </pre>
	          </div>

	        <span class='ui teal tag label'>返回结果</span>
	        <div class="ui message">
		        <pre>
// 成功
{
    "status": true,
    "code": "200",
    "msg": "成功",
    "data": {
        "time": "2016-08-02 12:07:09"
    }
}
		        </pre>
		        <pre>
// 失败
{
    "status": false,
    "code": "1001",
    "msg": "[app_id]缺失"
}
		        </pre>
	        </div>
	        <span class='ui teal tag label'> 错误码</span>
	        <div class="ui message">
		        命名规范
		        <table class="ui black celled striped table">
			        <thead>
			        <tr>
				        <th>类型</th>
				        <th>长度</th>
				        <th>说明</th>
			        </tr>
			        </thead>
			        <tbody>
			        <tr>
				        <td>系统码</td>
				        <td>3</td>
				        <td>同http状态码</td>
			        </tr>
			        <tr>
				        <td>公共错误码</td>
				        <td>4</td>
				        <td>公共参数错误相关的错误码</td>
			        </tr>
			        <tr>
				        <td>业务错误码</td>
				        <td>	6+</td>
				        <td>2位业务码+4位错误码，不足补位</td>
			        </tr>
			        </tbody>
		        </table>

		        现有错误码
		        <table class="ui black celled striped table">
			        <thead>
			        <tr>
				        <th>错误码</th>
				        <th>错误说明</th>
			        </tr>
			        </thead>
			        <tbody>
			        <tr>
				        <td>200</td>
				        <td>成功</td>
			        </tr>
			        <tr>
				        <td>400</td>
				        <td>未知错误</td>
			        </tr>
			        <tr>
				        <td>401</td>
				        <td>无此权限</td>
			        </tr>
			        <tr>
				        <td>500</td>
				        <td>服务器异常</td>
			        </tr>
			        <tr>
				        <td>1001</td>
				        <td>[app_id]缺失</td>
			        </tr>
			        <tr>
				        <td>1002</td>
				        <td>[app_id]不存在或无权限</td>
			        </tr>
			        <tr>
				        <td>1003</td>
				        <td>[method]缺失</td>
			        </tr>
			        <tr>
				        <td>1004</td>
				        <td>[format]错误</td>
			        </tr>
			        <tr>
				        <td>1005</td>
				        <td>[sign_method]错误</td>
			        </tr>
			        <tr>
				        <td>1006</td>
				        <td>[sign]缺失</td>
			        </tr>
			        <tr>
				        <td>1007</td>
				        <td>[sign]签名错误</td>
			        </tr>
			        <tr>
				        <td>1008</td>
				        <td>[method]方法不存在</td>
			        </tr>
			        <tr>
				        <td>1009</td>
				        <td>执行方法不存在，请联系管理员</td>
			        </tr>
			        <tr>
				        <td>1010</td>
				        <td>[nonce]缺失</td>
			        </tr>
			        <tr>
				        <td>1011</td>
				        <td>[nonce]必须为字符串</td>
			        </tr>
			        <tr>
				        <td>1012</td>
				        <td>[nonce]长度必须为1-32位</td>
			        </tr>
			        </tbody>
		        </table>
	        </div>
        </div>
	    <div class="ui blue message">
		    <strong>温馨提示：</strong> 具体写法请参考Demo中getUserInfo接口。
	    </div>
        <p><?php echo COPYRIGHT?><p>

    </div>
</body>
</html>
