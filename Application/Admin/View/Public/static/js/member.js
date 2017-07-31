/*普通会员开始*/
$(function(){
	$('.edict').each(function(i){
		$(this).data('index',i);
	});
	$('.cancel').each(function(i){
		$(this).data('index',i);
	});
	$('.save').each(function(i){
		$(this).data('index',i);
	});
	//点击编辑出现表单
	$('.edict').click(function(){
		var index = $(this).data('index');
		$('.showDiv'+index).css('display','none');
		$('.hiddenDiv'+index).css('display','block');
	});
	//取消
	$('.cancel').click(function(){
		var index = $(this).data('index');
		$('.showDiv'+index).css('display','block');
		$('.hiddenDiv'+index).css('display','none');
	});
	$('.save').click(function(){
		var index = $(this).data('index');
		if($('.hiddenDiv'+index).css('display') == 'none'){
			return;
		}
		var numbering = $('.hiddenDiv'+index+' input[name="numbering"]').val();
		var status = $('.hiddenDiv'+index+' select[name="status"]').val();
		var type = $(this).attr('type');
		var _this = this;
		//console.log(numbering,username,status,type);
		/*$.ajax({
			type:'POST',
			url:$(_this).attr('url'),
			data:"username="+username+"&status="+status+"&type="+type,
			success:function(msg){
				console.log(msg);
			}
		});*/
		if(type == 0){//修改普通会员
			var username = $('.hiddenDiv'+index+' input[name="username"]').val();
			$.post($(_this).attr('url'),{
				'numbering':numbering,
				'username':username,
				'status':status,
				'type':type
			},function(data){
				if(data == '1'){
					alert('修改成功!');
					var statusText = status==0?'冻结':'正常';
					$('.showDiv'+index).css('display','block');
					$('.hiddenDiv'+index).css('display','none');
					$('.showDiv'+index).find('.username').html(username);
					$('.showDiv'+index).find('.status').html(statusText);
				}else if(data == '0'){
					alert('修改失败!');
					$('.showDiv'+index).css('display','block');
					$('.hiddenDiv'+index).css('display','none');
				}else{
					alert('参数错误!');
					$('.showDiv'+index).css('display','block');
					$('.hiddenDiv'+index).css('display','none');
				}
			});
		}else if(type == 1){//修改商户会员
			var name = $('.hiddenDiv'+index+' input[name="name"]').val();
			//console.log($(this).attr('url'),numbering,name,status,type);
			$.post($(_this).attr('url'),{
			'numbering':numbering,
			'name':name,
			'status':status,
			'type':type
		},function(data){
			console.log(data)
			if(data == '1'){
				alert('修改成功!');
				var statusText = status==0?'冻结':'正常';
				$('.showDiv'+index).css('display','block');
				$('.hiddenDiv'+index).css('display','none');
				$('.showDiv'+index).find('.name').html(name);
				$('.showDiv'+index).find('.status').html(statusText);
			}else if(data == '0'){
				alert('修改失败!');
				$('.showDiv'+index).css('display','block');
				$('.hiddenDiv'+index).css('display','none');
			}else{
				alert('参数错误!');
				$('.showDiv'+index).css('display','block');
				$('.hiddenDiv'+index).css('display','none');
			}
		});
		}
		
	});
	
	
});
//普通会员安全设置
$(function(){
	$('.edict_security').click(function(){
		$('.wechat_show').css('display','none');
		$('.wechat_hidden').css('display','block');
		$('.mphone_show').css('display','none');
		$('.mphone_hidden').css('display','block');
	});
	$('.save_security').click(function(){
		var url = $(this).attr('url');
		var wechat = $('input[name="wechat"]').val();
		var mphone = $('input[name="mphone"]').val();
		var id = $('input[name="id"]').val();
		var type = $(this).attr('type');
		if($('.wechat_hidden').css('display') == 'none'){
			return;
		}
		console.log(wechat,mphone,id,type);
		$.post(url,{
			'wechat':wechat,
			'mphone':mphone,
			'type':type,
			'id':id
		},function(data){
			if(data == '1'){
				alert('修改成功！');
				$('.wechat_show').html(wechat);
				$('.mphone_show').html(mphone);
				$('.wechat_show').css('display','block');
				$('.wechat_hidden').css('display','none');
				$('.mphone_show').css('display','block');
				$('.mphone_hidden').css('display','none');
			}else if(data == '0'){
				alert('修改失败！');
			}else{
				
				alert('非法操作!');
			}
		});
	});
});

//编辑商户会员
$(function(){
	$('.edict_gro').click(function(){
		$('.type_show,.wechat_show,.mphone_show,.password_show,.role_show').css('display','none');
		$('.type_hidden,.wechat_hidden,.mphone_hidden,.password_hidden,.role_hidden').css('display','block');
	});
});
/*普通会员结束*/