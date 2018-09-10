// JavaScript Document
/* 任务管理 */
var Task = {
	
	url : {
		apply : '#',
	},
	//申请任务
	apply : function(task_id){
		$.ajax({
			type:'post',
			dataType:"json",
			url : Task.url.apply,
			data:{task_id:task_id},
			success: function(rs){
				if(rs.data != null && rs.data.url != 'undefined'){
					//layer.alert(rs.msg, function(index){
						location.href = rs.data.url;
					//});
				}else if(rs.msg !=''){
					layer.alert(rs.msg);
				}
			}
		});
	}
};
