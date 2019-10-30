<?php
namespace Admin\Model;
use Think\Model\RelationModel;

class AdminModel extends RelationModel{
    protected $_link = array(
        'gid' => array(
			'mapping_type' => self::BELONGS_TO,// 关联类型
			'class_name' => 'AdminGroups',// 关联数据表
			'foreign_key' => 'gid', //关联外键名
			'mapping_fields' => 'gid,title', // 需要查询的关联字段名称
		),
    );

}


