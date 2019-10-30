<?php
    header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="HandheldFriendly" content="true" />
<meta name="MobileOptimized" content="320" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<title>404错误</title>
<style>
.big404 {
    text-align: center;
    margin-top: 20px;
}
.big404 img {
    width: 640px;
    height: 400px;
    margin-top: 10px auto;
}
.big404 h1 {
    font-size: 18px;
    margin-top: 40px;
    color: #333;
}
.big404 a {
    margin: 80px 5%;
    display: block;
    color: #333;
    text-decoration: none;
    border: 1px solid #ccc;
    height: 40px;
    line-height: 40px;
    border-radius: 3px;
}
</style>
</head>
<body style="max-width:640px;margin:0 auto;">
    <div class="big404">
        <img src="/404.png" alt="404" onclick="tc()" />
        <a href="javascript:history.back();">返回上一页</a>
    </div>
    <div style="display:none">
        <?php echo strip_tags($e['message']);?><br/>------------
        错误位置：<br/>------------
        FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?>
    </div>

<script type="text/javascript" src="/Public/Admin/js/jquery.min.js"></script>
<script>
function tc(){
    $.alert("<?php echo strip_tags($e['message']);?>",function() {
        $("body").html("FILE: <?php echo $e['file'] ;?> &#12288;LINE: <?php echo $e['line'];?>");
    });
}
</script>
</body>
</html>