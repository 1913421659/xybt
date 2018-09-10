// JavaScript Document
User = {
	url_ajax:{
		logout:null,//ajax登出的请求地址，由于框架限制，由页面上的js代码赋值，参照模板目录public/js.html
		login:null
	},
	ajaxLogout : function(){
		$.get(User.url_ajax.logout, {}, function(rs){
			if(rs.status){
				location.href = rs.data.url;
			}else{
				layer.alert(rs.msg);
			}
		});
	},
	
};
User.Login = {
	lay_loading : null,
	ajaxPost : function(){
		var _data = $('#form_login').serialize();
		User.Login.lay_loading = layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
		$.ajax({
			type:'post',
			dataType:"json",
			data:_data,
			url:User.url_ajax.login,
			success: function(rs){
				if(rs.status == 1){
					location.href = rs.data.url;
				}else{
					if(rs.err == 9002){
						User.Login.View.mobile_err = 3;
					}else if(rs.err == 9003){
						User.Login.View.password_err = 3;
					}else{
						layer.alert(rs.msg);
					}
				}
			},
			complete: function(rs){
				layer.close(User.Login.lay_loading);
			}
		});
		return false;
	},
	View : {
		mobile_err :0,
		password_err : 0,
	},
	init : function(){
		User.Login.View = new Vue({
			el:'#form_login',
			data:User.Login.View,
			methods : {
				submit : function(){
					this.checkPhone();
					this.checkPassword();
					if(this.mobile_err ==0 && this.password_err == 0){
						User.Login.ajaxPost();
					}
				},
				checkPhone : function(){
					var _mobile = $('#user_name').val();
					if(_mobile == ''){
						this.mobile_err = 1;
					}else if(!(/^1[34578]\d{9}$/).test(_mobile)){
						this.mobile_err = 2;
					}else{
						this.mobile_err = 0;
					}
				},
				checkPassword : function(){
					var _password = $('#password').val();
					if(_password == ''){
						this.password_err = 1;
					}else if(/^.{1,4}$/.test(_password)){
						this.password_err = 2;
					}else{
						this.password_err = 0;
					}
				}
			}
		});
	}
};
User.Team = {
	url_ajax : {
		list : null
	},
	page_list : {
		type : 0,
		page : 1,
		page_size : 10,
		search : '',
		is_empty : false,
		is_loading : true,
		list : [],
		last_page : 1,//假设有第二页
	},
	bindList : function(){
		User.Team.page_list = new Vue({
			el:'#team_box',
			data:User.Team.page_list
		});
		User.Team.flushList();
	},
	flushList : function(){
		User.Team.page_list.is_loading = true;
		User.Team.page_list.list = [];
		var _data = {
			type:User.Team.page_list.type,
			search:User.Team.page_list.search,
			page:User.Team.page_list.page,
			page_size:User.Team.page_list.page_size,
		};
		$.post(User.Team.url_ajax.list,_data, function(rs){
			if(rs.status){
				User.Team.page_list.is_loading = false;
				User.Team.page_list.list = rs.data.list;
				User.Team.page_list.last_page = rs.data.page_info.last_page;
				User.Team.page_list.is_empty = rs.data.list.length == 0;
			}
		});
	}
};

User.Register = {
	countdown : 0,
	target : null,
	settime : function(){
		if(User.Register.countdown<=0){
			$(User.Register.target).val("获取验证码");
			return true;
		}else{
			$(User.Register.target).val("重新发送(" + User.Register.countdown + ")");
			User.Register.countdown --;
			window.setTimeout(User.Register.settime, 1000);
		}
	},
	url_ajax:{
		sms : '#'
	},
	sendSms : function(obj){
		User.Register.target = obj;
		if(User.Register.countdown > 0){
			return false;
		}
		var _mobile = $('#mobile').val();
		if(_mobile == ''){
			User.Register.validate.form();
			return false;
		}
		if (!(/^1[34578]\d{9}$/).test(_mobile)){
			User.Register.validate.form();
			return false;
		}
		$.ajax(User.Register.url_ajax.sms, {
			async:true, 
			cache:false, 
			type:'POST', 
			dataType:"json", 
			data:{'mobile':_mobile}, 
			success: function(rs){
				if(rs.code == 1){
					User.Register.countdown = 60;
					User.Register.settime();
					return true;
					//window.wxc.xcConfirm('验证码已发送，请在手机上查收', 'success');
				}else{
					layer.alert(rs.msg,{icon:0});
				}
			}
		});
	},
	validate : null,
	init : function(){
		User.Register.validate = $("#fogetForm").validate({
			submitHandler:function(){
				return true;
			},
			rules: {
				mobile : {
					required : true,
					mobile : true,
				},
				Verification: "required",
				password: {
					required: true,
					minlength: 5
				},
				confirm_password: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
				agree: "required",
				code:{
					required: true,
				}
			},
			messages: {
				mobile : {
					required : '请输入手机号',
					mobile : '请输入正确格式',
				},
				Verification: 
				{
					required: "必填",
				},
				agree: "请先勾选同意服务协议",
				password: {
					 required: "不能为空",
					  minlength: "密码长度不能小于 5 个字母"
				},
				confirm_password: {
					required: "不能为空",
					 minlength: "密码长度不能小于 5 个字母",
					equalTo:"两次密码输入不一致"
				},
				code:{
					required: '请输入验证码',
				}
			}
			
	   });
	}
};
