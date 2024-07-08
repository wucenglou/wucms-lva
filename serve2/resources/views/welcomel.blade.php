<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>WUCMS演示站</title>
    <meta name="author" content="WUCMS内容管理系统">
    <meta name="keywords" content="WUCMS演示站" />
    <meta name="description"
        content="WUCMS是一款基于YZMPHP开发的一套轻量级开源内容管理系统，WUCMS简洁、安全、开源、实用，可运行在Linux、Windows、MacOSX、Solaris等各种平台上，专注为公司企业、个人站长快速建站提供解决方案。" />
    <link rel="stylesheet" type="text/css" href="libs/css/swiper.min.css" />
    <link href="libs/css/yzm-common.css" rel="stylesheet" type="text/css" />
    <link href="libs/css/yzm-style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="libs/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="libs/js/yzm-front.js"></script>
    <script type="text/javascript" src="libs/js/swiper.min.js"></script>
    <meta http-equiv="mobile-agent" content="format=xhtml;url=http://t2.t/index.php?m=mobile">
    <script type="text/javascript">
        if (window.location.toString().indexOf('pref=padindex') != -1) {} else {
            if (/AppleWebKit.*Mobile/i.test(navigator.userAgent) || (
                    /MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/
                    .test(navigator.userAgent))) {
                if (window.location.href.indexOf("?mobile") < 0) {
                    try {
                        if (/Android|Windows Phone|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)) {
                            window.location.href = "http://t2.t/index.php?m=mobile";
                        } else if (/iPad/i.test(navigator.userAgent)) {} else {}
                    } catch (e) {}
                }
            }
        }
    </script>
    <script src="https://unpkg.com/vue"></script>
    <!-- 会使用最新版本，你最好指定一个版本 -->
    <script src="https://unpkg.com/naive-ui"></script>
</head>

