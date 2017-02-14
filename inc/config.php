<?php
define("app_sitename","苹果电影程序");      //网站名称
define("app_installdir","/");        //网站路径
define("app_siteurl","localhost");        //网站域名地址
define("app_keywords","免费在线电影");        //网站关键字
define("app_description","提供最新最快的影视资讯和在线播放");        //网站描述信息
define("app_templatedir","default");    //模板目录
define("app_htmldir","html");      //模板html目录
define("app_cache",0);       //是否开启缓存
define("app_cachetime",60);       //缓存时间
define("app_cacheid","1354846469");      //缓存标示
define("app_picpath",1);      //图片保存路径方式，0=默认,1=按月份,2=按日期,3=每目录500图片，超过自动创建新目录
define("app_dbtype","mysql");      //数据库类型: 值分别为 access ; mssql
define("app_dbpath","");      //access数据库路径
define("app_dbserver","localhost");      //SQL数据库服务器地址
define("app_dbname","maccms7");      					  //SQL数据库名称
define("app_dbuser","root");      //SQL数据库用户名
define("app_dbpass","123456");      					  //SQL数据库密码
define("app_tablepre","mac_");      					  //表结构前缀
define("app_icp","");      //网站备案号
define("app_email","123456@maccms.com");      //站长邮箱
define("app_qq","123456");      //站长qq
define("app_user",1);          //是否开启会员验证0关闭，1开启
define("app_dynamiccache",0);          //是否开启动态文件缓存0关闭，1开启
define("app_timming",0);          //是否开启定时任务0关闭,1开启
define("app_install",0);          //是否已经安装本程序0未安装，1已安装
define("app_safecode","maccms");      //后台登录安全码

define("app_vodplayersort",0);        //视频播放器显示顺序0添加顺序，1全局顺序
define("app_vodmaccjsname",0);        //联盟资源库入库时重名判断条件0名称，1名称+分类
define("app_vodviewtype",0);        //视频首页、地图 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=静态模式,3=rewrite伪静态
define("app_vodlistviewtype",0);        //视频列表页 浏览模式, 同上
define("app_vodtopicviewtype",0);        //视频专题首页、列表 浏览模式, 同上
define("app_vodcontentviewtype",0);        //视频内容页 浏览模式, 同上
define("app_vodplayviewtype",0);        //视频播放页 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=rewrite伪静态,3=静态每数据一页,4=静态每集一页,5=静态每组一页,6=静态全站一页
define("app_voddownviewtype",0);        //视频播放页 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=rewrite伪静态,3=静态每数据一页,4=静态每集一页,5=静态每组一页,6=静态全站一页
define("app_vodsuffix","html");      //生成视频静态文件后缀名
define("app_vodmakeinterval",2);        //生成视频静态页面间隔
define("app_playtype",0);        //0=有内容页播放,1=无内容页播放
define("app_encrypt",0);        //是否加密播放地址 0不加密,1 escape加密,2 base64加密
define("app_playisopen",0);        //否弹窗播放 0普通播放，1弹窗播放
define("app_vodlistpath","vodlisthtml/{id}");        //视频分类页面目录
define("app_vodpath","vodhtml/{id}");         //视频内容页面目录
define("app_vodplaypath","vodplayhtml/{id}");          //视频播放页面目录
define("app_voddownpath","voddownhtml/{id}");          //视频下载页面目录
define("app_vodtopicpath","vodtopichtml/{id}");          //视频专题页目录

define("app_artviewtype",0);        //文章首页、地图 浏览模式, 0=仿伪静态模式,1=asp动态模式,2=静态模式,3=rewrite伪静态
define("app_artlistviewtype",0);        //文章列表页 浏览模式, 同上
define("app_arttopicviewtype",0);        //文章专题首页、列表 浏览模式, 同上
define("app_artcontentviewtype",0);        //文章内容页 浏览模式, 同上
define("app_artsuffix","html");        //生成文章静态文件后缀名
define("app_artmakeinterval",2);        //生成文章静态页面间隔
define("app_artlistpath","artlisthtml/{id}");        //文章列表目录
define("app_artpath","arthtml/{id}");        //文章页面目录
define("app_arttopicpath","arttopichtml/{id}");          //文章专题页目录

define("app_watermark",0);          //上传的图片是否添加水印 0关闭，1开启
define("app_waterlocation",2);          //水印在图片的位置
define("app_waterfont","MacCMS.Com");          //水印文字
define("app_gbook",1);        //是否开启留言本 0关闭，1开启
define("app_gbooknum",10);        //留言本每页显示数量
define("app_gbooktime",10);        //留言时间间隔
define("app_gbookverify",0);        //留言时是否开启验证码0关闭,1开启
define("app_gbookaudit",0);        //留言时是否需要审核0关闭,1开启
define("app_comment",1);        //是否开启评论 0关闭，1开启
define("app_commentnum",10);        //评论每页显示数量
define("app_commenttime",10);        //评论时间间隔
define("app_commentverify",0);        //评论时是否开启验证码0关闭,1开启
define("app_commentaudit",0);        //评论时是否需要审核0关闭,1开启
define("app_mood",1);        //是否开启心情 0关闭，1开启
define("app_pagenum",20);          //后台列表每页显示个数
define("app_filter","http,//,com,cn,net,org,www");          //评论和留言本过滤内容
define("app_reg",1);        //是否开启会员注册 1开启，0关闭
define("app_regpoint",1);        //用户注册赠送点数
define("app_regstate",1);        //注册用户默认状态，1激活，0锁定
define("app_popularize",2);        //推广1个人获取积分数
define("app_popularizestate",1);        //是否开启推广赚积分1,开启 0关闭
define("app_reggroup",1);        //用户注册默认会员组

define("app_weekpoint",100);        //包周所需积分数

define("app_monthpoint",1000);        //包月所需积分数

define("app_yearpoint",5000);        //包年所需积分数

define("app_api",0);        //接口API开关1开启，0关闭
define("app_apicjflag","");        //联盟图片域名，以http:开头,/结尾，不包含upload目录
define("app_apitypefilter","");        //过滤分类参数，SQL查询条件例如 and t_hide=0
define("app_apivodfilter","");        //过滤数据参数，SQL查询条件例如 and d_hide=0
define("app_apipagenum",20);        //数据每页显示量
?>