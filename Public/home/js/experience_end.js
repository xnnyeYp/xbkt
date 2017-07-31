/**
 * Created by Administrator on 2016/5/13.
 */
$(function(){
    var click_message = 0;
    $('#comment').click(function(){
        click_message = 1;
    });
    $('.submit').click(function () {
        var max_point = $('[name=point_max]').val();
        max_point = parseInt(max_point);
        var point = $('[name="point"]').val();
        point = parseInt(point);

        if(point > max_point){
            alert('最多可以打赏'+ max_point + '积分！');
            return false;
        }

        var comment = $('#comment').val();
        var message_length = comment.length;
        var  user_point =0;
        $.ajax({
            url:'/order/get_point',
            type:'post',
            async:false,
            success:function(data){
               user_point = data;
            }
        });

       if(point > user_point){
           var worning_msg = "您的积分不足，当前积分剩余" + user_point;
           alert(worning_msg);
           return false;
       }

        //if(comment == '' || click_message == 0 || message_length < 15){
        //    alert('要输入15个字才是好同志!');
        //    return false;
        //}

    });
});