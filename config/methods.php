<?php

return
array (
  'ArticleCollect' => 
  array (
    'ArticleCollect.run' => '@title 接口信息展示',
    'ArticleCollect.sogoWechatArticleList' => '@title 获取搜狗微信文章并组织输出',
    'ArticleCollect.sogoWechatArticleCategory' => '@title 返回搜狗分类标识',
    'ArticleCollect.sogoWechatArticleDetail' => '@title 获取文章详情(并返回文章基本信息用于读取/更新)',
    'ArticleCollect.toutiaoArticleList' => '@title 今日头条文章列表',
    'ArticleCollect.toutiaoArticleDetail' => '@title 今日头条文章详情',
  ),
  'Badwords' => 
  array (
    'Badwords.run' => '接口基础运行返回控制器数据',
    'Badwords.setBadWords' => '添加新的敏感词汇',
    'Badwords.getBadWords' => '获取所有的敏感词汇',
    'Badwords.checkBadWords' => '检测提交的词那些是敏感词汇',
    'Badwords.checkArticle' => '检测一段文本中包含的敏感词汇',
  ),
  'Demo' => 
  array (
    'Demo.run' => '接口基础运行',
    'Demo.getUserInfo' => '批量获取用户基本信息',
  ),
  'Ocr' => 
  array (
    'Ocr.run' => '接口基础运行返回控制器数据',
    'Ocr.ocrTest' => '图片ocr识别',
  ),
  'Qrcode' => 
  array (
    'Qrcode.run' => '接口基础运行返回控制器数据',
    'Qrcode.createQrcode' => '生成普通二维码',
  ),
  'Wallpaper' => 
  array (
    'Wallpaper.run' => '接口基础运行返回控制器数据',
    'Wallpaper.createWallPaper' => '生成墙纸',
  ),
  'Wechat' => 
  array (
    'Wechat.run' => '接口基础运行返回控制器数据',
    'Wechat.getQrcode' => '获取微信公众号带参数的二维码',
    'Wechat.LangUrlToShort' => '微信长连接转短连接',
    'Wechat.get' => '微信多媒体文件下载',
    'Wechat.getAccessToken' => '获取公众号全局票据',
    'Wechat.getIp' => '获取微信服务器ip',
    'Wechat.sendTemplate' => '发送模板消息',
    'Wechat.getUserInfo' => '根据用户openid获取用户详细信息',
    'Wechat.previewMsg' => '主动推送一条预览消息',
  ),
  'Music' => 
  array (
    'Music.run' => '接口基础运行返回控制器数据',
    'Music.getTopList' => '获取榜单列表',
    'Music.getSongList' => '获取榜单歌曲列表',
    'Music.getMusicUrl' => '获取歌曲播放链接',
  ),
)?>