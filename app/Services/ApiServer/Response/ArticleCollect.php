<?php
namespace App\Services\ApiServer\Response;

use QL\QueryList;

class ArticleCollect extends BaseResponse implements InterfaceResponse {

  /**
   * 接口信息展示
   * @method ArticleCollect.run
   * @desc 展示接口的详细信息
   * @author slide
   * @param $params
   * @param $action
   *
   */
  public function run ( &$params, $action ) {
    // TODO: Implement run() method.
  }

  /**
   * 获取搜狗微信文章并组织输出
   * @method ArticleCollect.sogoWechatArticleList
   * @desc 获取搜狗微信文章列表并组织输出
   * @author slide
   * @return object result 输出文章列表
   * @return string result[].title 文章标题
   * @return string result[].link  文章链接
   * @return string result[].thumb 文章缩略图
   * @return string result[].author_name 文章作者名
   * @return string result[].author_headimage 文章头像
   * @return string result[].timestamp 文章发布时间
   */

  public function sogoWechatArticleList(){
    $category = $this->getParams('category', 'string') != '' ? $this->getParams('category', 'string') : 'wap_0';
    $page = $this->getParams('page', 'int');

    $url = "http://weixin.sogou.com/wapindex/wap/0612/{$category}/{$page}.html";

    //输出结果：二维关联数组
    $hj = QueryList::Query($url,array(
      'title'=>array('.list-txt>h4>a>div','text'),
      'link'=>array('.list-txt>h4>a','href'),
      'thumb'=>array('.pic img', 'src'),
      'author_name' => array('.list-txt>.time>span', 'text'),
      'author_headimage' => array('.list-txt>.time>span', 'data-headimage'),
      'timestamp' => array('.list-txt>.time>a>span', 'data-lastmodified'),
    ));

    foreach ($hj->data as $k => &$v){
      $v['thumb'] = '/wechat_image?url='.explode('url=', $v['thumb'])[1];
    }

    return $this->ajax(200, 'success', '成功', $hj->data);
  }

  /**
   * 返回搜狗分类标识
   * @method ArticleCollect.sogoWechatArticleCategory
   * @desc 返回搜狗微信分类表示
   * @author slide
   * @return array result 分类结果
   */
  public function sogoWechatArticleCategory(){
    $c_data = [
      'wap_0' => '热门',
      'wap_1' => '推荐',
      'wap_2' => '段子手',
      'wap_3' => '养生堂',
      'wap_4' => '私房话',
      'wap_5' => '八卦精',
      'wap_6' => '科技咖',
      'wap_7' => '财经迷',
      'wap_8' => '汽车迷',
      'wap_9' => '爱生活',
      'wap_10' => '潮人帮',
      'wap_11' => '辣妈帮',
      'wap_12' => '点赞党',
      'wap_13' => '旅行家',
      'wap_14' => '职场人',
      'wap_15' => '美食家',
      'wap_16' => '古今通',
      'wap_17' => '学霸族',
      'wap_18' => '星座控',
      'wap_19' => '体育迷',
    ];

    return $this->ajax(200, 'success', '成功', $c_data);
  }

