/*
 * Image preview script 
 * powered by jQuery (http://www.jquery.com)
 * 
 * written by Alen Grakalic (http://cssglobe.com)
 * 
 * for more info visit http://cssglobe.com/post/1695/easiest-tooltip-and-image-preview-using-jquery
 *
 */
 
this.imagePreview = function(){	
	/* CONFIG */
		
		xOffset = 10;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		// #pre_view{position:absolute;border:1px solid #ccc;background:#333;padding:5px;display:none;color:#fff;}
	/* END CONFIG */
	$("img.preview").hover(function(e){
		this.t = this.title;
		this.title = "";	
		var c = (this.t != "") ? "<br/>" + this.t : "";
		$("body").append("<div id='pre_view'><img src='"+ this.src +"' alt='Image preview' height='150' />"+ c +"</div>");	
		
		var obj = $(this);
		var top = obj.offset().top;
		var left = obj.offset().left;
		top -= ( $("#pre_view").outerHeight(true) - obj.outerHeight() ) / 2;
		if ( left + obj.outerWidth(true) + $("#pre_view").outerWidth(true) + 5 > document.documentElement.clientWidth ) {
			left -= $("#pre_view").outerWidth(true) + 5;
		}
		else {
			left += obj.outerWidth(true) + 5;
		}
		//var top = e.pageY - xOffset;
		//var left = e.pageX + yOffset;
		$("#pre_view")
			.css("top",top + "px")
			.css("left",left + "px")
			.fadeIn("fast");
						
    },
	function(){
		this.title = this.t;	
		$("#pre_view").remove();
    });	
	/*$("img.preview").mousemove(function(e){
		$("#pre_view")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px");
	});	*/		
};


// starting the script on page load
/*$(document).ready(function(){
	imagePreview();
});*/