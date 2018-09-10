<?php
namespace app\common\user\company ;
use \anywhere\MBase;
use phpqrcode\QRcode;

/**
 * 
 * @author 乌大湿 
 * @date 2017年12月14日
 * 神兽庇佑，bug無有~！
┏┛┻━━━┛┻┓
┃｜｜｜｜｜｜｜┃
┃　　　━　　　┃
┃　┳┛　┗┳　┃ 
┃　　　　　　　┃
┃　　　┻　　　┃
┃　　　　　　　┃
┗━┓　　　┏━┛
　　┃　　　┃
　　┃　　　┃
　　┃　　　┃
　　┃　　　┃
　　┃　　　┗━━━┓
　　┃　　　　　　　┣┓
　　┃　　　　　　　┃
　　┗┓┓┏━┳┓┏┛
　　　┃┫┫　┃┫┫
　　　┗┻┛　┗┻┛
 */

class Model extends MBase {
	public $table	= 'user_company'; //database table name
	public $pk		= 'id';  // primary key  of database table
	
	/**
	 * 
	 * @var VO
	 */
	static $vo = VO::class;
	
	/**
	 *
	 * @var Model
	 */
	static protected $instance = null;
	
	static private $qr_err_level = '3';//二维码容错等级
	static private $qr_size = 6;//尺寸
	
	/**
	 * 创建二维码
	 * @param unknown $company_id
	 */
	static public function createQRcode($company_id){
		if($company_id <= 0){
			throw new \Exception('参数错误', 9000);
		}
		$text = [
			'code'        => 'joinCompany',
			'company_id' => $company_id
		];
		
		$website_root = ROOT_PATH . 'public/';
		$path = 'static/qrcode_img/company/';
		@$return = mkdir($website_root.$path, 0777, true);//创建目录
		$logo = $website_root . 'static/qr_logo.jpg';//中间的logo
		$qrcode = $website_root . $path . 'company_' . $company_id . '.png';
		$qrcode_last = $path . 'company_' . $company_id . '.png';
		QRcode::png(json_encode($text), $qrcode, self::$qr_err_level, self::$qr_size, 2);
		//生成logo
		$qrcode = imagecreatefromstring(file_get_contents($qrcode));
		$logo = imagecreatefromstring(file_get_contents($logo));
		$qrcode_width = imagesx($qrcode);
		$qrcode_height = imagesy($qrcode);
		$logo_width = imagesx($logo);
		$logo_height = imagesy($logo);
		$logo_qr_width = $qrcode_width / 5;
		$scale = $logo_width / $logo_qr_width;
		$logo_qr_height = $logo_height / $scale;
		$from_width = ($qrcode_width - $logo_qr_width) / 2;
		imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
		imagepng($qrcode, $qrcode_last);//生成最终的文件
		\FileUpload::upload($qrcode_last, $qrcode_last);//将二维码上传到aliyunoss服务器
		return $qrcode_last;
	}
	
	static public function getOneById($company_id){
		$company = db('user_company')->where('id', $company_id)->find();
		if(!$company){
			throw new \Exception('数据不存在', 9000);
		}
		if($company['qrcode_path'] == null || $company['qrcode_path'] == ''){
			$company['qrcode_path'] = self::createQRcode($company['id']);
			db('user_company')->where('id', $company_id)->update(['qrcode_path'=>$company['qrcode_path']]);
		}
		return $company;
	}
	
}
