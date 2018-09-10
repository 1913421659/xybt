// JavaScript Document
var vue = new Vue({
	el:'#vue',
	data:{
		layer:0,
		user:{
			id:0,
			name:'',
			times:0, //可抽奖次数。
			used:0, 
			total:0, //获得的红包
			my_top:'-'
		},
		login_form:{
			mobile:'',
			code:'',
		},
		send_sms_wait:0,
		send_sms_wait_timer:null,
		luck_rs_awards_id:1,
		luck_rs_name:'',
		logs:[],
		top:[],//排行榜
		poster:'',
		top_page:1,
		top_page_size:20,
		top_is_end:false,
		top_is_loading:false,
	},
	methods:{
		checkUser:function(){
			if(this.user.id > 0){
				return true;
			}else{
				this.showLogin();
				return false;
			}
		},
		tixianShow:function(){
			var ua = navigator.userAgent.toLowerCase();	
			if (/iphone|ipad|ipod/.test(ua)) {
				window.location = "description_ios.html";
			} else if (/android/.test(ua)) {
				window.location = "description_android.html";
			}
//			this.layer = 18;
		},
		showLogin:function(){
			this.layer = 11;
		},
		showshre:function(){
			$(".rule_share_1").css("display","block");
			$(".rule_share_1").css("z-index","10000000");	
		},
		closeshare:function(){
			$(".rule_share_1").css("display","none");
			$(".rule_share_1").css("z-index","100");	
		},
		sendSms:function(){
			if(this.login_form.mobile==''){
				alert('请输入手机号码');
				return false;
			}
			var data = {mobile:this.login_form.mobile};
			$.post('/api/login/sendLoginSms.html', data, function(rs){
				if(rs.status){
					vue.send_sms_wait = 60;
					vue.send_sms_wait_timer = window.setInterval(function(){
						vue.send_sms_wait -= 1;
						if(vue.send_sms_wait == 0){
							window.clearInterval(vue.send_sms_wait_timer);
						}
					}, 1000);
				}else{
					alert(rs.msg);
				}
			},'json');
		},
		login:function(){
			if(this.login_form.mobile==''){
				alert('请输入手机号码');
				return false;
			}
			if(this.login_form.code==''){
				alert('请输入验证码');
				return false;
			}
			//$.post('/index/login/ajaxLogin',this.login_form, function(rs){
			$.post('/api/login/smsLogin',this.login_form, function(rs){
				if(rs.status == '1'){
					vue.layer=0;
					vue.checkLogin();
				}else{
					alert(rs.msg);
				}
			},'json');
		},
		checkLogin:function(){
			var parent_id = location.href.replace(/(.*)\.html/,'').replace(/#/,'');
			//console.log(parent_id);
			$.post('/topic_h5/lottery/check.html?parent_id='+parent_id,this.login_form, function(rs){
				if(rs.status){
					vue.user.id=rs.data.user_id;
					vue.user.name=rs.data.user_name;
					vue.user.times=rs.data.times;
					vue.user.used=rs.data.used;
					vue.user.total = rs.data.total;
					vue.user.my_top = rs.data.my_top;
					if(parent_id!==vue.user.id){
						location.href=location.href.replace(/#.*/,'') +'#'+rs.data.user_id;
					}
				}
			},'json');
		},
		showMyPoster:function(){
			if(this.checkUser()){
				this.layer=14;
				this.poster='poster.jpg?id='+this.user.id;
			}else{
				this.showLogin();
			}
		},
		showMyPrize:function(){
//			if(this.checkUser()){
				this.layer=13;
				this.top = [];
				this.top_page = 1;
				this.top_is_end = false;
				this.flushTop();
//			}else{
//				this.showLogin();
//			}
		},
		flushMyPrize:function(){
			$.post('/topic_h5/lottery/myPrize.html',{id:null}, function(rs){
				if(rs.status){
					vue.user.my_prize_list = rs.data;
				}
			});
		},
		luck:function(){
			alert('活动已结束，感谢参与');
			return;
			if(!this.checkUser()){
				this.showLogin();
				return;
			}
			if(!lottery.ready) { //click控制一次抽奖过程中不能重复点击抽奖按钮，后面的点击不响应
				return false;
			}
			if(this.user.times<=0){
				this.layer=20;
				return false;
			}
			this.luck_rs_awards_id = 0;
			this.luck_rs_name = '';
			$.post('/topic_h5/lottery/luck.html',{id:null},function(rs){
				console.log(rs);
				if(rs.status){
					vue.luck_rs_awards_id = rs.data.awards_id;
						vue.luck_rs_name=rs.data.awards_name;
					vue.user.times-=1;
					lottery.speed = 100;
					lottery.prize = rs.data.awards_id -1;
					lottery.roll(); //转圈过程不响应click事件，会将click置为false
					lottery.ready = false; //一次抽奖完成后，设置click为true，可继续抽奖
					window.setTimeout(function(){vue.flush();},2000);//两秒后刷新。
				}else{
					alert(rs.msg);
				}
			},'json');
		},
		flush:function(){
			//vue.flushMyPrize();
			vue.flushTop();
			vue.checkLogin();
		},
		showPrize:function(){
			this.layer=1;
		},
		flushTop:function(){
			if(this.top_is_loading || vue.top_is_end){
				return;
			}
			this.top_is_loading = true;
			$.post('/topic_h5/lottery/top.html?page=' + this.top_page+'&page_size='+this.top_page_size,{id:null}, function(rs){
				if(rs.status){
					for(var i in rs.data){
						vue.top.push(rs.data[i]);
					}
					if(rs.data.length < vue.top_page_size){
						vue.top_is_end = true;
					}else{
						vue.top_page += 1;
					}
					vue.top_is_loading = false;
					//vue.top = rs.data;
				}
			});
		},
		flushLogs:function(){
			$.get('/topic_h5/lottery/logs.html','',function(rs){
				if(rs.status){
					vue.logs = rs.data;
				}
			}, 'json');
		},
		again:function(){
			if(this.user.times){
				this.layer=0
			}else{
				this.layer = 20;
			}
		}
	}
});
vue.checkLogin();
//vue.flushLogs();
//vue.flushTop();
//var logs_timer = window.setInterval(vue.flushLogs, 1000);
Vue.filter('date', function (value) {
        return new Date(parseInt(value) * 1000).toLocaleDateString();//().replace(/年|月/g, "-").replace(/日/g, " ");
    })