  /**
   * 获取文章详情(并返回文章基本信息用于读取/更新)
   * @method ArticleCollect.sogoWechatArticleDetail
   * @desc 获取文章详情(并返回文章基本信息用于读取)
   * @author slide
   * @return array result 结果返回
   * @return array result[].content 内容
   * @return array result[].article_url 文章地址
   * @return array result[].article_title 文章标题
   * @return array result[].author_avatar 文章作者
   * @return array result[].article_thumb 文章缩略图
   */
  public function sogoWechatArticleDetail()
  {
    $url = $this->getParams('url', 'string');

    if($url == '') return $this->ajax(404, 'param error', '参数错误');

    $option = array(
      'http' => array(
        'header' => "Referer:" . 'http://weixin.sogou.com/'),
    );
    $url = file_get_contents($url, FALSE, stream_context_create($option));

    //去除微信干扰元素!!!否则乱码
    $url = str_replace("<!--headTrap<body></body><head></head><html></html>-->", "", $url);
    $rules = array(
      'content' => array('#js_content', 'html','a -.content_copyright -script',function($content){
        //利用回调函数下载文章中的图片并替换图片路径为本地路径
        //使用本例请确保当前目录下有image文件夹，并有写入权限
        //由于QueryList是基于phpQuery的，所以可以随时随地使用phpQuery，当然在这里也可以使用正则或者其它方式达到同样的目的
        $doc = \phpQuery::newDocumentHTML($content);
        $imgs = pq($doc)->find('img');
        foreach ($imgs as $img) {
          $src = '/wechat_image?url='.pq($img)->attr('data-src');
          pq($img)->attr('src',$src);
        }
        $videos  = pq("iframe.video_iframe");
        $vplay = '/https:\/\/v.qq.com\/iframe\/preview.html/';
        $vplay_n = 'https://v.qq.com/iframe/player.html';
        foreach ($videos as $vedio) {

          $vds = pq($vedio)->attr('data-src');

          $reg = "/width=[0-9]*&height=[0-9]*/"; // 'width=auto&height=auto'

          $vdrp = preg_replace($vplay, $vplay_n, $vds);
          $vdrp = preg_replace($reg, '', $vdrp);
          // $vdrp = preg_replace('/auto=0/', 'auto=1', $vdrp);
          pq($vedio)->attr('src', $vdrp);
        }
        return $doc->htmlOuter();
      }),
      'time' => array('#post-date', 'text'),
      'author_name' => array('#post-user', 'text'),
      'stylesheet' => array('head', 'html', '-meta', function ($head) {
        $doc = \phpQuery::newDocumentHTML($head);
        $styles = pq($doc)->find('style');
        $css = '';
        foreach ($styles as $style){
          $css .= pq($style)->text();
        }
        return $css;
      })
    );
    $content = QueryList::Query($url, $rules)->getData();

    //原文链接
    preg_match("/var msg_link = \".*\"/", $url, $matches);
    $orUrl = html_entity_decode(urldecode($matches[0]));
    $orUrl = substr(explode('var msg_link = "', $orUrl)[1], 0, -4);

    //原文标题 !避免出现标题被截取
    preg_match("/var msg_title = \".*\"/", $url, $matches);
    $orTitle = $matches[0];
    $orTitle = substr(explode('var msg_title = "', $orTitle)[1], 0, -1);

    //原文作者头像
    preg_match("/var round_head_img = \".*\"/", $url, $matches);
    $orAuthAvatar = $matches[0];
    $orAuthAvatar = substr(explode('var round_head_img = "', $orAuthAvatar)[1], 0, -1);

    //原文缩略图
    preg_match("/var msg_cdn_url = \".*\"/", $url, $matches);
    $orImgUrl = $matches[0];
    $orImgUrl = substr(explode('var msg_cdn_url = "', $orImgUrl)[1], 0, -1);

    $detail = array(
      'content'        => $content[0]['content'],
      'article_url'    => urldecode($orUrl),
      'article_title'  => html_entity_decode($orTitle),
      'author_avatar'  => $orAuthAvatar,
      'author_name' => $content[0]['author_name'],
      'time' => $content[0]['time'],
      'article_thumb'  => $orImgUrl,
      'style' => $content[0]['stylesheet']
    );

    return $this->ajax(200, 'success', '成功', $detail);
  }

  /**
   * 今日头条文章列表
   * @method ArticleCollect.toutiaoArticleList
   * @desc 今日头条文章列表
   * @author slide
   * @return array result 文章列表返回
   * @return string result[].chinese_tag 文章中文标签
   * @return string result[].media_avatar_url 文章缩略图
   * @return string result[].is_feed_ad 是否是付费广告
   * @return string result[].tag_url 文章标签列表
   * @return string result[].title 文章标题
   * @return string result[].middle_mode 不明字段
   * @return string result[].single_mode 不明字段
   * @return string result[].abstract 文章摘要
   * @return string result[].tag 文章分类
   * @return array result[].label 文章标签组
   * @return string result[].behot_time 文章成为热门的时间
   * @return string result[].source_url 文章链接
   * @return string result[].source 文章所属头条号名字
   * @return string result[].article_genre 文章作者
   * @return string result[].image_url 文章小图
   * @return int result[].comments_count 文章评论数
   * @return string result[].media_url 头条号专栏地址
   * @return string result[].group_source 未知
   * @return string result[].has_gallery 未知
   */
  public function toutiaoArticleList(){
    $max_behot_time = $this->getParams('max_behot_time', 'int');
    $min_behot_time = $this->getParams('min_behot_time', 'int');
    $category = $this->getParams('category', 'string') != '' ? $this->getParams('category', 'string') : '__all__';

    $url = "http://www.toutiao.com/api/pc/feed/?category={$category}&utm_source=toutiao&widen=1&tadrequire=true&as=A17589E9F500B18&cp=5995C03BF1287E1&min_behot_time={$min_behot_time}&max_behot_time={$max_behot_time}";

    $result = httpRequest($url, 'get', [],['Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8','Host:www.toutiao.com','Upgrade-Insecure-Requests:1','User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3159.5 Safari/537.36','Referer:http://www.toutiao.com/','Cookie:UM_distinctid=15dee028220cbd-0205e934cb78f6-6d55752c-1fa400-15dee0282219b8; uuid="w:1d89a142e6254d87a64ad6bf4c4454b9"; _ga=GA1.2.961564750.1502936733; _gid=GA1.2.822908139.1502936733; csrftoken=9d32a73d16d99759603410766909ee4e; WEATHER_CITY=%E5%8C%97%E4%BA%AC; __tasessionId=ss8axachr1502939921598; CNZZDATA1259612802=202626358-1502933369-null%7C1502938769; tt_webid=6455064090653541901'], false);


    return response()->json(['result' => json_decode($result, true)]);
  }

