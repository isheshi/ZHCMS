<?php
/**
 * TODO 基础分页的相同代码封装，使前台的代码更少
 * @param $count 要分页的总记录数
 * @param int $pagesize 每页查询条数
 * @return \Think\Page
 */
function getpage($count, $pagesize = 10) {
    $p = new Think\Page($count, $pagesize);
    $p->setConfig('header', '<li style="margin-left:10px" class="rows">共<b>%TOTAL_ROW%</b>条记录&nbsp;每页<b>' . $pagesize . '</b>条&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
    $p->setConfig('prev', '上一页');
    $p->setConfig('next', '下一页');
    $p->setConfig('last', '末页');
    $p->setConfig('first', '首页');
    $p->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');
    $p->lastSuffix = false;//最后一页不显示为总页数
    return $p;
}

// 调试
function debug($data){
    if($data){
        echo "<pre>";
        print_r($data);
    }else{
        echo "数据不存在";
    }

}

// 判断是否有值
function cutOff($str){
    if(!$str){
        $str = '未定义';
    }
    return $str;
}

// 字符串分割为数组
function stringIntoAnArray($str){
    $str = explode(',',$str);
    return $str;
}

function htmlTagsToEscape($cont){
    $str = htmlspecialchars_decode($cont);
    return $str;
}

// 判断是否文字是否溢出
function textProcess($str){
    $num = 20;
    if(isset($str{$num})){
        $str = mb_substr($str,0,$num,"UTF-8").'...';
    }
    return $str;
}

// 分类管理列表分类名称显示
function getTitle($id){
    $str = '';
    // 获取上级菜单id
    $pid = M('category')->where('id='.$id)->getField('pid');
    if ($pid > 0) {
        // 根据上级菜单id遍历上级菜单的标题
        while ($pid > 0) {
            $m = M('category')->find($pid);
            $str = $m['title'].'-->'.$str;
            $pid = $m['pid'];
        }
    }
    // 把获取的上级菜单标题与该标题拼接起来
    $title= M('category')->where('id='.$id)->getField('title');
    $str = $str.$title;
    return $str;
}

// 列表页媒体资源信息标题显示
function getNewTitle($id){
    $str = '';
    // 获取上级菜单id
    // 首先获取媒体资源分类id,标题
    $res = M('list_page')->where('id='.$id)->field('cid,title')->find();
    $pid = M('category')->where('id='.$res['cid'])->getField('pid');
    if ($pid > 0) {
        // 根据上级菜单id遍历上级菜单的标题
        while ($pid > 0) {
            $m = M('category')->find($pid);
            $str = $m['title'].'-->'.$str;
            $pid = $m['pid'];
        }
    }
    // 把获取的多个上级菜单标题与该标题拼接起来
    $title= M('category')->where('id='.$res['cid'])->getField('title');
    if ($title) {
        $str = $str.$title.'-->';
    }
    $str = $str.$res['title'];
    return $str;
}

// 显示图片
function get_cover_url($picture){
    if($picture){
        $url = __ROOT__.'/Uploads/'.$picture;
    }else{
        $url = __ROOT__.'/Uploads/empty.jpg';
    }
    return $url;
}
// 显示链接
function get_url($href){
    if($href){
        $url = $href;
    }else{
        $url = '无';
    }
    return $url;
}
// 显示icon,清空存入数据库的amp;以正常显示图标
function icon($icon) {
    $res = str_replace('amp;','',$icon);
    if($res) {
        $str = $res;
    }else{
        $str = $icon;
    }
    return $str;
}

?>