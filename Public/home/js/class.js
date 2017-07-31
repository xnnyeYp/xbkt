$(document).ready(function(){
	var open=0;
	$('.shade').css({'height': $('.content').css("height")});
	$('.allClassify').click(function(){
		$('.shade').toggle();
		$('.open').toggle();
		open=!open;
		if(open)
			$(this).children('a').css({'background-image':'url(/Public/home/images/class/upIcon.gif)'});
		else
			$(this).children('a').css({'background-image':'url(/Public/home/images/class/downIcon.gif)'});
		$('.shade').click(function(){
			open=!open;
			$('.open').hide();
			$(this).hide();
			$('.allClassify').children('a').css({'background-image':'url(/Public/home/images/class/downIcon.gif)'});
		});
	});
	var oldOpen=null;
	var num=0
	$('.menu').children('ul').children('li').children('a').click(function(){
		var nowOpen=$(this).next('ul');
		$(this).css({'color':'#98d261','background-image':'url(/Public/home/images/class/upIcon2.gif)'});
		$(nowOpen).show();
		if(nowOpen.is(oldOpen)){
			num++;
		}else{
			num=0;
		}
		if(num==1||num==0){
			$(oldOpen).siblings('a').css({'color':'#b2b2b2','background-image':'url(/Public/home/images/class/downIcon3.gif)'});
			$(oldOpen).hide();
			num=(num==1)?1:0;
		}
		if(num==2){
			num=0;
		}
		oldOpen=nowOpen;
	});
	
})
