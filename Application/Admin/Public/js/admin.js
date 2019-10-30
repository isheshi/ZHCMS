$(function(){
//---------- 后台登陆页面自动聚焦用户名文本框 ---------	
 	$("#admin_username").focus();
	$("#login1_r").keyup(function (e) {
			  var curKey = e.which; 
			  if(curKey==13){
			  		$("#loginbtn").click();
					return false; 
			  }
	 });
	 
//----------------checkbox 批量选中----------------
	/*$("#checkAll").click(function () {
		//is_select = document.getElementById("checkAll").checked;
		is_select = $(this).prop('checked');
		$("form input[type='checkbox']").not("input[id='checkAll']").attr("checked", is_select);
	});*/
	$('#checkAll').click(function() {
		is_select = $(this).prop('checked');
		$("#myform input[type='checkbox']").not("input[id='checkAll']").attr('checked', is_select);
		
		//选中表格出现背景色
		if(is_select){
			$("#myform table tr").not($("#myform table tr:first,#myform table tr:last")).addClass("trbgcolor");
		}else{
			$("#myform table tr").removeClass("trbgcolor");
		}
	});
    $("#myform").delegate('input[type=checkbox]','click',function() {
		//根据当前状态调整全选的显示状态; 如果当前页面全部被选, 则为黑色对号; 有被选但不是全部, 则为灰色对号; 没有被选则没有对好;
		is_select = $(this).prop('checked');
		selected = $('#myform input[type=checkbox]:checked').not("input[id='checkAll']").size();
		is_checkall = ( $('#myform input[type=checkbox]').not("input[id='checkAll']").size() != selected );
		if(selected == 0) {
			$('#checkAll').prop('checked', is_select);
			$('#checkAll').prop('indeterminate', false);
		} else {
			$('#checkAll').prop('checked', is_select);
			$('#checkAll').prop('indeterminate', is_checkall);
		}
	});
//----------------表格css-----------------
	$("#myform input[name='id[]']").click(function(){
		$(this).parents("tr").toggleClass("trbgcolor");
	})
	$("#myform table tr:even").not($("#myform table tr:first")).addClass("oddbg");
	//$("#myform table tr:odd").addClass("oddbg");
	$("#myform table tr.oddbg td").hover(function(){
		$(this).parents("tr").removeClass("oddbg");
	},function(){
		$(this).parents("tr").addClass("oddbg");
	});
	
	
	
});

//--------------类别批量添加---------------------
function addClass(id,ClassName,depth){
	//$("#ClassAdd").animate({height:"200px"});
	var obj=$("#ClassAdd");
	//obj.find("#depth").val(parseInt(depth)+1);//客户端获取当前深度，服务器端就不用获取了
	obj.find("#depth").val(depth);//客户端获取当前深度，服务器端就不用获取了
	var obj_pid=obj.find("#parent_id");
	var parent_id=obj_pid.val();
	obj_pid.val(id);
	obj.find("#class_title strong").html(ClassName);
	if(id!=parent_id && parent_id && obj.css("display")!="none"){ //如果不是当前信息则多执行一次效果
		obj.slideToggle();
	}
	obj.slideToggle();
};