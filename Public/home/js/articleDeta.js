/**
 * Created by Administrator on 2016/3/17.
 */
$(document).ready(function(){
	var clientW=document.documentElement.clientWidth||document.body.clientWidth;
	var clientH=document.documentElement.clientHeight||document.body.clientHeight;
	$('.shade').css({'width':clientW+'px','height':clientH+'px'});
    $('#collect').click(function(){
        var url = $('#collect_url').val();
        $.get(url,function(data){
            if(data == 0){
                alert('您已经收藏过这篇文章！');
            }else{
                if(data['result'] > 0){
                    $('#collect').find('span').html(data['collect_num']);
                    alert('收藏成功！');
                }
            }

        });
    });

    
});