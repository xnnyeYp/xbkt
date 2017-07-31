/**
 * Created by Administrator on 2016/3/17.
 */

$(document).ready(function(){
    var error = $('#error').val();
    var point = $('#point').val();
    if(error == 1){
        var str = '您的积分不足，剩余积分' + point;
        alert(str);
    }

    $('#collect').click(function(){
        var url = $('collect_url').val();
        $.get(url,function(data){
            if(data == 1){
                alert('收藏成功！');
            }
        });
    });

});
function check_point(){
    if($('#ppoint').val() > 10){
        alert('最多打赏10积分！');
        $('#ppoint').select();
        $('#ppoint').val('');
        return false;
    }else{
        return true;
    }
}
