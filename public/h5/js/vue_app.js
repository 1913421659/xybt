// JavaScript Document
var vue = {
	el:"#vue_page",
	data:{
		page:'loading',
		default_page:'loading',
		init_fun_list :[],
	},
	methods:{
		goto:function(page_name){
			location.href='#'+page_name;
			this.page = 'page_'+page_name;
		},
		init:function(){
			for(i in this.init_fun_list){
				//console.log(this.init_fun_list[i]);
				this[this.init_fun_list[i]]();
			}
			var re = /#!?(.*)$/g
			var p = location.href.match(re);
			if(p){
				p=p[0].replace(/^#/,'');
				if(p != ''){
					this.page = 'page_'+p;
				}else{
					this.page = 'page_'+ this.default_page;
				}
			}else{
				this.page = 'page_'+ this.default_page;
			}
		}
	},
	appendData : function(obj){
		for(i in obj){
			this.data[i] = obj[i];
		}
	},
	appendMethods : function(obj){
		for(i in obj){
			this.methods[i] = obj[i];
		}
	},
	append:function(obj){
		for(i in obj){
			if(typeof(obj[i])=='function'){
				this.methods[i] = obj[i];
			}else{
				this.data[i] = obj[i];
			}
		}
	},
	addInit: function (name){
		this.data.init_fun_list.push(name);
	},
	setDefault:function(page){
		this.data.default_page = page;
	},
};
window.addEventListener('load', function(){
	vue = new Vue(vue);
	vue.init();
});