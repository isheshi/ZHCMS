/************************************/
/*        EDIT BY PG-EID1134        */
/*   VERSION 1.0 COPYRIGHT MC-SIN   */
/************************************/
var userAgent = navigator.userAgent.toLowerCase(); 
jQuery.browser = { 
	version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [0,'0'])[1], 
	safari: /webkit/.test( userAgent ), 
	opera: /opera/.test( userAgent ), 
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ), 
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent ) 
}; 
if($.browser.msie && ($.browser.version == '6.0'))
{
	var htmlmark = '<div id="alertBrowser">您的IE版本过旧，建议您使用<a>火狐浏览器</a><span>关闭</span></div>';
	
	setTimeout('function(){$("body").prepend(htmlmark)}','1000');
	
	$('#alertBrowser').css({
		'width':'100%',
		'height':'60px',
		'background':'#fff4d9',
		'font':'14px/60px 微软雅黑',
		'color':'#984b12',
		'text-align':'center',
		'position':'relative'
	}).children('a').css({
		'padding':'0 10px'
	}).attr({'href':'http://softdownload.hao123.com/hao123-soft-online-bcs/soft/F/Firefox-setup.exe'});
	
	$('#alertBrowser span').css({
		'width':'60px',
		'height':'26px',
		'font':'12px/26px 微软雅黑',
		'text-align':'center',
		'color':'#333',
		'position':'absolute',
		'top':'0',	
		'right':'0',	
		'z-index':'2',	
		'cursor':'pointer',
		'title':'关闭'
	}).click(function() {
		
		if(confirm('确定取消本次浏览器升级吗？'))
		{
			$('#alertBrowser').hide();
		}
	});
}

