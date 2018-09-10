<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Url;
use app\common\user\MUser;

function api($url, $param = [], $print = false){
    $param['sign'] = sign($param);
    $host = config('url_domain_root');
    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    $url  = $http_type.$host . url('/api/' . $url);
    # 启动一个CURL会话
    $curl = curl_init();
    # 批量设置
    $options = array(
        # 要访问的地址
        CURLOPT_URL => $url,
        # 使用浏览器标识模拟浏览器请求
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36',
        # 设置HTTPS
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        # 设置超时限制防止死循环
        CURLOPT_TIMEOUT => 10,
        # 显示返回的Header区域内容
        CURLOPT_HEADER => 0,
        # 获取的信息以文件流的形式返回
        CURLOPT_RETURNTRANSFER => 1,
        # 参数
        CURLOPT_POSTFIELDS => http_build_query($param),
        # 发送一个常规的Post请求
        CURLOPT_POST => 1,
    );
    
    # 批量设置参数
    curl_setopt_array($curl, $options);
    # 执行操作
    $result = curl_exec($curl);
    # 获取错误信息
    $error = curl_error($curl);
    # 关闭CURL会话
    curl_close($curl);
    $res = json_decode($result, true);
    if($print == true){
        dump($res);
        print_r($res);
    }
    
    if(! isset($res['code'])){
    	$res['code'] = 0;
    	$res['msg'] = '请求失败';
    	$res['data'] = [];
    }
    return $res;
}

function sign($param){
    ksort($param);
    $str = md5(http_build_query($param) . '&' . config('sign_key2'));
    return md5(config('sign_key1') . $str);
}
/**
 * 无限极分类树
 * @param droplist $list 传入数据
 */
function infinite (array $list, $option = []){
    $now    = isset($option['start'])      ? $option['start'] 	: 0;
    $id     = isset($option['id'])         ? $option['id'] 		: 'id';
    $pid    = isset($option['pid'])        ? $option['pid'] 	: 'pid';
    $child  = isset($option['child'])      ? $option['child'] 	: 'child';
    $trees  = array();
    foreach($list as $key => $value){
        if($value[$pid] == $now){
            $trees[] = $value;
            unset($list[$key]);
        }
    }
    if($trees){
        foreach($trees as $k => $v){
            $option['start'] = $v[$id];
            $array = infinite($list, $option);
            if($array){
                $trees[$k][$child] = $array;
            }
        }
    }
    return $trees;
}

/**
 * key exists echo
 */
function kee($key, $array, $default = ''){
	if(isset($array[$key])){
		echo (string) $array[$key];
	}else{
        echo $default;
    }
}

/**
 * key exists return
 */
function ker($key, $array, $default = null){
	if(isset($array[$key])){
		return $array[$key];
	}else{
		return $default;
	}
}

/**
 * 生成商品链接
 */
function shop_url($goods_id){
    $host = 'http://shop.yi-zu.com/';
    return $host . 'goods.php?id=' . $goods_id;
}


/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 		需要转换的字符串
 * @param string $start 	开始位置
 * @param string $length 	截取长度
 * @param string $charset 	编码格式
 * @param string $suffix 	截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length = 1, $charset = "utf-8", $suffix = true)
{
	if(function_exists("mb_substr")){
        if($suffix && strlen($str) > $length){
            return mb_substr($str, $start, $length, $charset) . ((strlen($str) / 3) > $length ? "..." : "");
        }else{
    		return mb_substr($str, $start, $length, $charset);
    	}
    }elseif(function_exists('iconv_substr')){
        if ($suffix && strlen($str) > $length){
            return iconv_substr($str, $start, $length, $charset) . "...";
        }else{
            return iconv_substr($str, $start, $length, $charset);
        }
    }
    $re['utf-8']   	= "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] 	= "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    	= "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   	= "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = implode("", array_slice($match[0], $start, $length));
    if($suffix){
    	return $slice . '...';
    }else{
    	return $slice;
    }
}


/**
 * nav 动态返回css样式class
 * @param $mca string m.c.a c.a a 传入小写
 * @param $class string css样式class
 */
