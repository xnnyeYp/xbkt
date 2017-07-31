/**
 * Created by Administrator on 2016/5/19.
 */
$(document).ready(function(){
    $('[name=w]').val(localImage.Width);
    $('.pic').ondragstart=function(){
        return false;
    }//阻止图片被拖动
   
    var img_src = '';
    var fromTop=0;
   $('#File').change(function(){
       $('#image').ajaxSubmit({
           type:'post',
           url:'/me/upload_image',
           beforeSend: function () {
             $('#loading').show();
           },
           success:function(data){
                $('[name=img_src]').val(data);
                $('#localImage').show();
                fromTop=$("#localImage").offset().top;
                $('.shadeEdit').css('top',fromTop);
                $('.shadeE').show();
                if(document.getElementById('preview').complete){
                    var newImg=getSize('#preview');
                    a(newImg);
                };
               $('#loading').hide();
           }
       });
   })
    function a(newImg){
        if(newImg.Height<getSize('.shadeEdit').Height){
            $('.shadeEdit').css({'height':newImg.Height,'width':newImg.Height});
        }
    }
    var clientW=document.documentElement.clientWidth||document.body.clientWidth;
    var clientH=document.documentElement.clientHeight||document.body.clientHeight;
    $('.shadeE').css({'width':clientW,'height':clientH});
    $('.shadeEdit').css('height',clientW*0.6);
    var divWidth=clientW*0.6;
    var divHeight=getSize('.shadeEdit').Height;
    var oImg=$('#preview');
    $('.shadeEdit').bind('touchstart',function(){
        var downX=event.targetTouches[0].clientX;
        var downY=event.targetTouches[0].clientY;
        var divPosX=getImgPos('.shadeEdit').Left;
        var divPosY=getImgPos('.shadeEdit').Top;
        
        $(this).bind('touchmove',function(){/*$(this).css({'margin-left':0,'left':0});*/
            var moveX=event.targetTouches[0].clientX-downX;
            var moveY=event.targetTouches[0].clientY-downY;
            var x=divPosX+moveX;
            var y=divPosY+moveY;
            //document.title=fromTop+','+$(".shadeEdit").offset().top;

            $(this).css({'top':judgePos(x,y).Y+'px','left':judgePos(x,y).X+'px'});
        })
        $(this).bind('touchend',function(){
            $(this).unbind('touchmove');
            $(this).unbind('touchend');
            var Top=getImgPos('.shadeEdit').Top-fromTop;
            var Left=getImgPos('.shadeEdit').Left; 
            Top=b(Top,getSize('#localImage').Height);
            Left=b(Left,getSize('#localImage').Width);//截下来的部分相对于原图片的坐标
            $('[name=x]').val(Left);
            $('[name=y]').val(Top);
            $('[name=w]').val($('.shadeEdit').height());
            $('[name=img_w]').val($('#preview').width());

        })
    });
        
    function b(value,border){
        if(value<0){
            return 0;
        }else if(value>border){
            return border;
        }
        return value;
    };

    /**
     * 取消上传
     */
    $('.noIcon').click(function () {
        $('.shadeE').hide();
        $('#localImage').hide();
        $('#preview').attr('src','');
        $('#preview').removeAttr('style');
    });

    $('.yesIcon').click(function(){
        $.ajax({
            url:'/me/crop_img',
            data:$('#image').serialize(),
            type:'get',
            beforeSend: function () {
                $('#loading').show();
            },
            success:function(data){
                console.log(data);
                $('#headProtrait').attr('src',data);
                $('.shadeE').hide();
                $('#localImage').hide();
                $('#preview').attr('src','');
                $('#loading').hide();
            }
        });
        $('.shadeE').hide();
        $('#localImage').hide();
        $('#preview').attr('src','');
        $('#preview').removeAttr('style');
    });

    function judgePos(x,y){
        divHeight=getSize('.shadeEdit').Height;
        if(fromTop){
            if(x<0){
                x=0;
            }else if(x+divWidth>clientW){
                x=clientW-divWidth;
            }
            if(y<fromTop){
                y=fromTop;
            }else if(y+divHeight>getSize('#localImage').Height+fromTop){
                y=getSize('#localImage').Height+fromTop-divHeight;
            }
            return {
                X:x,
                Y:y
            };
        }
            
    };
    function getSize(id){
        return{
            Width:parseFloat($(id).css('width')),
            Height:parseFloat($(id).css('height'))
        };
    };
    function getImgPos(id){
        return {
            Top:parseFloat($(id).css('top')),
            Left:parseFloat($(id).css('left'))
        };
    };
});