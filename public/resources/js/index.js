$(function(){
	
    $('.menu_1').mouseenter(function () {
        $(this).find('.menu_4').stop(false, true).slideDown(0);
    }).mouseleave(function () {
        $(this).find('.menu_4').stop(false, true).slideUp(0);
    });
    
//   $(".menu_r_1 li a").click(function(){
//  	$(".menu_r_1 li a").removeClass("menu_home");
//  	$(this).addClass("menu_home");
//  });
    $(".massage_2").hover(function(){
    	$(".dss").show();
		$(this).find(".mas_hover").stop(false, true).slideDown();
		$(".massages_3").css("background-color","#FFFFFF");
		$(".massages_3").css("border","1px solid #d5d5d5");
		
		 $("#images2").css("transform","rotate(180deg)")
	},function(){
		$(this).find(".mas_hover").stop(false, true).slideUp(1);
		$(".massages_3").css("background-color","#efefef");
		$(".massages_3").css("border","1px solid #efefef");
		$(".dss").hide();
		 $("#images2").css("transform","rotate(0deg)")
	});
  

    $(".ti_r_e li").hover(function(){    	
		$(".ti_hover").hide();
    	$(this).find(".ti_hover").fadeIn(800);
    	
	},function(){
		$(this).find(".ti_hover").fadeOut(1);
	});
	
	$(".menu_top").hover(function(){   	
		$(this).find(".menu_bottom").show();
	},function(){
		$(this).find(".menu_bottom").hide();		
	});
	
    $(".div_9 .que_l").click(function(){
    	$(".gg").css("display","block");
    	$(this).find(".per_hover").slideToggle(500);
   	
    });

    
    $(".per_hover>.que_l").click(function(){
    	$(".per_hover").css("display","block");
    })
    $('#makemenu1 li a').click(function(){
    	$('#mark_span1').css("background-color",'white');
    	$('#mark_span1').css("color",'#5b5b5b');
    	  $('#makemenu1 li a').removeClass("mark_color");
		  $(this).addClass("mark_color");
		  
		});

     $('#makemenu2 li a').click(function(){
    	  $('#mark_span2').css("background-color",'white');
    	  $('#mark_span2').css("color",'#5b5b5b');
    	  $('#makemenu2 li a').removeClass("mark_color");
		  $(this).addClass("mark_color");
		  
		});
		  $('#makemenu3 li a').click(function(){
    	  $('#mark_span3').css("background-color",'white');
    	  $('#mark_span3').css("color",'#5b5b5b');
    	  $('#makemenu3 li a').removeClass("mark_color");
		  $(this).addClass("mark_color");
		  
		});
		
		 $('.mark_span').click(function(){
		 	$(this).css("background-color",'#FF5C00');
		 	$(this).css("color",'#FFFFFF');
		 	$('.mark_menu li a').removeClass("mark_color");
		});
		 $('.tou_r_a li').click(function(){
		 	var ss=$(this).index();
		 	 $('.tou_r_a li').removeClass("active_nav");
		 	$(this).addClass("active_nav");
		 	$(".tou_r_c .tou_r_a_box").hide();
		 	$(".tou_r_c .tou_r_a_box").eq(ss).show();
		 
		 	$('.mark_menu li a').removeClass("mark_color");
		});


    $(".dianhua5").hover(function(){
    	$(".top_1").css("background-color","#FF5C00");
		$(this).find(".dianhua6").stop(false, true).animate({marginRight:'0px'},'1000');
	},function(){
      $(this).find(".dianhua6").stop(false, true).animate({marginRight:'-154px'},'1000');
		$(".top_1").css("background-color","#1e2025");
	});

    $(".er_1").hover(function(){
    	
		$(this).find(".erweima").stop(false, true).fadeIn(1000);
	},function(){
        $(this).find(".erweima").stop(false, true).fadeOut(1);		
	});  	
	
    $("#leftsead li:eq(3)").hover(function(){
      $("#leftsead li:eq(3) img").attr("src",$("#leftsead li:eq(3) img").attr("src").replace("zixun.png","zixun1.png"));		
	},function(){
      $("#leftsead li:eq(3) img").attr("src",$("#leftsead li:eq(3) img").attr("src").replace("zixun1.png","zixun.png"));	
	});  	
	$("#top_btn").click(function(){if(scroll=="off") return;$("html,body").animate({scrollTop: 0}, 600);});

	$('#withdraw').click(function(){
		  if($("#jine").val()==""){
		  	var txt=  "金额不能为空！";
			    window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.error);
		  	return false;
		  }
		  if(!$("#jine").val()==""){
		  	var txt=  "等待审核！";
			window.wxc.xcConfirm(txt, window.wxc.xcConfirm.typeEnum.success);
		  	return false;
		  }		   
	});
	 $(".padding_b .aann").hover(function(){
			
	     $(this).find(".hover_selected").stop(false, true).slideDown(1);
		 $(this).find(".hover_aaa").addClass("hover_a");
		  $(this).find(".kkk").addClass("hover_span").show(1);
	},function(){
         $(this).find(".hover_selected").stop(false, true).slideUp(1);
		 $(this).find(".hover_aaa").removeClass("hover_a");
	     $(this).find(".kkk").removeClass("hover_span");	
	});
})
