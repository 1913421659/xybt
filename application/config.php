<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
# 兼容模式
\think\Url::root($_SERVER['PHP_SELF'] . '?s=');
// if(!defined('XYBT_MOBILE_HOST')){
// 	define('XYBT_MOBILE_HOST','http://shop.yi-zu.com/mobile');
// }
return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => '',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'index',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => '',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
	'url_domain_root'        => $_SERVER['HTTP_HOST'],//'shopdev.yi-zu.com',
	// 商城url
	'url_shop_root' => 'http://shop.yi-zu.com/',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => true,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => [],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [
        '__RESOURCES__' => '/public/resources/',
    ],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => false,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],


    // +----------------------------------------------------------------------
    // | 个人设置
    // +----------------------------------------------------------------------

    'sign_key1' => 'asd65*2345sad4Li9saudi_009090s8a9kjh298(&*02kjk',
    'sign_key2' => '9+6dsadw^564sa5d523jhjehLI/sdasdw02415454da6452',


	'shop' => [
		'open_oss' => true,
		'check_oss' => false,
	],
    'alipay'=>[
        'app_id'=>'2017120400371357',
        'merchant_private_key'=>'MIIEpAIBAAKCAQEA1SEqeloQ2LSEErX+iNuNQP9dULInV4sZUXlUh+FSyOztIiTdnDaQTa8ko1C7Ezh+9B7xQAff1k7zI5FZKvnRj1VTP++YD6FrwTkYnC1qLJCFijmddCM7KxLEfKMcjnFtVyaF3CDjggNtakPNKERhNH+fD0FlQPNr5G02xOA7qx+3IlpE1b3GQiAyQ2guaBEU5Nckk68eO5RwF6PccyzuSoYbb4XrIat9SIzXzqOMryl33mIvjt9v6/q6Cg1hGIpz8UV1zxIM36PExRIrbELWNeUevu/PLTztXXu7hcbh9jDdOjdVVaCbrZozLWWHPwz4k5O3iSUziV1JU2efX3otqwIDAQABAoIBAQCEWGwAPh1n8/BvjgPZCDzWt7xCd95mRkIajbUoC4tUqYs3QJ08e8Vv9+pQX7lLXjG3dd9pW3proerpk0BDp42x69IeSbGrQxmeis31bV4Y/kVuaAiWLap4PNc2bjM1YMn87DcDUoj0Gxy+eAvVjWCvMPCckIdHpeDBn+/6oSNsRstK59JNxQtS3EZCd4bdO97UOPbfuazoUs3oMFUVbyxFahtLFzJZvsaQCH3ft68HHN46fZ5r6W2ZHAMTwy1k+7ro5A/BS9ncenPmyXODzg9KlW1dbmKF8hknliPfuR290vv3DYT4WXm7L6fap2P36BfWT4z8NyQMqJOOS6XforcBAoGBAO9XRGHJzaVZfH9vIVbpu7jsKnuH1hKvxHwX9qdBp3cGybCukSFYX6MkkNE4ujzEFcPHnnHH5gJ6xPajdws96gqUMJ8mpMl16XiLLm+FcJC0w3o/tNelACCTu6UrC+j7PF1BcxOBJY2izxcqZhk0K3+ip2s9yLBzBZIEmlO0NvPRAoGBAOP22RzVip4Q5sYcgY/aG5yWV4rY6odhvjMjENEcwszwKE7XYpywlHfdVcRoYhk5J+Z3tyeMpjtagOvpyzgPnG/GqxpcXTCj00GZkqWgDjA2kkTP+jMjqQ2lRQXCxQ2rlsjhARLw7HkNgI9XMfbUGjCIzkiymdsUpyBkZWZe3dS7AoGBAJJtQnHNQ9o4YDmapLwGrYmaOvduiGLQVBZTltyHB0yDw5srgbDz3zbxZQQLf60zjdlEZERaNvcnYx89rNQc4fXs6b5EpyZ11AuioxspeU+et8Uv+pS/5s8HcxK+vj+gjTLEmwHAzlfT1nzmvKYIy9//hAvRNdn11N+bn0s8gy4xAoGAYnWxnISW1GKUaijRxOH9XwqIUQ4TbdnHnqqcjtUPRhjMMBFTJD4YQhU1ABndlOtc0mwOjWRwP959JGfcQCBt/tEeAcq27VU033aHIkYZGrXXQyVY+VGDqMMjJrPTDG56N9UG87lfSYGNb1vH1sYRcbkE791EGp22+YSjTy3WPhUCgYA+4UfWekUhNOf+FbfPUxV1jCErtGMgM6UF5Fs85uHOglUFcs//zjqF+6I8j36R+7Jz+nzeaCuPJNqZKhD7yV45qZ0m9fKRczl+8fTzdEQLMh75uCzmXilBC0gDn2s5fSbM79QFRkdjhNRuTA9Ql4QKlTb22hgiSFLEI+XZXrhZUg==',
        'alipay_public_key'=>'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAv6MukoCH15/TUqwEjrOOQjZ6wjlky2JfXX1CS53W9ZKBQZYTl51D73nouG+Dm1GG2rZEsSfoAtnw21sI00O9MpexmDugPqqNTz3hrYtrE+MQXku9Yzx+gr1NgCkzZI8s+E+UU4Cpo64u6ih52xmTopeWE7ccaPYkyacHD5E9QlmTqAU/Wz6qSc+yRA54B2C2lsyUrvrUTga/GFHhZaEdEc0W62JzzH3y7Yh8lfTYH2Pq7gusADCb5S2nIb1TPmXd2ILSY9/rTqyh3hAl2I4dq1ThcxMUEfV3uNSDz4RSUsy59L0p/lwrJk8rSsh/1djHIYUkfuGOX7uHnaNi5Y8gcwIDAQAB',
        //异步通知地址
        'notify_url' => (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/alipay_notify.php',
        //同步跳转
        'return_url' => (((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].'/index.php?s='.urlencode('index/company/com_recharge'),
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type'=>"RSA2",
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
    ]




];
