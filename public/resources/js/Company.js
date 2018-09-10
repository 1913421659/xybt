// JavaScript Document
var isSubmit = false;
Company = new function(){
	var $this = this;
	$this.url_ajax = {
		search : null,
		apply : null,
		user_list : null,
		user_remove : null,
		remarks : null,
		upLogo : null,
	};
	$this.url_index = null;	
	$this.view_join = {
		is_default : true,
		is_loading : 0,
		is_empty : 0,
		list:[],
		key:''
	};
	$this.view_join_form = {
		id:0,
		company_name:'',
		apply_txt:'',
		show_tips :false,
		is_loading : 0,
	}
	$this.initJoin = function(){
		$this.view_join = new Vue({
			el:'#page_company_list',
			data : $this.view_join,
			methods : {
				flushList : function(){
					this.key = $('#key').val();
					this.is_default = this.is_empty = 0;
					this.is_loading = 1;
					this.list = [];
					$.post($this.url_ajax.search,{key:this.key},function(rs){
						$this.view_join.is_loading = 0;
						if(rs.status == 1){
							$this.view_join.is_empty = rs.data.length == 0;
							$this.view_join.list = rs.data;
						}else{
							$this.view_join.is_empty = 1;
						}
					});
				},
				showApplyForm : function(item){
					$this.view_join_form.id = item.id;
					$this.view_join_form.company_name = item.company_name;
					$this.view_join_form.apply_txt = '';
					$this.view_join_form.show_tips = 0;
					$(".now_tan").fadeIn(300);
				}
			}
		});
		$this.view_join_form = new Vue({
			el:'.now_tan',
			data : $this.view_join_form,
			methods :{
				hideForm : function(){
					$(".now_tan").fadeOut(100);
				},
				postForm : function(){
					if(this.apply_txt == ''){
						return false;
					}
					this.is_loading =  layer.load(1, {
						shade: [0.1,'#fff'] //0.1透明度的白色背景
					});
                    var self = this;
					$.post($this.url_ajax.apply,{company_id:this.id,content:this.apply_txt},function(rs){
                        layer.msg(rs.msg);
                        layer.close(self.is_loading);
                        window.setTimeout(function(){
                            window.location.href = $this.url_index;
                        },1500);
					});
				},
				changeTxt:function(){
					this.apply_txt = $('#apply_txt').val();
				}
			}
		});
	};
	
/******* 企业首页开始 ********/
	$this.view_home = {
		company_id : 0,//企业ID
		company_name:'',//企业名
		company_head_portrait:'',//头像
		remarks:'',//简介
		user_number:0,//用户量
		apply_number:0,//申请数量
		qrcode_path:'',//二维码
		is_manage : 0,
		open_edit : 0
	};
	$this.initHome = function(){
		$this.view_home = new Vue({
			el:"#page_company_home",
			data:$this.view_home,
			methods : {
				editRemarks:function(){
					this.open_edit = 1;
				},
				cancelEditRemarks:function(){
					this.open_edit = 0;
				},
				saveRemarks : function(){
					var remarks = $('#company_remarks').val();
					$.post($this.url_ajax.remarks, {remarks:remarks}, function(rs){
						if(rs.status){
							$this.view_home.remarks = rs.data;
							$this.view_home.open_edit = 0;
						}else{
							layer.msg(rs.msg);
						}
					});
				},
				saveLogo : function(){
					var file = $('#company_logo')[0];
					var formData = new FormData();
					formData.append("file",file.files[0]);
					$.ajax({ 
						url : $this.url_ajax.upLogo, 
						type : 'POST', 
						data : formData, 
						processData : false, // 告诉jQuery不要去处理发送的数据
						contentType : false,// 告诉jQuery不要去设置Content-Type请求头
						beforeSend:function(){
							console.log("正在进行，请稍候");
						},
						success : function(rs) {
							if(rs.status===1){
								layer.alert('上传成功',function(){location.href=location.href});
							}else{
								layer.msg("上传失败");
							}
						}, 
						error : function(responseStr) { 
							layer.msg("上传失败");
						} 
					});
				},
			}
		});
	};
	/* 企业首页结束 */
	
/************** 用户列表页 *******************/
	$this.view_users = new function(){
		var $this = this;
		$this.user_list = [];
		$this.page_size = 5;
		$this.page = 1;
		$this.is_end = false;
		$this.is_loading = true;
		$this.getUserList = function(){
			$this.is_loading = true;
			$.post(Company.url_ajax.user_list, {
				page:$this.page, 
				page_size:$this.page_size
			}, function(rs){
				if(rs.status){
					$this.user_list=$this.user_list.concat(rs.data);
					if(rs.data.length < $this.page_size){
						$this.is_end = true;
					}else{
						$this.page +=1;
					}
				}
				$this.is_loading = false;
			});
		};
		$this.remove_loading = null;
		$this.removeUser = function(user_id){
			layer.confirm('是否要移除些用户?', function(index){
				$this.remove_loading = layer.load(2);
				console.log(user_id);
				$.post(Company.url_ajax.user_remove, {user_id:user_id}, function(rs){
					layer.close($this.remove_loading);
					if(rs.status == 1){
						$this.user_list = [];
						$this.is_loading = true;
						$this.page = 1;
						$this.getUserList();
					}else{
						layer.alert(rs.msg);
					}
				});
			  layer.close(index);
			});
		}
	};
	$this.initUsers = function(){
		$this.view_users = new Vue({
			el:"#page_company_users",
			data:$this.view_users
		});
		$this.view_users.getUserList();
	}
/*############### 用户列表页 #####################*/

/************ 创建企业 *******************/
	$this.view_create = new function(){
		var $this = this;
		$this.step = 1;
		$this.step1_validate = null;
		$this.step2_validate = null;
		$this.step3_validate = null;

		$this.nextStep = function(){
			switch($this.step){
				case 1:
					if(!$this.step1_validate.form()) return;
				break;
				case 2:
					if(!$this.step2_validate.form()) return;
				break;
			}
			$this.step += 1;
		}
		$this.prevStep = function(){
			$this.step -= 1;
		}
		$this.err_bank = 0;
		$this.checkAliName = function(){
			var _v = $('#personal_alipay_name').val() == '' ? 0 : 1;
			$('#personal_alipay_bind').val(_v);
			return _v;
		}
		$this.checkWx = function(){
			var _v = $('#personal_wechat_name').val() == '' ? 0 : 1;
			$('#personal_wechat_bind').val(_v);
			return _v;
		}
		$this.checkBank = function(){
			var _v = 0;
			_v += $('#personal_bank_user').val() == '' ? 1 : 0;
			_v += $('#personal_bank_name').val() == '' ? 2 : 0;
			_v += $('#personal_bank_number').val() == '' ? 4 : 0;
			
			if(_v == 7){//全空
				$('#personal_bank_bind').val(0);
				$this.err_bank = 0;
			}else{
				$this.err_bank = _v;
				$('#personal_bank_bind').val(1);
			}
			
		};
		$this.submit = function(){
            if(isSubmit){
                return;
            }
			var _data = {
				personal_alipay_bind : $('#personal_alipay_bind').val(),
				personal_bank_bind : $('#personal_bank_bind').val(),
				personal_wechat_bind : $('#personal_wechat_bind').val(),
				company_name : $('#company_name').val(),
				credit_code : $('#credit_code').val(),
				business_address : $('#business_address').val(),
				legal_person : $('#legal_person').val(),
				contact_name : $('#contact_name').val(),
				contact_phone : $('#contact_phone').val(),
				contact_email : $('#contact_email').val(),
				company_type : $('#company_type').val(),
				company_account : $('#company_account').val(),
				company_bank_name : $('#company_bank_name').val(),
				company_address : $('#company_address').val(),
				tax_number : $('#tax_number').val(),
				ticket_phone : $('#ticket_phone').val(),
				personal_alipay_name : $('#personal_alipay_name').val(),
				personal_wechat_name : $('#personal_wechat_name').val(),
				personal_bank_user : $('#personal_bank_user').val(),
				personal_bank_name : $('#personal_bank_name').val(),
				personal_bank_number : $('#personal_bank_number').val(),
			};
			if(_data.personal_alipay_bind == 0 && _data.personal_bank_bind == 0 && _data.personal_wechat_bind == 0){
				layer.alert('至少绑定一种结算方式');
				return false;
			}
            isSubmit = true;
			$.post('',_data,function(rs){
				if(rs.status){
					window.location.href=Company.url_index;
				}else{
                    isSubmit = false;
					layer.msg(rs.msg);
				}
			});
		}
	};
	$this.initCreate = function(){
		$this.view_create = new Vue({
			el : "#page_create",
			data : $this.view_create
		});
		$this.view_create.step1_validate = $('#create_form').validate({
			rules: {
				company_name 		: "required",
				//license_path 		: "required",
				credit_code 		: "required",
				business_address 	: "required",
				legal_person 		: "required",
				contact_name 		: "required",
				contact_phone 		: "required",
				contact_email 		: "required",
				company_type 		: "required"
				
			},
			messages: {
				company_name 		: "企业名称必填",	
				license_path 		: "营业执照必填",
				credit_code 		: "信用代码必填",
				business_address 	: "办公地址必填",
				legal_person 		: "企业法人必填",
				contact_name 		: "创建联系人必填",
				contact_phone 		: "联系电话必填",
				contact_email 		: {
					required 	: "邮箱必填",
                    email 		: "E-Mail格式不正确"
				},				
				company_type 		: "请选择企业角色"
			
			}
		});
		$this.view_create.step2_validate = $('#form_step_2').validate({
			rules: {
				company_name2: "required",
				company_account:"required",
				company_bank_name: "required",
				company_address:"required",
				tax_number:"required",
				ticket_phone: "required",
				
			},
			messages: {
				company_name2: "企业名称必填",	
				
				company_account:"银行账号必填",
				company_bank_name: "开户行必填",
				company_address:"单位地址必填",
				tax_number:"税务登记号必填",
				ticket_phone: "联系电话必填",
			
			}
		});
	}
/*########### End 创建企业#################*/
};