  /**
   * 今日头条文章详情
   * @method ArticleCollect.toutiaoArticleDetail
   * @desc 根据头条地址获取文章详情
   * @author slide
   * @return array result 文章信息
   */
  public function toutiaoArticleDetail(){

    $url = $this->getParams('url', 'string');

    if($url == '') return $this->ajax(404, 'params error', '参数错误');

    $html = file_get_contents($url);
    $article_data = [];
    $html_doc = \phpQuery::newDocumentHTML($html);

    $article_data['title'] = pq($html_doc)->find('.article-title')->text();
    $article_data['content'] = pq($html_doc)->find('.article-content')->html();
    $article_data['author_name'] = pq($html_doc)->find('.src')->text();
    $article_data['time'] = pq($html_doc)->find('.time')->text();

    $label_list = pq($html_doc)->find('.label-item');
    $label_data = [];
    foreach ($label_list as $k => $v){
      $temp = [
        'url' => pq($v)->find('a')->attr('href'),
        'text' => pq($v)->find('a')->text()
      ];
      $label_data[] = $temp;
    }
    $article_data['label'] = $label_data;

    return $this->ajax(200, 'success', '成功', $article_data);
  }
  /**
   * 参数说明
   */
  public static function getRules () {
    // TODO: Implement getRules() method.
    return [
      'sogoWechatArticleList' => [
        'category' => [
          'name'    => 'category',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '可以通过ArticleCollect.sogoWechatArticleCategory接口获取'
        ],
        'page' => [
          'name'    => 'page',
          'type'    => 'int',
          'min'     => '0',
          'require' => true,
          'desc'    => '页码从0开始'
        ],
      ],
      'sogoWechatArticleCategory' => [],
      'sogoWechatArticleDetail' =>[

        'url' => [
          'name'    => 'url',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '文章地址'
        ],
      ],
      'toutiaoArticleDetail' =>[

        'url' => [
          'name'    => 'url',
          'type'    => 'string',
          'min'     => '',
          'require' => true,
          'desc'    => '文章地址'
        ],
      ],
      'toutiaoArticleList' =>[

        'max_behot_time' => [
          'name'    => 'max_behot_time',
          'type'    => 'int',
          'min'     => '0',
          'require' => false,
          'desc'    => '文章最大时间'
        ],
        'min_behot_time' => [
          'name'    => 'min_behot_time',
          'type'    => 'int',
          'min'     => '0',
          'require' => false,
          'desc'    => '文章最小时间'
        ],
        'category' => [
          'name'    => 'category',
          'type'    => 'string',
          'min'     => '__all__',
          'require' => true,
          'default' => '__all__',
          'desc'    => '文章分类标识__all__(全部)，news_hot（热点），video（视频），news_image（图片），essay_joke（段子），news_society（社会），news_entertainment（娱乐），news_tech（科技），news_sports（体育），news_car（汽车），news_finance（财经），funny（搞笑），news_military（军事），news_world（国际），news_fashion（时尚），news_travel（旅游），news_discovery（探索），news_baby（育儿），news_regimen（养生）,news_essay（美文），news_game(游戏)，news_history（历史），news_food（美食）'
        ],

      ],
      'run' => []
    ];
  }
}
