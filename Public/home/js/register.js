window.onload=function(){
		var teleNum=0;
		var codeNum=0;
		var yes=0;
		var ok=0;
		var timer=null;
		var time=null;
		var sec=60;
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

	var url = "/user/register_code";
	var tel=/^1[3|4|5|7|8]\d{9}$/;
	var is_register = 0;
	$('#getCode').click(function(){
		var getcode_text = $(this).text();

		if(getcode_text == '获取动态码'){
			var to = $('.telephone').val();

			//检查该手机号是否被注册
			$.ajax({
				url:'/user/is_register',
				type:'post',
				data:{
					'mphone':to
				},
				success: function (data) {
					if(data == 1){
						is_register = 1;
						$('.telPs').text('该手机号已被注册');
						$('.telPs').css({'color':'red'});
					}
				},
				async:false
			});
			if($('.telephone').val()&&yes>0 && is_register == 0){
				a();
			}else{
				$('.telPs').css({'color':'red'});
			}
			if(tel.test(to) && is_register == 0) {
				$.ajax({
					url:url,
					type:'get',
					data:{
						'to':to,
					},
					success:function(data){
						var yes = 0;
						$(".activeCode").bind("input propertychange", function () {
							if ($(this).val().length == 6)
								yes++;
							if (yes) {
								$('.next').css({'background-color': '#98d261', 'color': '#FFF', 'border-color': '#98d261'});
								if ($('.activeCode').val() == data) {
									$('.next').click(function () {
										$('#registerf').submit();
									});
									$('.telPs2').css({'color': '#FFF'});
								}
								else if ($('.activeCode').val())
									$('.telPs2').css({'color': 'red'});
								else {
									$('.telPs2').css({'color': '#FFF'});
									yes = 0;
								}
							}
						});
					}
				});
			}
		}

	});

		$('.telephone').focus(function(){borderColor(this)});
		$('.activeCode').focus(function(){borderColor(this)});
		$('.activeCode').blur(function(){borderColor1(this)});

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
		$('.passwordFirst').blur(function(){/*typeof($(this).val()==string*/
			// b($(this));
			var strPass=$(this).val();
			var zhengze= /^[a-z0-9_-]{6,18}$/;
			if(zhengze.test(strPass))
			{
				ok++;
				$('.ps1').css({'color':'#FFF'});
			}else if($(this).val()){
				alert(1);
				//$('.ps1').css({'color':'red'});
			}
			if(ok==2){
				$('.ps1').css({'color':'#FFF'});
				$('.ps2').css({'color':'#FFF'});
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
			}document.title=ok;
		});
		$('.passwordAgain').change(function(){
			var pattern=$('.password').val();
			if(pattern.match($(this).val()))
			{
				$('.ps2').css({'color':'#FFF'});
				if($(this).val().length==pattern.length){
					ok++;
				}
			}
			else if($(this).val()||($(this).val().length!=pattern.length)){
				$('.ps2').css({'color':'red'});
			}document.title=ok;
			if(ok==2){
				$('.ps1').css({'color':'#FFF'});
				$('.ps2').css({'color':'#FFF'});
				$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
			}
		});
	};