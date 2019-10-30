/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
        // Define changes to default configuration here. For example:
        // config.language = 'fr';
         //config.uiColor = '#cccccc';
        
        //config.toolbar=[ ['Source','-','Preview','-','Templates'], [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ],['TextColor', 'BGColor'],['Image', 'Flash', 'Table', 'HorizontalRule'] ,['Styles','Format','Font','FontSize','Link', 'Unlink']];


    //工具栏是否可以被收缩
    config.toolbarCanCollapse = false;
    //工具栏的位置
    config.toolbarLocation = 'top';//可选：bottom
    //工具栏默认是否展开
    config.toolbarStartupExpanded = true;

    //字体编辑时的字符集 可以添加常用的中文字符：宋体、楷体、黑体等 plugins/font/plugin.js
    config.font_names='宋体/宋体;黑体/黑体;仿宋/仿宋_GB2312;楷体/楷体_GB2312;隶书/隶书;幼圆/幼圆;微软雅黑/微软雅黑;'+ config.font_names;
        
        //字体编辑时可选的字体大小 plugins/font/plugin.js
    config.fontSize_sizes ='8/8px;9/9px;10/10px;11/11px;12/12px;14/14px;16/16px;18/18px;20/20px;22/22px;24/24px;26/26px;28/28px;36/36px;48/48px;72/72px'
    //设置字体大小时 使用的式样 plugins/font/plugin.js
    config.fontSize_style = {
        element   : 'span',
        styles   : { 'font-size' : '#(size)' },
        overrides : [ { element : 'font', attributes : { 'size' : null } } ]
    };
        
    //当从word里复制文字进来时，是否进行文字的格式化去除 plugins/pastefromword/plugin.js
    config.pasteFromWordIgnoreFontFace = true; //默认为忽略格式
        
    //是否使用<h1><h2>等标签修饰或者代替从word文档中粘贴过来的内容 plugins/pastefromword/plugin.js
    config.pasteFromWordKeepsStructure = true;
        
    //从word中粘贴内容时是否移除格式 plugins/pastefromword/plugin.js
    config.pasteFromWordRemoveStyle = true;
        
        // Remove some buttons provided by the standard plugins, which are
        // not needed in the Standard(s) toolbar.
        config.removeButtons = 'Underline,Subscript,Superscript';

        // Set the most common block elements.
        config.format_tags = 'p;h1;h2;h3;pre';

        // Simplify the dialog windows.
        config.removeDialogTabs = 'image:advanced;link:advanced';
};