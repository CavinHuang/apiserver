<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
   
    <script src='//cdn.bootcss.com/socket.io/1.3.7/socket.io.js'></script>
    <script src='//cdn.bootcss.com/jquery/1.11.3/jquery.js'></script>
    <script>
      (function ($) {
        $.fn.extend({
          notify: function (options) {
            var settings = $.extend({ type: 'sticky', speed: 500, onDemandButtonHeight: 35 }, options);
            return this.each(function () {
              var wrapper = $(this);
              var ondemandBtn = $('.ondemand-button');
              var dh = -35;
              var w = wrapper.outerWidth() - ondemandBtn.outerWidth();
              ondemandBtn.css('left', w).css('margin-top',  dh + "px" );
              var h = -wrapper.outerHeight();
              wrapper.addClass(settings.type).css('margin-top', h).addClass('visible').removeClass('hide');
              if (settings.type != 'ondemand') {
                wrapper.stop(true, false).animate({ marginTop: 0 }, settings.speed);
              }
              else {
                ondemandBtn.stop(true, false).animate({ marginTop: 0 }, settings.speed);
              }

              var closeBtn = $('.close', wrapper);
              closeBtn.click(function () {
                if (settings.type == 'ondemand') {
                  wrapper.stop(true, false).animate({ marginTop: h }, settings.speed, function () {
                    wrapper.removeClass('visible').addClass('hide');
                    ondemandBtn.stop(true, false).animate({ marginTop: 0 }, settings.speed);
                  });
                }
                else {
                  wrapper.stop(true, false).animate({ marginTop: h }, settings.speed, function () {
                    wrapper.removeClass('visible').addClass('hide');
                  });
                }
              });
              if (settings.type == 'floated') {
                $(document).scroll(function (e) {
                  wrapper.stop(true, false).animate({ top: $(document).scrollTop() }, settings.speed);
                }).resize(function (e) {
                  wrapper.stop(true, false).animate({ top: $(document).scrollTop() }, settings.speed);
                });
              }
              else if (settings.type == 'ondemand') {
                ondemandBtn.click(function () {
                  $(this).animate({ marginTop: dh }, settings.speed, function () {
                    wrapper.removeClass('hide').addClass('visible').animate({ marginTop: 0 }, settings.speed, function () {

                    });
                  })
                });
              }

            });

          }
        });
      })(jQuery);

    </script>
    <style>
        @charset "utf-8";
        body {
            margin:0px; padding:0px;
            font-family: Arial, Helvetica, sans-serif;
            background:url(/repeat.jpg);
            font-size:15px;
            color:#000;
        }
        ul{list-style:none; margin:0px; padding:0px; margin-top:20px;}
        li{padding-bottom:20px;}
        .sticky p, .floated p, .fixed p, .ondemand p{ float:left; padding:0px; margin:0px; margin-left:10px; line-height:45px; color:#fff; font-size:12px;}
        .sticky a, .floated a, .fixed a, .ondemand a{ float:right; margin:13px 10px 0px 0px; }
        img{border:0px;}
        .wrapper{padding:20px;}
    
        .sticky {
        
            position:fixed;
            top:0;
            left:0;
            z-index:1000;
            width:100%;
            border-bottom:3px solid #fff !important;
        
            background: #91BD09; /* Old browsers */
            background: -moz-linear-gradient(top, #91BD09 0%, #91BD09 100%); /* FF3.6+ */
        
            /* FireFox 3.6 */
            /* Safari4+, Chrome */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#91BD09', endColorstr='#91BD09')";
            -pie-background: linear-gradient(#91BD09, #91BD09 100%);
            behavior: url(PIE.htc);
            -moz-box-shadow: 1px 1px 7px #676767;
            -webkit-box-shadow: 1px 1px 7px #676767;
            box-shadow: 1px 1px 7px #676767;
            height: 45px;
            background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #91BD09),color-stop(1, #91BD09));/* IE6,IE7 */
            /* IE8 */
            /* Firefox F3.5+ */
            /* Safari3.0+, Chrome */
        }
    
    
        .floated {
        
            position:absolute;
            top:0;
            left:0;
            z-index:1000;
            width:100%;
            border-bottom:3px solid #fff !important;
            background: #0e59ae; /* Old browsers */
            background: -moz-linear-gradient(top, #0e59ae 0%, #0e59ae 100%); /* FF3.6+ */
        
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#0E59AE', endColorstr='#0E59AE')";
        
            -moz-box-shadow: 1px 1px 7px #676767;
            -webkit-box-shadow: 1px 1px 7px #676767;
            box-shadow: 1px 1px 7px #676767;
            height: 45px;
            background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #0E59AE),color-stop(1, #0E59AE));/* IE6,IE7 */
            -pie-background: linear-gradient(#0E59AE, #0E59AE 100%);
            behavior: url(PIE.htc);
        }
    
    
        .fixed {
            position:absolute;
            top:0;
            left:0;
            width:100%;
            border-bottom:3px solid #fff !important;
        
            background: #660099; /* Old browsers */
            background: -moz-linear-gradient(top, #660099 0%, #660099 100%); /* FF3.6+ */
        
            /* FireFox 3.6 */
            /* Safari4+, Chrome */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#660099', endColorstr='#660099')";
            -pie-background: linear-gradient(#660099, #660099 100%);
            behavior: url(PIE.htc);
            -moz-box-shadow: 1px 1px 7px #676767;
            -webkit-box-shadow: 1px 1px 7px #676767;
            box-shadow: 1px 1px 7px #676767;
            height: 45px;
            background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #660099),color-stop(1, #660099));/* IE6,IE7 */
            /* IE8 */
            /* Firefox F3.5+ */
            /* Safari3.0+, Chrome */
        }
    
        .ondemand {
        
            width:100%;
            border-bottom:3px solid #fff !important;
            position:absolute;
            top:0;
            left:0;
            z-index:1000;
        
            background: #CC0000; /* Old browsers */
            background: -moz-linear-gradient(top, #CC0000 0%, #CC0000 100%); /* FF3.6+ */
        
            /* FireFox 3.6 */
            /* Safari4+, Chrome */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#CC0000', endColorstr='#CC0000')";
            -pie-background: linear-gradient(#CC0000, #CC0000 100%);
            behavior: url(PIE.htc);
            -moz-box-shadow: 1px 1px 7px #676767;
            -webkit-box-shadow: 1px 1px 7px #676767;
            box-shadow: 1px 1px 7px #676767;
            height: 45px;
            background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #CC0000),color-stop(1, #CC0000));/* IE6,IE7 */
            /* IE8 */
            /* Firefox F3.5+ */
            /* Safari3.0+, Chrome */
        }
        .ondemand-button
        {
            width:40px !important;
            height:40px;
            float:right !important;
            z-index:999;
            position:absolute;
            margin-right:100px!important;
        }
    
    
    
    
    
        #footer{width:100%; margin:0 auto; font-size:12px; color:#0E59AE; height:30px;  margin-top:200px;border-top:1px solid #CCC;padding:18px;}
        .hide{display:none;}
    
    
        /* Buttons */
    
        .round.button {
            -moz-border-radius: 15px;
            -webkit-border-radius: 15px;
            border-radius: 15px;
            background-image: url(button-images/round-button-overlay.png);
            border: 1px solid rgba(0, 0, 0, 0.25);
            font-size: 13px;
            padding: 0;
        }
    
        .button {
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            -moz-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.50);
            -webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, 0.50);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.50);
            background: #222;
            border: 1px solid rgba(0, 0, 0, 0.25);
            color: white !important;
            cursor: pointer;
            display: inline-block;
            font-size: 13px;
            font-weight: bold;
            line-height: 1;
            overflow: visible;
            padding: 5px 15px 6px;
            position: relative;
            text-decoration: none;
            text-shadow: 0 -1px 1px rgba(0, 0, 0, 0.25);
            width: auto;
            text-align: center;
        }
    
        .round.button span {
            -moz-border-radius: 14px;
            -webkit-border-radius: 14px;
            border-radius: 14px;
            display: block;
            line-height: 1;
            padding: 4px 15px 6px;
        }
    
    
        .green.button {
            background-color:#91BD09;
        }
        .green.button:hover {
            background-color:#749A02;
        }
        .green.button:active {
            background-color:#a4d50b;
        }
        .blue.button {
            background-color:#0E59AE;
        }
        .blue.button:hover {
            background-color:#063468;
        }
        .blue.button:active {
            background-color:#1169cc;
        }
        .purple.button {
            background-color:#660099;
        }
        .purple.button:hover {
            background-color:#330066;
        }
        .purple.button:active {
            background-color:#7f02bd;
        }
    
        .red.button {
            background-color:#CC0000;
        }
        .red.button:hover {
            background-color:#990000;
        }
        .red.button:active {
            background-color:#ea0202;
        }
        .close
        {}
    
        .show{
        
            background: #CC0000; /* Old browsers */
            background: -moz-linear-gradient(top, #CC0000 0%, #CC0000 100%); /* FF3.6+ */
        
        
            /* FireFox 3.6 */
            /* Safari4+, Chrome */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#CC0000', endColorstr='#CC0000')";
            -pie-background: linear-gradient(#CC0000, #CC0000 100%);
            behavior: url(PIE.htc);
            -moz-box-shadow: 1px 1px 7px #676767;
            -webkit-box-shadow: 1px 1px 7px #676767;
            box-shadow: 1px 1px 7px #676767;
            height: 35px;
            float: right;
            width: 30px;
            overflow:hidden;
            /*margin-top: 0px !important;*/
            margin-right: 10px !important;
            text-align: center;
            background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0, #CC0000),color-stop(1, #CC0000));/* IE6,IE7 */
            /* IE8 */
            /* Firefox F3.5+ */
            /* Safari3.0+, Chrome */
            /* Opera 10.5, IE 9.0 */
        
        
        }
    
        .show img{margin-top:10px;}

    </style>
</head>
<body>

<div class="notification sticky hide">
    <p id="content"> </p>
    <a class="close" href="javascript:"> <img src="/icon-close.png" /></a>
</div>
<div class="wrapper">
    <div style="width:850px;">
        <h3>介绍:</h3>
        <b>Web-msg-sender</b> 是一个web消息推送系统，基于<a rel="nofollow" href="https://github.com/walkor/phpsocket.io">PHPSocket.IO</a>开发。<br><br><br>
        <h3>支持以下特性：</h3>
        <ul>
            <li>多浏览器支持</li>
            <li>支持针对单个用户推送消息</li>
            <li>支持向所有用户推送消息</li>
            <li>长连接推送（websocket或者comet），消息即时到达</li>
            <li>支持在线用户数实时统计推送（见页脚统计）</li>
            <li>支持在线页面数实时统计推送（见页脚统计）</li>
        </ul>
        <h3>测试:</h3>
        当前用户uid：<b class="uid"></b><br>
        可以通过url：<a id="send_to_one" href="http://www.workerman.net:2121/?type=publish&to=1445590039000&content=%E6%B6%88%E6%81%AF%E5%86%85%E5%AE%B9" target="_blank"><font style="color:#91BD09">http://<font class="domain"></font>:2121?type=publish&to=<b class="uid"></b>&content=消息内容</font></a>  向当前用户发送消息<br>
        可以通过url：<a href="http://www.workerman.net:2121/?type=publish&to=&content=%E6%B6%88%E6%81%AF%E5%86%85%E5%AE%B9" target="_blank"  id="send_to_all" ><font style="color:#91BD09">http://<font class="domain"></font>:2121?type=publish&to=&content=消息内容</font></a> 想所有在线用户推送消息<br>
        <script>
          // 使用时替换成真实的uid，这里方便演示使用时间戳
          var uid = <?php echo $_GET['id'] ?>;
          $('#send_to_one').attr('href', 'http://'+document.domain+':2121/?type=publish&content=%E6%B6%88%E6%81%AF%E5%86%85%E5%AE%B9&to='+1);
          $('.uid').html(uid);
          $('#send_to_all').attr('href', 'http://'+document.domain+':2121/?type=publish&content=%E6%B6%88%E6%81%AF%E5%86%85%E5%AE%B9');
          $('.uid').html(uid);
          $('.domain').html(document.domain);
        </script>
    </div>
    
    <script>
      $(document).ready(function () {
        // 连接服务端
        var socket = io('http://'+document.domain+':2120');
        // 连接后登录
        socket.on('connect', function(){
          socket.emit('login', uid);
        });
        // 后端推送来消息时
        socket.on('new_msg', function(msg){
          $('#content').html('收到消息：'+msg);
          $('.notification.sticky').notify();
        });
        // 后端推送来在线数据时
        socket.on('update_online_count', function(online_stat){
          $('#online_box').html(online_stat);
        });
      });
    </script>
    <div id="footer">
        <center id="online_box"></center>
        <center><p style="font-size:11px;color:#555;"> Powered by <a href="http://www.workerman.net/web-sender" target="_blank"><strong>web-msg-sender!</strong></a></p></center>
    </div>
</body>
</html>
