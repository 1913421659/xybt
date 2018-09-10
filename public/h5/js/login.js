// JavaScript Document
vue.append({
	login:{
		form:{
			user_name:'',
			password:'',
		},
		subimt:function(){
			var $this = vue.login;
			if($this.form.user_name==''){
				$.toast("请输入手机号","text");
			}else if(!(/^1[34578]\d{9}$/).test($this.form.user_name)){
				$.toast("请输入正确的手机号", "text");
			}else if($this.form.password==''){
				$.toast("请输入密码","text");	
			}else if(/^.{1,4}$/.test($this.form.password)){
				$.toast("长度不应少到5个字符","text");	
			}else{
				$.post('/api/login/login', $this.form,function(rs){
					if(rs.status){
						vue.login.goBack();
					}else{
						alert(rs.msg);
					}
				},'json');
			}
		},
		goBack:function(){
			var url = $.cookie('login_goto');
			if(!url){
				//url = '/h5/index/index.html';
				url = '/h5/account/index.html';
			}
			location.href=url;
		}
	},
	register : new function(){
		var $this = this;
		$this.form = {
			mobile:'',
			password:'',
			code:'',
		};
		$this.sms_wait = 0;
		$this.checkPhone = function(){
			if($this.form.mobile == ''){
				alert('请输入手机号码');
				return false;
			}else if(!(/^1[34578]\d{9}$/).test($this.form.mobile)){
				alert('请输入正确的手机号码');
				return false;
			}else{
				return true;
			}
		};
		$this.checkPassword = function(){
			if($this.form.password == ''){
				alert('请输入密码');
				return false;
			}else if(/^.{1,4}$/.test($this.form.password)){
				alert('密码长度不应少到5个字符');
				return false;
			}else{
				return true;
			}
		};
		$this.checkCode = function(){
			if($this.form.code == ''){
				alert('请输入验证码');
				return false;
			}
			return true;
		};
		$this.sendSms = function(){
			if($this.sms_wait > 0){
				return false;
			}
			if($this.checkPhone()){
				$.post('/api/login/sendRegSms', $this.form, function(rs){
					if(rs.status){
						$this.sms_wait = 60;
						window.setTimeout($this.waitTimer, 1000);
					}else{
						alert(rs.msg);
					}
				}, 'json');
			}
			return false;
		};
		$this.waitTimer = function(){
			$this.sms_wait--;
			if($this.sms_wait > 0){
				window.setTimeout($this.waitTimer, 1000);
			}
		};
		$this.subimt = function(){
			if($this.checkPhone() && $this.checkPassword() && $this.checkCode()){
				$.post('/api/login/register', $this.form, function(rs){
					if(rs.status){
						alert('注册成功！');
						vue.login.goBack();
					}else{
						alert(rs.msg);
					}
				},'json');
			}
		};
	},
	forget:new function(){
		var $this = this;
		$this.form = {
			mobile:'',
			password:'',
			code:'',
		};
		$this.checkPhone = function(){
			if($this.form.mobile == ''){
				alert('请输入手机号码');
				return false;
			}else if(!(/^1[34578]\d{9}$/).test($this.form.mobile)){
				alert('请输入正确的手机号码');
				return false;
			}else{
				return true;
			}
		};
		$this.checkPassword = function(){
			if($this.form.password == ''){
				alert('请输入密码');
				return false;
			}else if(/^.{1,4}$/.test($this.form.password)){
				alert('密码长度不应少到5个字符');
				return false;
			}else{
				return true;
			}
		};
		$this.checkCode = function(){
			if($this.form.code == ''){
				alert('请输入验证码');
				return false;
			}
			return true;
		};
		$this.sms_wait = 0;
		$this.sendSms = function(){
			if($this.sms_wait > 0){
				return false;
			}
			if($this.checkPhone()){
				$.post('/api/login/sendForgetSms', $this.form, function(rs){
					if(rs.status){
						$this.sms_wait = 60;
						window.setTimeout($this.waitTimer, 1000);
					}else{
						alert(rs.msg);
					}
				}, 'json');
			}
			return false;
		};
		$this.waitTimer = function(){
			$this.sms_wait--;
			if($this.sms_wait > 0){
				window.setTimeout($this.waitTimer, 1000);
			}
		};
		$this.subimt = function(){
			if($this.checkPhone() && $this.checkPassword() && $this.checkCode()){
				$.post('/api/login/forget', $this.form, function(rs){
					if(rs.status){
						alert('密码重置成功！');
						vue.goto('login');
					}else{
						alert(rs.msg);
					}
				},'json');
			}
		};
	},
});
vue.setDefault('login');