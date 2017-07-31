window.onload=function(){
		var teleNum=0;
		var codeNum=0;
		var yes=0;
		var ok=0;
		var timer=null;
		var time=null;
		var sec=60;
		var reset_code = 0;
		// $(input:text).val(null);
		function borderColor(obj){
			obj.style.borderColor='#98d261';
			$('.telPs').css({'color':'#FFF'});
		}
		function borderColor1(obj){
			obj.style.borderColor='#e5e5e5';
		}
		/*倒计时函数*/
		function a(){
			$('#getCode').css({'background':'#eee','border-color':'#eee','color':'#c3c3c3'});
			$('#getCode').html($('#getCode').html()+'(60s)');
			clearInterval(timer);
			timer=setInterval(function(){
				sec--;
				$('#getCode').html('获取动态码'+'('+sec+'s)');
			},1000);

			time=setTimeout(function(){
				$('#getCode').css({'background':'#98d261','border-color':'#98d261','color':'#FFF'});
				$('#getCode').html('获取动态码');
				clearInterval(timer);
				sec=60;
				$('#getCode').click(function(){a()});
			},60000)
		}
		/*手机号判断*/
		$('.telephone').blur(function(){
			borderColor1(this);
			var tel=/^1[3|4|5|7|8]\d{9}$/;
			var strTel=$(this).val();
			if(tel.test(strTel))
			{
				yes++;
				$('.ps1').css({'color':'#FFF'});
			}else if($(this).val())
				$('.ps1').css({'color':'red'});	
			if(yes==2)
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
		});
		$('.telephone').focus(function(){borderColor(this)});
		$('.activeCode').focus(function(){borderColor(this)});
		$('.activeCode').blur(function(){borderColor1(this)});
		$('#getCode').click(function(){
			if($('.telephone').val()&&yes>0)
				a();
			else $('.telPs').css({'color':'red'});
		});
		$(".telephone").bind("input propertychange",function(){
　　		//console.log($(this).val().length);打印输入框字符长度
			if($(this).val().length==0)
				$('.ps1').css({'color':'#FFF'});
			if(yes==2)
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
 		});
 		$(".activeCode").bind("input propertychange",function(){
			if($(this).val().length==6)
				yes++;
			if(yes==2)
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
 		});
 		/*密码正则判断*/
		$('.password').blur(function(){/*typeof($(this).val()==string*/
			// b($(this));
			var strPass=$(this).val();
			var zhengze= /^[a-z0-9_-]{6,18}$/;
			if(zhengze.test(strPass))
			{
				ok++;
				$('.ps1').css({'color':'#FFF'});
			}
			else if($(this).val())
				$('.ps1').css({'color':'red'});
			if(ok==2)
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
		});
		$('.passwordAgain').blur(function(){
			if($(this).val()==$('.password').val())
			{
				ok++;
				$('.ps2').css({'color':'#FFF'});
			}
			else if($(this).val())
				$('.ps2').css({'color':'red'});
			if(ok==2)
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
		});
		$('.new_pass').blur(function(){
			var zhengze= /^[a-z0-9_-]{6,18}$/;
				$('.telPs2').css('color','#fff');
				$('.submit').css({'background':'#ddd','color':'#313131'});
			if(!zhengze.test($(this).val())){
				$('.telPs2').css('color','red');
			}
		});
		$('.re_pass').keyup(function(){
			var zhengze= /^[a-z0-9_-]{6,18}$/;
				$('.telPs3').css('color','#fff');
				$('.submit').css({'background':'#ddd','color':'#313131'});
			if($(this).val()==$('.new_pass').val() && reset_code == $('.activeCode').val()){//判断原密码是否和确认密码相同
				$('.submit').css({'background':'#98d261','color':'#fff'});
				$('.submit').removeAttr('disabled');
			}else{
				$('.telPs3').css('color','red');
				$('.submit').attr('disabled','true');
			}
		});

	/**
	 * 发送ajax请求，获取验证码
	 */
	$('#getCode').click(function(){
		var getcode_text = $(this).text();
		var tel=/^1[3|4|5|7|8]\d{9}$/;
		if(getcode_text == '获取动态码(60s)') {
			var to = $('.telephone').val();
			if (tel.test(to)) {
				$.ajax({
					url:'/user/reset_passwd_code',
					type:'post',
					data:{
						to:to,
					},
					success:function(data){
						reset_code = data;
					},
					async:false,
				});
			}
		}
	});
	};