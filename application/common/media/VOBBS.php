<?php
namespace app\common\media;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月08日
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
 */class VOBBS extends VOBase {
	public $id 			= null;
	public $type_id 	= 1;
	public $cat_id 		= null;
	public $old_id 		= null;
	public $site_name= null;//论坛名
	public $forum 		= null;//版块
// 	public $logo 		= null;//头像
// 	public $qr_img 		= null;
	public $link 		= null;//版块链接
	public $desc 		= null;
// 	public $power 		= null;
	public $price_top 	= null;//置顶价格
	public $price_best 	= null;//加精价格
// 	public $price_3 	= null;
	public $history 	= null;//接单数

	static protected $db_fields = array(
		'id' 		=> 'id',
		'type_id' 	=> 'type_id',
		'cat_id' 	=> 'cat_id',
		'old_id' 	=> 'old_id',
		'site_name' 		=> 'title',
		'forum' 	=> 'title_sub',
// 		'logo' 		=> 'logo',
// 		'qr_img' 	=> 'qr_img',
		'link' 		=> 'link',
		'description' 		=> 'description',
		'power' 	=> 'power',
		'price_top' => 'price_1',
		'price_best' 	=> 'price_2',
// 		'price_3' 	=> 'price_3',
		'history' 	=> 'history'
	);
}