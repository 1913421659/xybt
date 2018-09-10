<?php
namespace app\common\user ;
use \anywhere\VOBase;
/**
 * 
 * @author 乌大湿 
 * @date 2017年11月03日
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
 */class VOUserRank extends VOBase {
	public $rank_id 	= null;
	public $rank_name 	= null;
	public $min_points 	= null;
	public $max_points 	= null;
	public $discount 	= null;
	public $show_price 	= null;
	public $special_rank 	= null;
	public $reward_factor 	= null;
	public $cutting_factor 	= null;
	public $task_sum 	= null;
	public $sort_order 	= null;

	static protected $db_fields = array(
		'rank_id' 	=> 'rank_id',
		'rank_name' 	=> 'rank_name',
		'min_points' 	=> 'min_points',
		'max_points' 	=> 'max_points',
		'discount' 	=> 'discount',
		'show_price' 	=> 'show_price',
		'special_rank' 	=> 'special_rank',
		'reward_factor' 	=> 'reward_factor',
		'cutting_factor' 	=> 'cutting_factor',
		'task_sum' 	=> 'task_sum',
		'sort_order' 	=> 'sort_order'
	);
}