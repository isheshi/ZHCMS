<?php
$type=$_GET['type']; //上传类型
$tourl=$_GET['tourl']; //提交地址
$url=$_GET['url']; //站点目录
$UpFile=$_GET['UpFile']; //上传文件夹目录名称
?>
//-------------图片和文件显示------------------------
function adminFile_<?php echo $type?>(FileUrl,w,h,path) {
	//if($.isFunction(adminFile)){retrun ;}
	var url = FileUrl;
	url = url.split(".");
	url = url[url.length-1].toLowerCase();
	var _data ="";
	if (url=="jpg" || url=="gif" || url=="png"){//图片
		_data+="<img src=\""+path+"/"+FileUrl+"\" rel=\""+path+"/"+FileUrl+"\" width=\""+w+"\" height=\""+h+"\" class=\"preview\" border=0 onerror=this.src='<?=$url?>/App/Admin/Public/images/nopic.gif'>";
	}
	else if (url=="mp3" || url=="wma"){ //音频 invert()
		_data+="<EMBED style='FILTER: xray(); WIDTH: 100px; HEIGHT: "+ h +"px' src=\""+path+"/"+FileUrl+"\" type=application/x-mplayer2 volume='0' autostart='0' loop='-1'>";
	}
	else{
		_data=FileUrl;
	}
    _data+='<a id="del<?php echo $type?>">删除</a>';
	return _data;
}
$(function(){
	$("#<?php echo $type?>submitUplod").click(function (){
		var parentH=$(this).parent("div");
        var file=parentH.find("input[type='file']");
        if(file.val()=="" || file.val()==null || file.val()== "undefined"){humane.success("没有上传文件");return false;}
        parentH.find(".FName").html("<img src='<?php echo $url?>/App/Admin/Public/images/loading.gif'>");
		var fileId=file.attr("id");
		var fType=$(this).attr("rel");
		var data = { fileType: fType }
		$.ajaxFileUpload ({
			//url:"<?php echo $tourl?>&fileType="+fType, //你处理上传文件的服务端
            url:"<?php echo $tourl?>", //你处理上传文件的服务端
			secureuri:false, //与页面处理代码中file相对应的ID值
			data: data,
			fileElementId:fileId,//文件选择框的id属性
			dataType: 'json', //返回数据类型:text，xml，json，html,scritp,jsonp五种
			success: function (data,status) {
				parentH.find(".FName").html("");
				humane.success(data.msg);
                if(data.error=='err'){return false;}
                var inputNew=$("#"+fType+"_path_new");
				if(inputNew.size()<1){
                	var fType=$("#<?php echo $type?>_path").attr('name');
					parentH.append("<input type='hidden' name='"+fType+"_new' id='"+fType+"_new' value='"+data.imgurl+"'>");
				}else{
					parentH.find(inputNew).val(data.imgurl);
				}
                $("#<?php echo $type?>Name").html(adminFile_<?php echo $type?>(data.temurl,30,30,'<?php echo $UpFile?>'));
                imagePreview();
                
				parentH.find("input[type='file']").val("");
			},
			error: function (data, status, e){
				humane.error('上传失败');
				parentH.find(".FName").html("");
			}
		})
	});
    var dataUrl=$("#<?php echo $type?>_path").val();
    if(dataUrl){
        var dataHtml=adminFile_<?php echo $type?>(dataUrl,30,30,'<?php echo $UpFile?>');
        $("#<?php echo $type?>Name").html(dataHtml);
        imagePreview();
    }
    
    $("#del<?php echo $type?>").live("click",function(){
    	$("#<?php echo $type?>_path").val('');
        $("#<?php echo $type?>_path_new").val('');
        $("#<?php echo $type?>Name").html('');
    });
    
});

var _data="";
_data+='<div class="divUpload">';
_data+='	<span class="uploadF">';
_data+='		<input type="file" id="file_<?php echo $type?>" name="file" class="fileSub" onchange="<?php echo $type?>Name.innerHTML=this.value" size="1" >';
_data+='		<a>浏 览</a>';
_data+='	</span>';
_data+='	<a class="submitUplod" id="<?php echo $type?>submitUplod" rel="<?php echo $type?>">上 传</a>';
_data+='	<span class="FName" id="<?php echo $type?>Name"></span>';
_data+='</div>';

document.write(_data);