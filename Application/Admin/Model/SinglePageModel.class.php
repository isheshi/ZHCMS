<?php
namespace Admin\Model;
use Think\Model\RelationModel;

class SinglePageModel extends RelationModel{
    protected $_link = array(
        'nav_id' => array(
			'mapping_type' => self::BELONGS_TO,// 关联类型
			'class_name' => 'navigation',// 关联数据表
			'foreign_key' => 'nav_id', //关联外键名
			'mapping_fields' => 'id,title,url', // 需要查询的关联字段名称
		),
    );
}


