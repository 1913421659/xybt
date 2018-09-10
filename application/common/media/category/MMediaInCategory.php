<?php
namespace app\common\media\category ;

use anywhere\MBase;

class MMediaInCategory extends MBase {
	/**
	 *
	 * @var MMediaInCategory
	 */
	static protected $instance = null;
	
	public $table	= 'channel_media_in_cat'; //database table name
	public $pk		= 'media_id,cat_id';  // primary key  of database table
	
	/**
	 *
	 * @var VOMediaInCategory
	 */
	static $vo = VOMediaInCategory::class;
}

