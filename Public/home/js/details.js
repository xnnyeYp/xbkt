$(document).ready(function(){
	$('.left').find('font:first').css({'background':'#ffab40'});
	$('.left').find('font:first').css({'margin-right':'0.15rem'});
	$('.left').find('font:last').css({'background':'#5bceb7'});
	$('.chose').find('span:first').click(function(){
		$('.chose').find('img').css({'left':'0','width':$(this).css('width')});
		$('.talk').hide();
		$('.detail').show();
	});
	$('.chose').find('span:last').click(function(){
		$('.chose').find('img').css({'left':$('.chose').find('span:first').css('width'),
			'width':$(this).css('width')});
		$('.detail').hide();
		$('.talk').show();
	});
	$('.money').html($('.price').children('span').html());
});