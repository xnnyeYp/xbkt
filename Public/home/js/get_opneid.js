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
			//if(yes==2)
				//$('.next').css({'background-color':'#98d261','color':'#FFF','border-color':'#98d261'});
		});

	var url = "/WeChat/sendMsg";
	var tel=/^1[3|4|5|7|8]\d{9}$/;
	var is_register = 0;
	var submit = 0;
	var code;
	$('#getCode').click(function(){
		var getcode_text = $(this).text();

		if(getcode_text == '获取动态码'){
			var to = $('.telephone').val();

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
						code = data;
					},
					async:false
				});
			}
		}

	});

	$(".activeCode").bind("input propertychange", function () {
		if ($(this).val().length == 6){
			if ($('.activeCode').val() == code) {
				submit++;
				$('.telPs2').css({'color': '#FFF'});
			} else if ($('.activeCode').val()){
				$('.telPs2').css({'color': 'red'});
			} else {
				$('.telPs2').css({'color': '#FFF'});
				yes = 0;
			}
		}
	});

	$('[name=type]').change(function () {
		if(submit == 1){
			if($('input[name=type]:checked').val() == 1 && submit == 1){
				$.ajax({
					url:"/WeChat/check_branch_account",
					type:'post',
					async:false,
					data:{'to':$('.telephone').val()},
					success: function (data) {

						if(data == 1){
							$('.next').css({'background-color': '#98d261', 'color': '#FFF', 'border-color': '#98d261'});
							$('.next').click(function () {
								$('#registerf').submit();
							});
						}else{
							$('.telPs').text('请输入分校管理员手机号');
							$('.telPs').css({'color':'red'});
						}
					}
				});
			}else{
				$('.next').css({'background-color': '#98d261', 'color': '#FFF', 'border-color': '#98d261'});
				$('.next').click(function () {
					$('#registerf').submit();
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


	};