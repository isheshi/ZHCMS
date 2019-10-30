<?php
$action=$_GET['action']; //站点目录
$url=$_GET['url']; //站点目录
?>
<?php if($action=='ajaxlistinput'){ ?>
$(function(){
	//----------------列表和推荐值修改----------------------
	$(".TextTd").live("click", function() { // dblclick双击
		var _this=$(this);
		var value = _this.text();
		var id = _this.attr("rel");
		var isfresh = _this.attr("isfresh");
		var objName = _this.attr("name");
		var objPara = _this.attr("para");
		//var valueid = _this.next().text();
		if(value == "null") value = "";
		if(_this.find("input").length || id==""){return false;}
		_this.html("<input class=\"textPercentage TextBlur\" name=\""+objName+"\" title=\""+value+"\" rel=\""+id+"\" isfresh=\""+isfresh+"\" para=\""+objPara+"\" value=\'"+value+"\' />");
		$(".TextBlur").focus();
	});
	$(".TextBlur").live("blur", function() { //失去焦点保存
    	
		var _this=$(this);
		var objTitle=_this.attr("title");
        var objVal=_this.val();
        
		if(objTitle==objVal || objVal==""){_this.hide().parent("td").html(objTitle);return false;}// 判断是否和原始数据相同
        ajaxData(_this);
		/*$.post("<?php echo $url?>",{id:objId,fieldName:objField,Val:objVal},function(result){
        	//alert(result);
            if(result=="success"){
				_this.parent("td").html(objVal);
				_this.hide();
                if(objIsFresh==1){
                    humane.success('修改完毕',function(){
                        location.reload();
                    });
                }else{
					humane.success('修改完毕');
                }
			}
		});*/
	});
    
     //----------------select状态修改----------------------
	$(".selectTd").change(function() {
		var _this=$(this);
         _this.removeClass().addClass("select_color_"+_this.val());
		if(_this.val()==""){return false;}// 判断是否和原始数据相同
        ajaxData(_this);
	});
    
});
function ajaxData(_this){
    var objId=_this.attr("rel");
    var objField=_this.attr("name");
    var objPara=_this.attr("para");
    var objVal=_this.val();
	var objIsFresh=_this.attr("isfresh");
    $.post("<?php echo $url?>",{id:objId,fieldName:objField,Val:objVal},function(result){
    	//return result;
        if(result=="success"){
        
        	if(objPara=="input"){
                _this.parent("td").html(objVal);
                _this.hide();
            }
        
            if(objIsFresh==1){
                humane.success('修改完毕',function(){
                    location.reload();
                });
            }else{
                humane.success('修改完毕');
            }
        }else{
            humane.error('修改失败',function(){
                location.reload();
            });
        }
    });
}
<?php }?>
<?php if($action=='ajaxPara'){  ?>
$(function(){
     //----------------select状态修改----------------------
	$(".selectPara").change(function() {
		var _this=$(this);
         _this.removeClass().addClass("select_color_"+_this.val());
		if(_this.val()==""){return false;}// 判断是否和原始数据相同
        ajaxParaData(_this);
        
	});
});
function ajaxParaData(_this){
	
    var objId=_this.attr("rel");//当前id
    //var objField=_this.attr("name"); //当前字段
    var objVal=_this.val();//当前选中的值
    var objpid=_this.attr("pid");//分类id
    $.post("<?php echo $url?>",{para_id:objId,pid:objpid,ParaType:objVal},function(result){
        if(result=="success"){
            humane.success('修改完毕',function(){
                location.reload();
            });
        }else if(result=="delok"){
            humane.info('删除成功',function(){
                location.reload();
            });
        }
        else{
            humane.error('修改失败',function(){
                location.reload();
            });
        }
    });
}
<?php } ?>