<body>
    <!--网站头部-->
    <div class="yzm-header-box">
        <div class="yzm-header-top">
            <div class="yzm-member-status">
                <a href="http://t2.t/member/index/register.html" target="_blank">注册</a>
                <a href="http://t2.t/member/index/login.html?referer=http%3A%2F%2Ft2.t%2F" target="_blank"
                    class="login">登录</a>
            </div>
            <div class="topleft">
                轻量级开源内容管理系统：<a href="https://www.WUCMS.com" target="_blank">WUCMS官方网站</a>
            </div>
        </div>
    </div>
    <div class="yzm-container-box">
        <div class="yzm-header">
            <div class="yzm-logo">
                <a href="http://t2.t/">WUCMS内容管理系统</a>
                <!-- <a href="http://t2.t/"><img src="libs/picture/logo.png" title="WUCMS演示站" alt="WUCMS演示站"></a> -->
            </div>
            <div class="yzm-search">
                <form method="get" action="http://t2.t/search/index/init.html" target="_blank">
                    <div id="searchtxt" class="searchtxt">
                        <div class="searchmenu">
                            <div class="searchselected" id="searchselected">全站</div>
                            <div class="searchtab" id="searchtab">
                                <ul>
                                    <li data="0">全站</li>
                                    <li data="1">文章</li>
                                    <li data="2">产品</li>
                                    <li data="3">下载</li>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="modelid" value="0" id="modelid" />
                        <input name="q" type="text" placeholder="输入关键字" />
                    </div>
                    <div class="searchbtn">
                        <button id="searchbtn" type="submit">搜索</button>
                    </div>
                </form>
            </div>
            <div class="yzm-add-content"><a href="http://t2.t/member/member_content/init.html" target="_blank">发布内容</a>
            </div>
        </div>

        <!--网站导航-->
        <div class="yzm-menu">
            <ul class="yzm-nav">
                {{-- <li><a class="current" href="http://t2.t/">首页</a></li>
                <li>
                    <a href="/xinwenzhongxin/" target="_self">新闻中心</a>
                    <!-- 这里是二级栏目的循环，不需要的可以删除，代码开始 -->

                    <ul class="sub_nav">
                        <li><a href="/guanfangxinwen/" target="_self">官方新闻</a></li>
                        <li><a href="/qitaxinwen/" target="_self">其他新闻</a></li>

                    </ul>
                    <!-- 这里是二级栏目的循环，不需要的可以删除，代码结束 -->
                </li>
                <li>
                    <a href="/guanyuwomen/" target="_self">关于我们</a>
                    <!-- 这里是二级栏目的循环，不需要的可以删除，代码开始 -->
                    <!-- 这里是二级栏目的循环，不需要的可以删除，代码结束 -->
                </li>
                <li>
                    <a href="http://www.WUCMS.com/" target="_blank">官方网站</a>
                    <!-- 这里是二级栏目的循环，不需要的可以删除，代码开始 -->
                    <!-- 这里是二级栏目的循环，不需要的可以删除，代码结束 -->
                </li> --}}

                @foreach ($navs as $nav)
                    <li><a class="" href="/{{ $nav['name'] }}">{{ $nav['metaTitle'] }}</a>
                        @if (!empty($nav['children']))
                            <ul class="sub_nav">
                                @foreach ($nav['children'] as $n)
                                    <li><a class="" href="/{{$nav['name'] }}/{{ $n['path'] }}">{{ $n['metaTitle'] }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach


            </ul>
        </div>
    </div>

    <!--网站容器-->
    <div class="yzm-container">
        <div class="box">
            <!-- 轮播图 开始 -->
            <!-- 
    这里并没有调用后台自带的轮播图，如有需要调用后台轮播图请查看教程，
    https://www.WUCMS.com/dongtai/130.html
   -->
            <div class="swiper-container yzm-banner">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <a href="https://www.WUCMS.com" target="_blank">
                            <img src="libs/picture/banner1.png" alt="WUCMS内容管理系统" title="WUCMS内容管理系统" />
                        </a>
                    </div>
                    <div class="swiper-slide">
                        <a href="https://www.WUCMS.com/xiazai/" target="_blank">
                            <img src="libs/picture/banner2.png" alt="WUCMS内容管理系统" title="WUCMS内容管理系统" />
                        </a>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="yzm-button-next"></div>
                <div class="yzm-button-prev"></div>

            </div>
            <!-- 轮播图 结束 -->

            <div class="yzm-content-box yzm-top-right">
                <div class="yzm-title">
                    <h2>最近更新</h2>
                </div>
                <ul class="yzm-ranking">
                    <!-- 此处为功能演示，调取全站(不限模型)最近更新内容，加 allsite="1" 属性，即可调用分站内容 -->
                    <li><em>1</em><span class="date">01-11</span><a href="/guanfangxinwen/2.html"
                            title="WUCMS v6.3正式版发布" target="_blank">WUCMS v6.3正式版发布</a></li>
                    <li><em>2</em><span class="date">05-15</span><a href="/guanfangxinwen/1.html"
                            title="YZMPHP轻量级开源框架 V2.0" target="_blank">YZMPHP轻量级开源框架 V2.0</a></li>

                </ul>
            </div>

            <div class="yzm-line"></div>
            <div class="yzm-content-box yzm-text-thumbs">
                <div class="yzm-title">
                    <h2>推荐文章</h2>
                </div>
                <!-- 此处仅为功能演示，不分栏目，调取模型ID(modelid)为1的推荐文章 -->
                <div class="yzm-text-thumb">
                    <a href="/guanfangxinwen/2.html" class="yzm-text-thumbl">
                        <img src="libs/picture/nopic.jpg" alt="WUCMS v6.3正式版发布" title="WUCMS v6.3正式版发布" />
                    </a>
                    <div class="yzm-text-thumbr">
                        <a href="/guanfangxinwen/2.html"><span class="title_color" style="color:#ff0000">WUCMS
                                v6.3正式版发布</span></a>
                        <p>产品说明：WUCMS是一款轻量级开源内容管理系统，它采用OOP（面向对象）方式自主开发的框架。基于PHP+Mysql架构，并采用MVC框架式开发的一...</p>
                    </div>
                </div>
                <div class="yzm-text-thumb">
                    <a href="/guanfangxinwen/1.html" class="yzm-text-thumbl">
                        <img src="libs/picture/nopic.jpg" alt="YZMPHP轻量级开源框架 V2.0" title="YZMPHP轻量级开源框架 V2.0" />
                    </a>
                    <div class="yzm-text-thumbr">
                        <a href="/guanfangxinwen/1.html"><span class="title_color">YZMPHP轻量级开源框架 V2.0</span></a>
                        <p>简介：YZMPHP是一款免费开源的轻量级PHP框架，框架完全采用面向对象的设计思想，并且是基于MVC的三层设计模式。具有部署和应用及为简单、效...</p>
                    </div>
                </div>

            </div>

            <div class="yzm-line"></div>
            <div class="yzm-content-box yzm-text-list yzm-index-50">
                <div class="yzm-title">
                    <h2>新闻中心</h2>
                </div>
                <ul>
                    <!-- 此处仅为功能演示，调用栏目ID为1的内容 -->
                    <li>
                        <span class="yzm-date">2022-01-11</span>
                        <a href="/guanfangxinwen/2.html" title="WUCMS v6.3正式版发布" target="_blank"><span
                                class="title_color" style="color:#ff0000">WUCMS v6.3正式版发布</span></a>
                    </li>
                    <li>
                        <span class="yzm-date">2018-05-15</span>
                        <a href="/guanfangxinwen/1.html" title="YZMPHP轻量级开源框架 V2.0" target="_blank"><span
                                class="title_color">YZMPHP轻量级开源框架 V2.0</span></a>
                    </li>

                </ul>
            </div>
            <div class="yzm-content-box yzm-text-list yzm-index-50">
                <div class="yzm-title">
                    <h2>官方新闻</h2>
                </div>
                <ul>
                    <!-- 此处仅为功能演示，调用栏目ID为2的内容 -->
                    <li>
                        <span class="yzm-date">2022-01-11</span>
                        <a href="/guanfangxinwen/2.html" title="WUCMS v6.3正式版发布" target="_blank"><span
                                class="title_color" style="color:#ff0000">WUCMS v6.3正式版发布</span></a>
                    </li>
                    <li>
                        <span class="yzm-date">2018-05-15</span>
                        <a href="/guanfangxinwen/1.html" title="YZMPHP轻量级开源框架 V2.0" target="_blank"><span
                                class="title_color">YZMPHP轻量级开源框架 V2.0</span></a>
                    </li>

                </ul>
            </div>
            <div class="yzm-line"></div>
            <div class="yzm-advertise">
                <!-- 自定义广告位：更改这里，请登录后台->系统管理->自定义配置 -->
                <h1>免费又好用的CMS建站系统，就选WUCMS!</h1>
            </div>
            <div class="yzm-line"></div>
            <div class="yzm-content-box yzm-img-list">
                <div class="yzm-title">
                    <h2>图文资讯</h2>
                </div>
                <ul>
                    <!-- 此处仅为功能演示，调用模型ID为1且有缩略图的内容 -->

                </ul>
            </div>

            <div class="yzm-line"></div>
            <div class="yzm-content-box yzm-tag">
                <div class="yzm-title">
                    <h2>热门标签</h2>
                </div>
                <ul>

                </ul>
            </div>
            <div class="yzm-line"></div>
            <div class="yzm-content-box yzm-link">
                <div class="yzm-title">
                    <h2>友情链接</h2>
                    <span class="yzm-title-right"><a href="http://t2.t/link/index/init.html">>>申请友链</a></span>
                </div>
                <ul>
                    {{-- <li><a href="http://www.WUCMS.com/" target="_blank">WUCMS官方网站</a></li>
                    <li><a href="http://www.yzmask.com/" target="_blank">WUCMS交流社区</a></li>
                    <li><a href="http://blog.WUCMS.com/" target="_blank">WUCMS官方博客</a></li> --}}

                </ul>
            </div>
        </div>

        <script type="text/javascript">
            var swiper = new Swiper('.swiper-container', {
                loop: true,
                centeredSlides: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                navigation: {
                    nextEl: '.yzm-button-next',
                    prevEl: '.yzm-button-prev',
                },
            });
        </script>

    </div>
    <div class="clearfix"></div>
    <div class="yzm-container-box yzm-footer">
        <div>
            <p>Powered By WUCMS内容管理系统</p>
        </div>
    </div>

    <n-button> @{{ message }}</n-button>
    <script>
      const App = {
        setup() {
          return {
            message: 'naive'
          }
        }
      }
      const app = Vue.createApp(App)
      app.use(naive)
      app.mount('#app')
    </script>
</body>

</html>
