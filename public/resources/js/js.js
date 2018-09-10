$(function(){
        var favour_submit = false;
        //1.楼梯什么时候显示，800px scroll--->scrollTop
        $(window).on('scroll',function(){
            var $scroll=$(this).scrollTop();
            if($scroll>=600){
                $('#loutinav').show();
            }else{
                $('#loutinav').hide();
            }
            //4.拖动滚轮，对应的楼梯样式进行匹配
            $('.louti').each(function(){
                var $loutitop=$('.louti').eq($(this).index()).offset().top+200;
//              console.log($loutitop);
//                console.log($(this).index());
                if($loutitop>$scroll){//楼层的top大于滚动条的距离
                    $('#loutinav li').removeClass('active');
                    $('#loutinav li').eq($(this).index()).addClass('active');
                    return false;//中断循环
                }
            });
        });
        //2.获取每个楼梯的offset().top,点击楼梯让对应的内容模块移动到对应的位置  offset().left

        var $loutili=$('#loutinav li').not('.last');
        $loutili.on('click',function(){
            $(this).addClass('active').siblings('li').removeClass('active');
            var $loutitop=$('.louti').eq($(this).index()).offset().top;
            //获取每个楼梯的offsetTop值
            $('html,body').animate({//$('html,body')兼容问题body属于chrome
                scrollTop:$loutitop
            })
        });
        //3.回到顶部
        $('.last').on('click',function(){
            $('html,body').animate({//$('html,body')兼容问题body属于chrome
                scrollTop:0
            })
        });
    $(".mySelect").hover(function(){  	
    	$(this).find(".mySelect_ul").slideDown(1);
    	$(".xiala_jj").removeClass("xiala_a");
    	$(".xiala_jj").addClass("xiala_aa");
	},function(){
		$(this).find(".mySelect_ul").slideUp(1);
        $(".xiala_jj").removeClass("xiala_aa");
    	$(".xiala_jj").addClass("xiala_a");
	});
	$(".mySelect_ul li a").click(function(){
	    var value=$(this).html();
	    $(".mySelect span").text(value);
	});
     $(".praise_1").click(function(){
        if(favour_submit){
            return;
        }
        favour_submit = true;
        var is_favour=0;
		var praise_img = $(this).find(".praise-img");
		var text_box =$(this).siblings(".add-num");
		var praise_txt = $(this).siblings(".praise-txt");
		var num=parseInt(praise_txt.text());
		if(praise_img.is(".zan_lable2")){
			$(this).css("color","#333");
			$(this).find("label").removeClass("zan_lable2");
			$(this).find("label").addClass("zan_lable1");
			praise_txt.removeClass("hover");
			text_box.show().html("<em class='add-animation'>-1</em>");
			$(".add-animation").removeClass("hover");
			num -=1;
			praise_txt.text(num)
            is_favour = 2;
		}else{
			$(this).css("color","#FF5C00");
			$(this).find("label").removeClass("zan_lable1");
			$(this).find("label").addClass("zan_lable2");
			praise_txt.addClass("hover");
			text_box.show().html("<em class='add-animation'>+1</em>");
			$(".add-animation").addClass("hover");
			num +=1;
			praise_txt.text(num)
            is_favour = 1;
		}

        if(is_favour>0){
            var data = {
                'shareId':$(this).attr('data'),
                'isFavour':is_favour
            };
            $.ajax({
                type:'post',
                url:'index.php?s=/index/index/sharefavour',
                data:data,
                async:true,
                success:function(response){
                    if(response.code==1){

                    }else if(response.code==0){
                        layer.msg(response.msg);
                        window.location.reload();
                    }else if(response.code==-1){
                        layer.msg(response.msg);
                        window.location.href="index.php?s=/index/login/index/from/index-sharethehall.html";
                    }
                    favour_submit = false;
                },
                error:function(msg){
                    console.log(msg);
                }
            });
        }
	});
//	营销管理菜单栏
	$(".left_nav_sub a").click(function(){
		$(".left_nav_sub a").removeClass("action");
		$(this).addClass("action");
	});
//  $(".menu_y").click(function(){ 
//  	$(this).sibling(".per_hover").toggleClass();	
//  });
//	分类分类
    $(".mark_suan li").click(function(){
		$(".mark_suan li").find("span img").attr("src","images/down.png");	
		$(this).find("span img").attr("src","images/up.png");
    });
    $(".pinglun").click(function(){	
		$(this).parents().parents().siblings(".ping_box ").slideToggle(500);		
   });
   
    $("#search").keyup(function(){
    	if($("#search").val()==""){
    		$(this).siblings("#guanbi_1").css({
			"display":"none"
		    });	
    		
    	}else{
    	   $(this).siblings("#guanbi_1").css({
			   "display":"block"
		    });		
    	}	    	
    });
    $("#guanbi_1").click(function(){
    	
    	 $("#search").val("");
    	 $("#guanbi_1").css("display","none");
    });
//  $(".menu_y").click(function(){ 
//  	$(this).sibling(".per_hover").toggleClass();
//  	
//  });
// 聊天页面
   $(".chat_b_4 .chat_u").click(function(){				
    $(this).toggleClass('on_click').siblings().removeClass('on_click');
    if($(this).find(".chat_y").is(":hidden")){
        $(this).find(".chat_y").slideDown().parent().siblings().find(".chat_y").slideUp();
    }else{
        $(this).find(".chat_y").slideUp();
    }
   });
   
     // 聊天切换
    $("#chat_ul_li li").click(function(){
    	$("#chat_ul_li li").removeClass("active_u");
    	$(this).addClass("active_u");
		var ss=$(this).index();
		$(".fenzhu").hide();
		$(".fenzhu").eq(ss).show();
   });
   

 //图片上传预览    IE是用了滤镜。
    function previewImage(file)
    {
      var MAXWIDTH  = 260; 
      var MAXHEIGHT = 180;
      var div = document.getElementById('preview');
      if (file.files && file.files[0])
      {
          div.innerHTML ='<img id=imghead>';
          var img = document.getElementById('imghead');
          img.onload = function(){
            var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
            img.width  =  rect.width;
            img.height =  rect.height;
//                 img.style.marginLeft = rect.left+'px';
            img.style.marginTop = rect.top+'px';
          }
          var reader = new FileReader();
          reader.onload = function(evt){img.src = evt.target.result;}
          reader.readAsDataURL(file.files[0]);
      }
      else //兼容IE
      {
        var sFilter='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src="';
        file.select();
        var src = document.selection.createRange().text;
        div.innerHTML = '<img id=imghead>';
        var img = document.getElementById('imghead');
        img.filters.item('DXImageTransform.Microsoft.AlphaImageLoader').src = src;
        var rect = clacImgZoomParam(MAXWIDTH, MAXHEIGHT, img.offsetWidth, img.offsetHeight);
        status =('rect:'+rect.top+','+rect.left+','+rect.width+','+rect.height);
        div.innerHTML = "<div id=divhead style='width:"+rect.width+"px;height:"+rect.height+"px"+sFilter+src+"\"'></div>";
      }
    }
    function clacImgZoomParam( maxWidth, maxHeight, width, height ){
        var param = {top:0, left:0, width:width, height:height};
        if( width>maxWidth || height>maxHeight )
        {
            rateWidth = width / maxWidth;
            rateHeight = height / maxHeight;
             
            if( rateWidth > rateHeight )
            {
                param.width =  maxWidth;
                param.height = Math.round(height / rateWidth);
            }else
            {
                param.width = Math.round(width / rateHeight);
                param.height = maxHeight;
            }
        }
        param.left = Math.round((maxWidth - param.width) / 2);
        param.top = Math.round((maxHeight - param.height) / 2);
        return param;
    }
    $(window).scroll(function () {
	    if ($(this).scrollTop() > 500) {
	        $('.scrollup').fadeIn('slow');
	    } else {
	        $('.scrollup').fadeOut('slow');
	    }
	});
	$('.scrollup').click(function () {
	    $("html, body").animate({scrollTop: 0}, 2000);
	    return false;
	});
	$(".footer_li").hover(function(){
		$(this).find(".footer_hover").toggle();
	});
	
})
 
