// JavaScript Document
var vue = {
	el:"#vue_page",
	data:{
	},
	methods:{
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
	}
};
window.addEventListener('load', function(){
	vue = new Vue(vue);
});