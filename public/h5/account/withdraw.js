// JavaScript Document

//用户基础信息
vue.append({
	user:{
		user_id:0,
		user_name:'-',
		user_picture:null,
		user_rank:0,
		yi_currency_total:'-',
		reward_total:'-',
		cutting_total:'-',
		user_money:'-',
	},
	getUserBase:function(){
		$.post("/api/user/baseInfo",{null:null},function(rs){
			if(rs.status){
				vue.user = rs.data;
				vue.setUserRankName();
			}
		}, 'json');
	},
});
vue.addInit('getUserBase');

//等级
vue.append({
	vip:0,
	rank_name:'-',
	user_rank_list : [],
	getUserRankList:function(){
		$.post("/api/user/rankList",{null:null},function(rs){
			if(rs.status){
				vue.user_rank_list = rs.data;
				vue.setUserRankName();
			}
		}, 'json');
	},
	setUserRankName:function(){
		if(this.user.user_rank > 0 && this.user_rank_list.length>0){
			for(var i=0;i<this.user_rank_list.length;i++){
				if(this.user_rank_list[i].rank_id == this.user.user_rank){
					vue.rank_name = this.user_rank_list[i].rank_name;
					break;
				}
			}
		}
	},
});
vue.addInit('getUserRankList');


/**
 * 钱包相关功能
 */
vue.append({
	task_list:[],
	getTaskList:function(){
		$.post("/api/task/listInWithraw",{null:null},function(rs){
			if(rs.status){
				vue.task_list = rs.data;
			}
		}, 'json');
	}
});
vue.addInit('getTaskList');

//标签页功能
vue.append({
	tab_index:0,//标签页号
	changeTab:function(n){
		this.tab_index = n;
	},
});