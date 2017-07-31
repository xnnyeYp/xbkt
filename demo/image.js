/**
 * Created by Administrator on 2016/5/19.
 */
$(document).ready(function(){
    $('.pic').ondragstart=function(){
        return false;
    }//阻止图片被拖动
    var div=getSize('.div');
    var scale=1;
    $('.div').css('height',div.Width+'px');
    div=getSize('.div');
    $('.pic').bind('touchstart',function(){
        var img=getSize('.pic');
        var imgPos=getImgPos('.pic');
        if(event.targetTouches.length<2){
            var downX=event.targetTouches[0].clientX;
            var downY=event.targetTouches[0].clientY;

            $(this).bind('touchmove',function(){
                var moveX=event.targetTouches[0].clientX-downX;
                var moveY=event.targetTouches[0].clientY-downY;
                var y=imgPos.Top+moveY;
                var x=imgPos.Left+moveX;
                img=getSize('.pic');
                x=judgePos(x,y,img).X;
                y=judgePos(x,y,img).Y;
                $(this).css({'top':y+'px','left':x+'px'});
            });
        }else if(event.targetTouches.length==2){
            var targetOne=event.targetTouches[0];
            var targetTwo=event.targetTouches[1];
            var oneDown={
                oneX:targetOne.clientX,
                oneY:targetOne.clientY
            };
            var twoDown={
                twoX:targetTwo.clientX,
                twoY:targetTwo.clientY
            };
            var downLength=calculateLength(oneDown,twoDown);
            $(this).bind('touchmove',function(){
                //document.title='width:'+img.Width+',height:'+img.Height;
                var targetOne=event.targetTouches[0];
                var targetTwo=event.targetTouches[1];
                imgPos=getImgPos('.pic');
                var oneMove={
                    oneX:targetOne.clientX,
                    oneY:targetOne.clientY
                };
                var twoMove={
                    twoX:targetTwo.clientX,
                    twoY:targetTwo.clientY
                };
                var moveLength=calculateLength(oneMove,twoMove);
                scale=moveLength/downLength;
                var centerPos=getCenterPos(oneDown,twoDown);
                var endImgX=calculatePos(imgPos,centerPos,scale).endImgX;
                var endImgY=calculatePos(imgPos,centerPos,scale).endImgY;
                var changeImg=judgeArea(scale,img);
                endImgX=judgePos(endImgX,endImgY,img).X;
                endImgY=judgePos(endImgX,endImgY,img).Y;
                var endImgWidth=changeImg.endImgWidth;
                var endImgHeight=changeImg.endImgHeight;
                $('.pic').css({"width":endImgWidth+'px',"height":endImgHeight+'px'});
                $('.pic').css({"top":endImgY+'px',"left":endImgX+'px'});
                if(!changeImg.on){
                    $(this).unbind('touchmove');
                }
            });
            $(this).bind('touchend',function(){
                $(this).unbind('touchmove');
                $(this).unbind('touchend');
            });
        }
    });
    function calculateLength(json1,json2){
        return Math.sqrt(Math.pow(json1.oneX-json2.twoX,2)+Math.pow(json1.oneY-json2.twoY,2));
    };
    function judgePos(x,y,img){
        if(x>=0){
            x=0;
        }
        if(y>=0){
            y=0;
        }
        if(x<=div.Width-img.Width){
            x=div.Width-img.Width;
        }
        if(y<=div.Height-img.Height){
            y=div.Height-img.Height;
        }
        return {
            X:x,
            Y:y
        };
    }
    function judgeArea(scale,img){
        var w=scale*img.Width;
        var h=scale*img.Height;
        var on=1;
        h>w?(function(){
            if(w<div.Width){
                w=div.Width;
                h=getSize('.pic').Height;
                on=0;
            }
        })():(function(){
            if(h<div.Height){
                h=div.Height;
                w=getSize('.pic').Width;
                on=0;
            }
        })();
        return {
            endImgWidth:w,
            endImgHeight:h,
            on:on
        };
    }
    function getSize(id){
        return{
            Width:parseFloat($(id).css('width')),
            Height:parseFloat($(id).css('height'))
        };
    };
    function getCenterPos(json1,json2){
        return{
            centerX:(json1.oneX+json2.twoX)/2,
            centerY:(json1.oneY+json2.twoY)/2
        };
    };
    function getImgPos(id){
        return {
            Top:parseFloat($(id).css('top')),
            Left:parseFloat($(id).css('left'))
        };
    };
    function calculatePos(json1,json2,scale){
        return {
            endImgX:scale*json1.Left+(1-scale)*json2.centerX,
            endImgY:scale*json1.Top+(1-scale)*json2.centerY
        };
    }
});