function nav_live($mca, $class, $default = ''){
    $list = array_reverse(explode(',', $mca));
    # 获取传入的 m c a
    $aAccess = isset($list[0]) ? $list[0] : '*';
    $cAccess = isset($list[1]) ? $list[1] : '*';
    $mAccess = isset($list[2]) ? $list[2] : '*';
    # 获取请求的 m c a
    $mRequest = $mAccess == '*' ? '*' : strtolower(request()->module());
    $cRequest = $cAccess == '*' ? '*' : strtolower(request()->controller());
    $aRequest = $aAccess == '*' ? '*' : strtolower(request()->action());
    # 验证
    if(array($mRequest, $cRequest, $aRequest) == array($mAccess, $cAccess, $aAccess)){
        return $class;
    }else{
        return $default;
    }
}

/**
 * 获取图片的url;
 * @author 谭武云
 * @date 2017年9月16日
 * @param unknown $url
 * @return string
 */
function get_image_path($image='', $path = ''){
// 	return $image;
	if (strtolower(substr($image, 0, 4)) == 'http') {
		$url = $image;
	}elseif($image == ''){
		$url = $image;
	}elseif(config("shop.open_oss")) {
// 		
	
		$url = '/' . $image;
		if (config('shop.check_oss') == 1) {
			$url = FileUpload::getOssUrl($image);
		}else{
			$path = (empty($path) ? '' : rtrim($path, '/') . '/');
			$bucket_info = get_bucket_info();
			$url = rtrim($bucket_info['endpoint_url'], '/') . '/' . $path . $image;
		}
	}else{
		$url = $image;
	}
	return $url;
}
function get_bucket_info()
{
	$res = db('oss_configure')->where('is_use',1)->limit(1)->find();
    $endpoint_url='';
	if ($res) {
		$regional = substr($res['regional'], 0, 2);
		if (($regional == 'us') || ($regional == 'ap')) {
			$res['outside_site'] = 'https://' . $res['bucket'] . '.oss-' . $res['regional'] . '.aliyuncs.com';
			$res['inside_site'] = 'https://' . $res['bucket'] . '.oss-' . $res['regional'] . '-internal.aliyuncs.com';
		}else {
			$res['outside_site'] = 'https://' . $res['bucket'] . '.oss-cn-' . $res['regional'] . '.aliyuncs.com';
			$res['inside_site'] = 'https://' . $res['bucket'] . '.oss-cn-' . $res['regional'] . '-internal.aliyuncs.com';
		}
        if($res['is_cname']==1&&!empty($res['site_domain'])){
            $endpoint_url='https://'.$res['site_domain'];
        }else{
            $endpoint_url=$res['outside_site'];
        }
	}
    if ($res) {
    	//用内网或者外网访问操作oss
        if($res['is_intranet']==1){
            $res['endpoint']=$res['endpoint_inside'];
        }else{
            $res['endpoint']=$res['endpoint_outside'];
        }
        //oss的访问路径
        $res['endpoint_url']=$endpoint_url.'/';
	}
	return $res;
}

function array2tree(&$list, $id_name='id', $parent_id_name='parent_id', $children_list_name='children_list'){
	foreach ($list as $k => $v){
		$keys[$v[$id_name]] = & $list[$k];
		$list[$k][$children_list_name] = [];
	}
	foreach ($list as $k => $v){
		if(isset($keys[$v[$parent_id_name]])){
			unset($list[$k]);
			$keys[$v[$parent_id_name]][$children_list_name][] = & $keys[$v[$id_name]];
		}
	}
}

//蚁币转换成货币
function yibi2money($yibi=0){
	$money=number_format($yibi/100,2,'.','');
	return $money;
}

function getOneAdByPositionName($ap_name = ''){
    $row = db('ad')->alias('a')->join('ad_position ap', 'ap.position_id = a.position_id', 'left')
    ->where('ap.position_name',$ap_name)->limit(1)->field('a.*')->find();
    return $row;
}

/**
 * 返回文章URL
 * @param unknown $art_id
 * @return string
 */
function getArtLink($art_id){
	return 'http://shop.yi-zu.com/article_cat.php?id=' . $art_id;
}

