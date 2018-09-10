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
		alipay_bind:0,
		alipay_name:''
	},
	can_withdraw:0,
	flushUserBase:function(){
		$.post("/api/user/baseInfo",{null:null},function(rs){
			if(rs.status){
				vue.user = rs.data;
				vue.setUserRankName();
				vue.can_withdraw = Math.floor(vue.user.yi_currency_total/100);
			}else{
				$.cookie('login_goto','/h5/account/index.html', { path: "/"});
				location.href='/h5/login/index.html#login';
			}
		}, 'json');
	},
});
vue.addInit('flushUserBase');

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