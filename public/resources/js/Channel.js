// JavaScript Document
var Channel = new function(){
	var $this = this;
	/* 分类列表 */
	$this.category_list = [];
	$this.getCategoryList = function(){
		return $this.category_list;
	};
	$this.flushCategoryList = function(){
		$.ajax({
			url:"?s=/index/data/channelCompanyCategoryList"
			,type:'GET'
			,success: function(rs){
				$this.category_list = rs.data;
			}
			,complete: function(rs){
			}
			,error: function(){
				console.log('获取渠道分类错误了');
			}
			,dataType:"json"
		});
	}
};