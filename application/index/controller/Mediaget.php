<?php
namespace app\index\controller;

use app\common\media\Model as MMedia;
use \Exception;
use app\common\media\VOBBS;
use app\common\media\VOWB;
use app\common\media\VOWX;
use app\common\media\VOPYQ;
use app\common\media\VOBL;
use app\common\media\VOSP;
use anywhere\FW;
use app\common\media\VO;

/**
 * 资源抓取
 * @author darkcloud.tan
 *
 */
class Mediaget extends Common{
	
	private function resolver($url, &$page_end){
		$html = str_replace('
','',file_get_contents($url));
		if(preg_match("/<a.*?page=(\d*)('|\")[^>]*?>尾页<\\/a>/i", $html, $matches)){
			$page_end= $matches[1];
		}else{
			$page_end= 1;
		}
		if(!preg_match("/<table\sclass=\\\"fixedtable\\\".*?<\\/table>/i", $html, $matches)){
			throw new Exception('分析不出表格！', __LINE__);
		}
		$html = $matches[0];
		if(!preg_match_all("/<tr\sclass=\"(?:GValter|GVrow)\".*?>(.*?)<\\/tr>/i", $html, $matches)){
			throw new Exception('分析不出表格行', __LINE__);
		}
		$data = array();
		foreach($matches[0] as $k => $v){
			if(!preg_match_all("/<td.*?>.*?<\\/td>/i",$v, $m)){
				throw new Exception('分析不出表格列',__LINE__);
			}
			$data[] = $m[0];
		}
		return $data;
	}
	
	/**
	 * 论坛资源
	 * @return mixed|string
	 */
	public function check1(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/LTXG.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VOBBS();
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id 	= $m_td[2]; //ID
				$vo->site_name 	= trim(strip_tags($v[3])); //站点名称
				$vo->forum 		= trim(strip_tags($v[4])); //版块名称
				$vo->link 		= trim(strip_tags($v[5])); //版块链接
				$vo->desc 		= trim(strip_tags($v[6]));
				//价格
				$vo->price_top 	= floatval(trim(strip_tags($v[7])));
				$vo->price_best = floatval(trim(strip_tags($v[8])));
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check1', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e->getTraceAsString());
		}
	}
	public function checkcat(){
		$list_url = [
			'',
			'http://www.zimeiticlub.com/Resource/LunTan/0-{$cat_id}-0-0-0-0-0-0-0.html?page={$page}',
			'http://www.zimeiticlub.com/Resource/WeiBo/0-0-0-{$cat_id}-0-0-0-0-0-0-0-0.html?gourl=by&ch=&page={$page}',
			'http://www.zimeiticlub.com/Resource/weiXin/0-{$cat_id}-0-0-0-0-0-0-0-0-0-0-0-0-0.html?gourl=by&ch=&page={$page}',
			'http://www.zimeiticlub.com/Resource/PYQ/{$cat_id}-0-0-0-0-0-0.html?gourl=by&page={$page}',
			'http://www.zimeiticlub.com/Resource/blogs/0-0-{$cat_id}-0-0-0-0-0-0.html?gourl=by&page={$page}',
			'http://www.zimeiticlub.com/Resource/Video/0-0-{$cat_id}-0-0-0.html?gourl=by&page={$page}',
			'http://www.zimeiticlub.com/Resource/MiaoPai/0-{$cat_id}-0-0-0-0-0-0-0.html?gourl=by&page={$page}',
			'http://www.zimeiticlub.com/Resource/TouTiao/0-0-0-{$cat_id}-0-0-0-0-0-0-0-0.html?gourl=by&ch=&page={$page}',
		];
		try{
			$model = MMedia::getInstance();
			$type_id = request()->param('type_id', 1);
			$page = request()->param('page', 1);
			$cat_id = request()->param('cat_id', 1);
			$url = str_replace('{$cat_id}', $cat_id, str_replace('{$page}', $page, $list_url[$type_id]));
			$rows = $this->resolver($url, $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			$list_insert = [];
			$list_old_id = [];
			foreach ($rows as $k => $v){
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$list_old_id[] = $m_td[2];
			}
			$list = db('channel_media')
				->where('old_id','in', $list_old_id)
				->where('type_id', $type_id)
				->column('id', 'old_id');
			foreach ($list as $k => $v){
				$list_insert[] = [
					'media_id' => $v,
					'cat_id' =>  $type_id * 100 + $cat_id
				];
			}
			db('channel_media_in_cat')->insertAll($list_insert, true);
			if($page < $page_end){
				$url = url('mediaget/checkcat', [
					'page' => $page+1,
					'type_id' => $type_id,
					'cat_id' => $cat_id
				]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			throw $e;
// 			\anywhere\FW::debug($e->getTraceAsString());
		}
	}
	
	/**
	 * 微博资源
	 * @return mixed|string
	 */
	public function check2(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/WBZY.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VOWB();
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				$vo->nickname = trim(strip_tags($v[4]));//昵称
				$vo->site_name = trim(strip_tags($v[2]));//站点名称
				//版块链接
				if(!preg_match("/<a.*?href=('|\")(.*)\\1/i", $v[4], $m_td)){
					throw new Exception('获取链接失败，' . var_export($v, true), __LINE__);
				}
				$vo->link = trim($m_td[2]);
				//价格
				$vo->price_1 = floatval(trim(strip_tags($v[6])));
				$vo->price_2 = floatval(trim(strip_tags($v[7])));
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check2', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	
	/**
	 * 微信（公众号）资源
	 * @return mixed|string
	 */
	public function check3(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/WXZY.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VOWX();
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				//头像
				if(preg_match("/<img.*?\ssrc=('|\")(.*?)('|\")/i", $v[2], $m_td)){
					$vo->logo = $m_td[2];
				}
				
				$vo->nickname = trim(strip_tags($v[3]));//昵称
				$vo->wx_id = trim(strip_tags($v[4]));//站点名称
				$vo->power = trim(strip_tags($v[5]));
				if(strpos($vo->power, '万')){
					$vo->power = str_replace('万','',$vo->power);
				}
				$vo->power = intval(floatval($vo->power) * 10000);
				//价格
				$vo->price_1 = floatval(trim(strip_tags($v[7])));
				$vo->price_2 = floatval(trim(strip_tags($v[8])));
				$vo->price_3 = floatval(trim(strip_tags($v[9])));
				$vo->power_2= intval(trim(strip_tags($v[9])));
				//二维码
				if(preg_match("/<img.*?\ssrc=('|\")(.*?)('|\")/i", $v[10], $m_td)){
					$vo->qr_img = $m_td[2];
				}
				
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check3', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	
	/**
	 * 微信（朋友圈）资源
	 * @return mixed|string
	 */
	public function check4(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/PYQ.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VOPYQ();
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				//头像
				if(preg_match("/<img.*?\ssrc=('|\")(.*?)('|\")/i", $v[2], $m_td)){
					$vo->logo = $m_td[2];
				}
				$vo->nickname = trim(strip_tags($v[3]));//昵称
				$vo->wx_id = trim(strip_tags($v[4]));//微信号
				$vo->power = intval(trim(strip_tags($v[5])));
				//价格
				$vo->price = floatval(trim(strip_tags($v[7])));
				$vo->profession = trim(strip_tags($v[8]));
				$vo->age = intval(trim(strip_tags($v[9])));
				// 				\anywhere\FW::debug($vo);exit;
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check4', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	
	
	
	/**
	 * 博客资源
	 * @return mixed|string
	 */
	public function check5(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/MBZY.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VOBL();
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				$vo->site_name = trim(strip_tags($v[2]));//昵称
				$vo->nick_name = trim(strip_tags($v[4]));//微信号
				$vo->link = trim(strip_tags($v[5]));
				$vo->power = intval(trim(strip_tags($v[6])));
				//价格
				$vo->price = floatval(trim(strip_tags($v[7])));
				// 				\anywhere\FW::debug($vo);exit;
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check5', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	
	/**
	 * 视频资源
	 * @return mixed|string
	 */
	public function check6(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/SPZY.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VOSP();
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				$vo->site_name = trim(strip_tags($v[2]));//网站名
				$vo->enter_level = trim(strip_tags($v[4]));//入口等级
				$vo->enter = trim(strip_tags($v[5]));//入口位置
				$vo->link = trim(strip_tags($v[6]));
				$vo->time = trim(strip_tags($v[7]));
				//价格
				$vo->price = floatval(trim(strip_tags($v[8])));
				// 				\anywhere\FW::debug($vo);exit;
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check6', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	/**
	 * 直播资源
	 * @return mixed|string
	 */
	public function check7(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/MIAOPAIZY.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VO();
				$vo->type_id = 7;
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				//头像
				if(preg_match("/<img.*?\ssrc=('|\")(.*?)('|\")/i", $v[2], $m_td)){
					$vo->logo = $m_td[2];
				}
				//平台
				//昵称
				$vo->site_name = trim(strip_tags($v[4]));//网站名
				//链接
				$vo->link = trim(strip_tags($v[5]));
				//粉丝数
				$vo->power = floatval(strip_tags($v[6])) * 10000;
				//价格
				$price = trim(strip_tags($v[7]));
				if(preg_match("/^直发价格：(.*?)(元)?转发价格：(.+?)(元)?$/i", $price, $matches)){
					$vo->price_1 = floatval($matches[1]);
					$vo->price_2 = floatval($matches[3]);
				}
				$vo->history = intval(strip_tags($v[11]));
				// 				\anywhere\FW::debug($vo);exit;
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check7', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	/**
	 * 头条资源
	 * @return mixed|string
	 */
	public function check8(){
		try{
			$model = MMedia::getInstance();
			$page = request()->param('page', 1);
			$rows = $this->resolver("http://www.zimeiticlub.com/TTZY.aspx?page={$page}", $page_end);
			// 			\anywhere\FW::debug($rows);exit;
			foreach ($rows as $k => $v){
				$vo = new VO();
				$vo->type_id = 8;
				//ID
				if(!preg_match("/value=('|\")(\d*)/i", $v[0], $m_td)){
					throw new Exception('获取ID失败，' . var_export($v, true), __LINE__);
				}
				$vo->old_id = $m_td[2];
				//头像
				if(preg_match("/<img.*?\ssrc=('|\")(.*?)('|\")/i", $v[2], $m_td)){
					$vo->logo = $m_td[2];
				}
				//昵称
				$vo->title = trim(strip_tags($v[3]));
				//链接
				$vo->link = trim(strip_tags($v[4]));
				//粉丝数
				$vo->power = floatval(strip_tags($v[5])) * 10000;
				//阅读数
				$vo->profession = trim(strip_tags($v[6]));
				//头条指数
				$vo->age = trim(strip_tags($v[7]));
				//价格
				$vo->price_1 = floatval(trim(strip_tags($v[8])));
				$vo->history = intval(strip_tags($v[10]));
// 								\anywhere\FW::debug($vo);exit;
				$model->saveFromGather($vo);
			}
			if($page < $page_end){
				$url = url('mediaget/check8', ['page'=>$page+1]);
				$this->assign('url', $url);
				return $this->fetch('check1');
			}else{
				die('complete-' . $page_end);
			}
		}catch(Exception $e){
			\anywhere\FW::debug($e);
		}
		exit;
	}
	
	public function reset8(){
		return;//已写入。
		$file_path = ROOT_PATH . '/extend/caiji/caiji_8.txt';
		if(file_exists($file_path)){
			$fp = fopen($file_path,'r');
			while(1){
				$row = fgets ($fp);
				if($row === false){
					fclose($fp);
					//@unlink($file_path);
					break;
				}
				$data = json_decode(trim($row), 1);
				if(!is_array($data)){
					throw new \Exception('无效数据'.$row);
				}
				$v = db('channel_media')->where('type_id', 8)->where('old_id', $data['old_id'])
				->find();
				if($v){
					$old_id = $data['old_id'];
					unset($data['old_id']);
					db('channel_media')->where('type_id', 8)->where('old_id', $old_id)
					->update($data);
				}else{
					$data['type_id'] = 8;
					db('channel_media')->insert($data);
// 					echo db()->getLastSql(). "<br/>\r\n";
				}
			}
		}else{
			echo $file_path;
		}
	}
}