/**
 * 据当前时间多久
 * @param int $diffTime  //对比时间时间戳
 * @param int $nowTime //当前时间，默认是当前时间  时间戳
 * @return Stirng $str //时间差
 */
function formatDateDiffNow($diffTime,$nowTime=null){
    $nowTime = empty($nowTime)?time():$nowTime;
    $diff = $nowTime-$diffTime;
    $str = '';
    if($diff<60){
        $str = $diff.'秒前';
    }else if($diff>=60 && $diff<60*60){
        $str = floor($diff/60).'分前';
    }else if($diff>=60*60 && $diff<60*60*24){
        $str = floor($diff/60/60).'时前';
    }else if($diff>=60*60*24 && $diff<60*60*24*3){
        $str = floor($diff/60/60/24).'天前';
    }else{
        $str = date('Y-m-d H:i:s',$diffTime);
    }

    return $str;
}

/**
 * 重定义URL函数，拼接上当前用户的
 * @author darkcloud.tan
 */
function url($url = '', $vars = '', $suffix = true, $domain = false){
	$mod_user = MUser::getInstance();
	$user = $mod_user->getLogined();
    if($user){
        if(is_array($vars)){
        	$vars['parent_id'] = $user->user_id;
        }else{
        	$vars .= '&parent_id=' . $user->user_id;
        }
    }
	return Url::build($url, $vars, $suffix, $domain) ;
}

/**
 * 生成邀请码
 */
function inviteCode()
{
    $chars = '123456789abcdefghijklmnopqrstuvwxyz';
    $len = 6;
    mt_srand(10000000 * (double)microtime());
    $str = '';
    for ($i = 0, $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    $code = $str.time();
    $id = db('user_invitation')->where(['code'=>$code])->field('id')->find();
    if(!empty($id)){
        inviteCode();
    }
    return $code;
}

//判断是否存在目录，不存在则创建
function mkdirs($path,$mode = 0777){
    if(!file_exists($path)){
        //创建目录
        if(mkdir($path,$mode,true)){
            return true;
        }else{
            return false;
        }
    }else{
        return true;
    }
}

//获取用户vip等级以及等级称谓,$type=1获取称谓，2,获取vip
function getUserLevel($rank_id=0,$type=1){
    $rankInfo = db('user_rank')->where(['rank_id'=>$rank_id])->field('rank_name,sort_order')->find();
    if($rank_id==0 || empty($rankInfo)) return '';
    if(!in_array($type,[1,2])) return '';
    if($type==1){
        return $rankInfo['rank_name'];
    }else{
        return 'VIP'.$rankInfo['sort_order'];
    }
}

//替换textarea中的回车换行，
function format_bl2br($content){
    $content = str_replace("\r\n",'',$content);
    $content = str_replace("\n\r",'',$content);
    $content = str_replace("\n",'',$content);

    return $content;
}

//记录登录日志
function addUserLoginLog($user_id,$user_name,$nick_name,$mobile_phone,$mobile_bind,$user_picture,$phone_device_id,$phone_brand,$phone_model,$company_id,$company_name,$login_type){
    $data = [
        'user_id'=>$user_id ,
        'user_name'=>$user_name,
        'nick_name'=>$nick_name,
        'mobile_phone'=>$mobile_phone,
        'mobile_bind'=>$mobile_bind,
        'user_picture'=>$user_picture,
        'phone_device_id'=>$phone_device_id,
        'phone_brand'=>$phone_brand,
        'phone_model'=>$phone_model,
        'company_id'=>$company_id,
        'company_name'=>$company_name,
        'os_type'=>3,
        'ip'=>$_SERVER['REMOTE_ADDR'],
        'login_type'=>$login_type,
        'add_time'=>time()
    ];
    return db('user_login_log')->insert($data);
}

//渠道派单生成订单规则，参考app的生成规则
function getOrdersn(){
    $ordersn = time();
    for($i=0;$i<6;$i++){
        $ordersn .= mt_rand(0,9);
    }
    $id = db('channel_company_order')->where(['ordersn'=>$ordersn])->field(['id'])->find();
    if(!empty($id)){
        getOrdersn();
    }
    return $ordersn;
}
