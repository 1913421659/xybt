// Example:

// alert( readCookie("myCookie") );
var COOKIE = new function(){
	this.set = function(name, value, hours){
		var expire = "";
		if (hours != null){
			expire = new Date((new Date()).getTime() + hours * 3600000);
			expire = "; expires=" + expire.toGMTString();
		}
		document.cookie = name + "=" + escape(value) + expire;
	};
	this.get = function(name){
		var cookieValue = "";
		var search = name + "=";
		if (document.cookie.length > 0)
		{
			offset = document.cookie.indexOf(search);
			if (offset != -1)
			{
				offset += search.length;
				end = document.cookie.indexOf(";", offset);
				if (end == -1)
					end = document.cookie.length;
				cookieValue = unescape(document.cookie.substring(offset, end))
			}
		}
		return cookieValue;
	}
}();