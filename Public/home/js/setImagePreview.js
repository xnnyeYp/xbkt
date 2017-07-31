function setImagePreview()
{
    var docObj=document.getElementById("File");
    var imgObjPreview=document.getElementById("preview");
    var div=getSize('#localImage');

    function getSize(id){
        return{
            Width:parseFloat($(id).css('width')),
            Height:parseFloat($(id).css('height'))
        };
    };

    if(docObj.files &&    docObj.files[0])
    {
        imgObjPreview.style.display = 'block';

        

        //imgObjPreview.src = docObj.files[0].getAsDataURL();
       
        imgObjPreview.src = window.URL.createObjectURL(docObj.files[0]); 
        /*var newImg=getSize('#preview');alert(newImg.Height);
         if(newImg.Width<div.Width||newImg.Height<div.Height){
           newImg.Width>newImg.Height?(function(){
                if(newImg.Height<div.Height){
                    $('#preview').css('height',div.Height);
                }
            })():(function(){
                if(newImg.Width<div.Width){
                    $('#preview').css('width',div.Width);
                }
            })();
        }*/
        
        
    }
    else
    {

        docObj.select();
        var imgSrc = document.selection.createRange().text;
        var localImagId = document.getElementById("localImag");

       /* localImagId.style.width = "300px";
        localImagId.style.height = "120px";*/

        try
        {
            localImagId.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale)";
            localImagId.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = imgSrc;
        }
        catch(e)
        {
            alert("wrong format");
            return false;
        }
        imgObjPreview.style.display = 'none';
        document.selection.empty();
    }
    return true;
}


