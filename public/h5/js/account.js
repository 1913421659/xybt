// JavaScript Document
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

//提现
vue.append({
	withdraw_method : 0,
	withdraw_money : '',
	changedWithdrawMoney:function(){
		this.withdraw_money = this.withdraw_money.replace(/\..*$/,'').replace(/[^0-9]/,'');
	},
	changeWithdrawmethod:function(n){
		if(n == 1 && !vue.user.alipay_bind){
			vue.goto('wallet_bind_withdraw_alipay');
			return;
		}
		this.withdraw_method = n;
		vue.goto('wallet_withdraw');
	},
	withdrawGo:function(){
		this.changedWithdrawMoney();
		if(!/^[0-9]+$/.test(this.withdraw_money)){
//			alert('请输入有效的金额！');
			$.toast("请输入有效的金额", "text");
			return false;
		}
		if(this.withdraw_money > this.can_withdraw){
//			alert('提现金额不得大于可提现金额！');
			$.toast("提现金额不得大于可提现金额！", "text");
			return;
		}
		$.post("/api/user/withdraw",{
			method : this.withdraw_method,
			money : this.withdraw_money,
		},function(rs){
			alert(rs.msg);
				vue.flushUserBase();
			if(rs.status){
				this.alertShow = true;
				vue.goto('wallet');
			}
		}, 'json');
	},
});
//绑定支付宝
vue.append({
	new_alipay_name : '',//新绑定的支付宝账号
	bindAlipay:function(){
		if(this.user.alipay_bind){
//			alert('您已绑定过支付宝，无法重新绑定。');
			$.toast("您已绑定过支付宝，无法重新绑定。", "text");
			return false;
		}
		if(this.new_alipay_name == ''){
//			alert('请输入支付宝账号');
			$.toast("请输入支付宝账号", "text");
			return false;
		}
		$.post("/api/user/bindAlipay",{v:this.new_alipay_name},function(rs){
			if(rs.status){
				vue.flushUserBase();
				vue.goto('wallet_withdraw_methods');
			}
		}, 'json');
	},
});
//提现成功弹窗
vue.append({
	alertShow:false,
	colseCover:function(){
		this.alertShow = false;
	}
	
});
