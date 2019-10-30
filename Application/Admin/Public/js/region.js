function getcity(obj)
{
	var addr = $(obj).attr("addr");
	$(obj).find("option[value='']").remove();
	var id = $(obj).val();
	$.ajax({
		type:"POST", 
		url:addr,
		data:"action=getcity&id="+id, 
		dataType:"text",
		async:false,
		error:function(err){alert("Loading Err");},
		success:function (msg){ 
			var retArr	=	msg.split('#');
			
			if (retArr[0] == "TRUE"){
				$("#city").empty();
				var cityArr	=	retArr[1].split(',');;
				for (var i in cityArr)
				{
					tempArr	=	cityArr[i].split('-');
					$("#city").append('<option value="'+tempArr[0]+'">'+tempArr[1]+'</option>');
				}
				getarea($("#city"))
				return true;
			}else{
				return false;
			}
		}
	});
}

function getarea(obj)
{
	var addr = $(obj).attr("addr");
	$(obj).find("option[value='']").remove();
	var id = $(obj).val();
	$.ajax({
		type:"POST", 
		url:addr,
		data:"action=getarea&id="+id, 
		dataType:"text",
		async:false,
		error:function(err){alert("Loading Err");},
		success:function (msg){
			var retArr	=	msg.split('#');
			
			if (retArr[0] == "TRUE"){
				$("#area").empty();
				var areaArr	=	retArr[1].split(',');;
				for (var i in areaArr)
				{
					tempArr	=	areaArr[i].split('-');
					$("#area").append('<option value="'+tempArr[0]+'">'+tempArr[1]+'</option>');
				}
				return true;
			}else{
				return false;
			}
		}
	});
}