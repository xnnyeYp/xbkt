$(document).ready(function(){
	/*var clientW=document.documentElement.clientWidth||document.body.clientWidth;
	var clientH=document.documentElement.clientHeight||document.body.clientHeight;
	$('.shadeEdit').css({'width':clientW,'height':clientH });*/
	$('.mame').text($('.petName').find('span').text());
	$('.shade').css({'height':$('body').css('height')});
	 function getSize(id){
        return{
            Width:parseFloat($(id).css('width')),
            Height:parseFloat($(id).css('height'))
        };
    };
	/*$('.photo').click(function(){
		
		$('.shade').show();
		$('.shade').animate({top:'0'});
		$('.setPhoto').show();
		$('.setPhoto').animate({bottom:'0'});
		$('.cancel').click(function(){
			$('.setPhoto').animate({bottom:'-2.4rem'});
			$('.setPhoto').hide(1000);
			$('.shade').animate({top:'100%'});
			$('.shade').hide(1000);
		//});
	});*/
	$('.sex').click(function(){
		$('.pageTwo').show();
		$('.pageTwo').animate({left:'0'});
		$('.pageTwo').find('li').click(function(){
			$('.pageTwo').find('li').find('img').hide();
			$(this).find('img').show();
			$('.sex').find('span').html($(this).text());
			var value = $(this).attr('value');
			var url = $('#update_url').val();
			$.get(url,{sex:value},function(data){

			});
		});
		
		$('.pageTwo').find('input[type=button]').click(function () {
			$('.pageTwo').animate({left:'100%'});
			$('.pageTwo').hide(1000);
		});
		
		$('.pageTwo').find('a').click(function(){
			$('.pageTwo').animate({left:'100%'});
			$('.pageTwo').hide(1000);
		});
	});

	//编辑昵称
	$('.petName').click(function(){
		$('.pageThree').show();
		$('.pageThree').animate({left:'0'});
		$('.pageThree').find('a').click(function(){
			$('.pageThree').animate({left:'100%'});
			$('.pageThree').hide(1000);
		});
		
		$('.pageThree').find('input[type=button]').click(function () {
			var username = $('.pageThree').find('input').val();
			if(username == ''){
				alert('请输入昵称！');
			}else{
				var url = $('#update_url').val();
				$.get(url,{username:username},function(data){
					$('.name').text(username);
					$('.petName').find('span').text(username);
					$('.pageThree').animate({left:'100%'});
					$('.pageThree').hide(1000);
				});
			}
		});
	});

	//编辑职业
	$('.job').click(function(){
		$('.pageFour').show();
		$('.pageFour').animate({left:'0'});
		$('.pageFour').find('li').click(function(){
			$('.pageFour').find('li').find('img').hide();
			$(this).find('img').show();
			$('.job').find('span').html($(this).text());
			var value = $(this).attr('value');
			var url = $('#update_url').val();
			$.get(url,{type:value},function(data){

			});
		});
		
		$('.pageFour').find('input[type=button]').click(function () {
			$('.pageFour').animate({left:'100%'});
			$('.pageFour').hide(1000);
		});
		
		$('.pageFour').find('a').click(function(){
			$('.pageFour').animate({left:'100%'});
			$('.pageFour').hide(1000);
		});
	});

	//编辑学校
	$('.school').click(function(){
		$('.pageFive').show();
		$('.pageFive').animate({left:'0'});
		$('.pageFive').find('a').click(function(){
			$('.pageFive').animate({left:'100%'});
			$('.pageFive').hide(1000);
		});
		$('.pageFive').find('input[type=button]').click(function () {
			var school = $('.pageFive').find('input').val();
			if(school == ''){
				alert('请输入学校！');
			}else{
				var url = $('#update_url').val();
				$.get(url,{school:school},function(data){

					$('.school').find('span').text(school);
					$('.pageFive').animate({left:'100%'});
					$('.pageFive').hide(1000);
				});
			}
		});
	});
	/**
	 * 点击调用上传文件
	 */
	$('#click_file').click(function(){
		return $('#File').click();
	});

});