/*打开窗口*/
var openWin = function (options) {
	var defaults = {
		title:'信息提示',
		width:280,
		height:180,
		content:'<div class="load">&nbsp;</div>',
		target:false,
		overflow:false,
		AutoClose:false,
		AutoCloseText:'秒后自动关闭',
		AlphaBG:0.5
	}
	var options = $.extend(defaults, options);
	//创建
	var creatWinbox=function(){
		var ww = options.width/2;
		var wh = options.height/2;
		//内容
		var html = '<section id="WebMisWinBg" style="height: 100%; background-color: #000;"></section>';
		html += '<section id="WebMisWin" style="width: 100%; height: 100%; max-width:'+options.width+'px; max-height:'+options.height+'px; left: 50%; margin-left: -'+ww+'px; top: 50%; margin-top: -'+wh+'px;">';
		html += '  <header id="WebMisWinTop" class="WebMisWin_top">';
		html += '    <span class="title">'+options.title+'</span>';
		html += '    <a href="#" class="close"><em></em><!--[if lt IE 9]>Close<![endif]--></a>';
		html += '  </header>';
		html += '  <section class="WebMisWin_ct" style="height: '+(options.height-37)+'px;">'+options.content+'</section>';
		html += '</section>';
		//加载信息框
		$('#WebMisWinBg,#WebMisWin').remove();
		$('body').prepend(html);
		$('#WebMisWinBg').fadeTo("slow",options.AlphaBG);
		//点击关闭窗口
		$('#WebMisWin .close').click(function(){
			closeWin(options.target);
			return false;
		});
		//ESC键关闭
		$(document).keydown(function(e){
			if(e.which == 27){closeWin(options.target);}
		});
		//添加移动
		WebMisWinMove('#WebMisWinTop','#WebMisWin');
	}
	
	//窗口类型
	if(options.overflow){
		options.content = '<div id="WebMisWinCT" style="width: 100%; height: '+(options.height-37)+'px; overflow: auto;">'+options.content+'</div>';
	}else if(options.AutoClose){
		options.content = '<div style="line-height: 24px; text-align: center; padding-top: 40px;">'+options.content;
		options.content += '<br /><span style="color: #666;"><b id="WebMisWinIntNum" class="red">&nbsp;</b> '+options.AutoCloseText+'</span>';
		options.content += '</div>';
		//开始倒计时
		WinInterval(options.AutoClose,options.target,'#WebMisWinIntNum');
	}else{
		options.content = '<div id="WebMisWinCT" style="width: 100%; height: '+(options.height-37)+'px;">'+options.content+'</div>';
	}
	creatWinbox();
}

/*关闭窗口*/
var closeWin = function (target) {
	$('#WebMisWinBg,#WebMisWin').fadeOut(function(){$(this).remove();});
	//跳转
	if(target && target!='false'){
		window.location.href = $base_url+target;
	}
	clearInterval(WebMisInt);
}

/*加载内容*/
var loadWin = function (data) {
	$('#WebMisWinCT').html(data);
}

/*添加选项卡*/
var addWinMenu = function (options) {
	var defaults = {change: '#winTopMenuBody', menus: ['选项卡1','选项卡2','选项卡3']}
	var options = $.extend(defaults, options);
	//添加选项
	var menu = options.menus;
	var html = '<span id="WebMisTopMenu" class="WebMisTopMenu">';
	var an = 'an1';
	for (var i=0; i<menu.length; i++) {
		if (i!=0) {an = 'an2';}
		html += '<a herf="#" class="'+an+'" num="'+i+'">'+menu[i]+'</a>';
	}
	html += '</span>';
	$('#WebMisWinTop').append(html);
	//添加事项
 	$('#WebMisTopMenu a').click(function() {
		var num = $(this).attr('num');
		var n = 0;
		//初始化
		$('#WebMisTopMenu a').each(function(){
			$(this).attr('class','an2');
			$(options.change+n).hide();
			n++;
		});
		//改变
		$(this).attr('class','an1');
		$(options.change+num).show();
	});
}

/* 倒计时信息提示 */
var WebMisInt,WebMisTime;
function WinInterval(time,target,numID){
	WebMisTime = time;
	WebMisInt = setInterval("WinIntervalFun('"+target+"','"+numID+"')",1000);
}
function WinIntervalFun(target,numID){
	if (WebMisTime == 0) {
		$.webmis.win('close',target);
		clearInterval(WebMisInt);
	}
	$(numID).text(WebMisTime);
	WebMisTime--;
}

/* 移动窗口 */
function WebMisWinMove(click,move){
	var _move = false;
	var _x,_y,_w,_h,x,y;
	var padding = 0;
	//鼠标按下
	$(click).unbind().mousedown(function(e){
		_move=true;
		_x=e.pageX-parseInt($(move).css("left"));
		_y=e.pageY-parseInt($(move).css("top"));
		_w = $(window).width()-padding;
		_h = $(window).height()-padding;
	});
	//鼠标移动
	$(document).mousemove(function(e){
		if(_move){
			x=e.pageX-_x;
			y=e.pageY-_y;
			//控制范围
			if(y < padding){y=padding};
			if(x > _w){x=_w};
			if(y > _h){y=_h};
			//当前位置
			$(move).css({'top':y,'left':x});
		}
	}).mouseup(function(){
		_move=false;
	});
}