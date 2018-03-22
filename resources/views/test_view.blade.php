<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
</head>
<body>
<div id="content"></div>
<script>
    $.get('http://127.0.0.1:8000/api/router?app_id=TY7616516167&method=ArticleCollect.sogoWechatArticleDetail&nonce=hcm&sign=6A5BD243DFA9ABE2E3F45EA69B301BA8&url=https://mp.weixin.qq.com/s?src=3&timestamp=1502930521&ver=1&signature=Kk1qbxvxVIHbJ2tBDgt5Ne69KN3JVmE0kcb2G6d3PPb0g0jnOC637DAVb9B4bo7BXp5rog0LwahYx5f2prwt14oqYNgf0Ic6u4YJt3dXkZCSxJ1BVqdequO-XsKQlWgKP8DD2*KllCnUKEuI3APwzm4hdzJoyMmUjKAwGVriCmQ=', {}, function (res) {
      console.log(res)
      $("#content").html(res.result.content)
    }, 'json')
</script>
</body>
</html>



