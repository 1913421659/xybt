<?php
namespace app\common\media\category ;
use \anywhere\VOBase;

class VOMediaInCategory  extends VOBase {
	public $media_id= null;
	public $cat_id= null;
	
	static protected $db_fields = array(
		'media_id' 	=> 'media_id',
		'cat_id' 	=> 'cat_id',
	);
}