<?php
namespace Admin\Controller;
use Myself\File;
use Myself\FileSystem;
class SiteController extends CommonController {

    protected $conf=array("单行文本框","文本域","下拉框","链接/不可修改值"); // 配置类型
	protected $confCate=array("站点基本信息","联系方式","SEO设置"); // 配置分类
	
	public function _initialize(){
		parent::_initialize();
		$this->data = require('Application/Common/Conf/site.cn.php');
	}
	
	/**
	 * 配置列表
	 */
    public function index(){
		$data = $this->data;
		$this->assign('data',$data);
		$this->display();
	}

	/**
	 * 配置列表信息写入配置文件
	 */
	public function save(){
		$data = I('post.');
		$old_data = $this->data;
		
		foreach($data as $key => $value){
			foreach($old_data as $key2 => $value2){
				if($key == intval($value2['id']) && $value != $value2['default']){
					// 查找字符串是否有该字符,判断是否为下拉框
					if(strpos($value2['option'],$value) !== false){
						$old_data[$key2]['optionKey'] = $value;
					}else{
						$old_data[$key2]['default'] = $value;
					}
				}
			}
		}
		$res = File::createFile('Application/Common/Conf/site.cn.php' ,"<?php return ".var_export( $old_data, TRUE ).";");
    	$this->ajaxReturn("OK");
	}
	
	/**
	 * 配置管理
	 */
    public function indexConf(){		
		$data = $this->data;
		$keyword = I('keyword','','trim');
		if($keyword){
			$data = $this->search($keyword);
		}
		$count = count($data);
		$num = 10;
		$p = getpage($count,$num);
		// 按需读取配置文件显示分页数据
		$numberPages = I('p',0,'intval');
		// 声明数组储存当页数据
		$dataArr = [];
		if($numberPages > 1){
			// 根据页数查出当页数据,先获取当页开始和最后读取数据的索引位置
			$firstRow = ($num * ($numberPages-1)) - 1;
			$listRow = ($num * $numberPages);
			// dump($firstRow);dump($listRow);
			foreach($data as $key => $value){
				// 读取索引位置后的数据并接为数组，其长度为$num
				if($key > $firstRow && $key < $listRow){
					$dataArr[] = $value;
				}
			}
		}else{
			foreach($data as $key => $value){
				// 读取第一页数据并接为数组，其长度为$num，最后一个元素索引为$num-1
				if($key < $num){
					$dataArr[] = $value;
				}
			}
		}
		// dump($dataArr);
        $this->assign('data', $dataArr);
        $this->assign('count', $count); 
        $this->assign('page', $p->show()); 
		$this->display();
	}

	/**
	 * 配置管理添加与编辑
	 */
	public function add(){
		$id = I('id',0,'intval');
		if($id){
			$data = $this->getDataId($id);
			$this->assign('data',$data);
		}

		$this->assign('conf',$this->conf);
		$this->assign('confCate',$this->confCate);
		$this->display();
	}
	
	/**
	 * 配置管理信息写入配置文件
	 */
	public function confSave(){
		$id = I('id',0,'intval');
		$configArr = I('post.');
		if(empty($configArr['zn_title']) || empty($configArr['el_title'])){
	    	$this->ajaxReturn("请先填写必填项中文名称和英文名称！");
		}
		$validation = $this->dataValidation($id,$configArr['zn_title'],$configArr['el_title']);
		if($validation){
			$this->ajaxReturn("中文名称或英文名称已存在！");
		}

		// 编辑
		if($id){
			$data = $this->setDataId($id,$configArr);
			File::createFile('Application/Common/Conf/site.cn.php' ,"<?php return ".var_export( $data, TRUE ).";");
		}else{ 
			// 添加
			// 判断配置文件中是否存在，依据是id
			$configArr['id'] = $this->getDataId();
			// 配置文件追加数组，先保存原来数组再添加新数组，最后两数组合并追加
			$old_data = $this->data;
			// dump($old_data);
			$data[] = $configArr;
			File::createFile('Application/Common/Conf/site.cn.php' ,"<?php return ".var_export( $data, TRUE ).";");
			if(is_array($old_data)){
				$data = array_merge($old_data, $data);// 数组合并
				File::createFile('Application/Common/Conf/site.cn.php' ,"<?php return ".var_export( $data, TRUE ).";");
			}
		}
    	$this->ajaxReturn("OK");
	}

	// 删除配置文件某个数组
	public function delete(){
		$id = I('id',0,'intval');
		$data = $this->delDataId($id);
		File::createFile('Application/Common/Conf/site.cn.php' ,"<?php return ".var_export( $data, TRUE ).";");
		$this->ajaxReturn("OK");
	}
	
	// 保证(配置管理文件里)生成唯一id值或者根据id查询配置文件某个数组（当$id大于0）
	public function getDataId($id = 0){
		$data = $this->data;
		if($id){
			// 根据id查询配置文件某个数组（当$id大于0）
			foreach($data as $value){
				if($value['id'] == $id){
					$id = $value;
				}
			}
		}else{
			// 保证(配置管理文件里)生成唯一id值
			$id = rand(1,100000);
			foreach($data as $value){
				if($value['id'] == $id){
					$this->getDataId();
				}
			}
		}
		return $id;
	}

	// （编辑时）从配置文件中替换某个数组
	public function setDataId($id,$arr){
		$data = $this->data;
		foreach($data as $key => $value){
			// 替换
			if($value['id'] == $id){
				$data[$key] = $arr;
			}
		}
		return $data;
	}

	// （添加和编辑时）数据校验
	public function dataValidation($id,$zn_title,$el_title){
		$data = $this->data;
		if($id){
			foreach($data as $value){
				if(intval($value['id']) != $id && ($value['zn_title'] == $zn_title || $value['el_title'] == $el_title)){
					return true;
				}
			}
		}else{
			foreach($data as $value){
				if($value['zn_title'] == $zn_title || $value['el_title'] == $el_title){
					return true;
				}
			}
		}
		return false;
	}

	// （删除时）从配置文件中删除某个数组
	public function delDataId($id){
		$data = $this->data;
		foreach($data as $key => $value){
			if($value['id'] == $id){
				unset($data[$key]);
			}else{
				$data[$key] = $value;
			}
		}
		return $data;
	}

	// （搜索时）从配置文件中搜索有该元素的数组
	public function search($keyword){
		$data = $this->data;
		foreach($data as $value){
			// 储存搜索关键字数组
			$titleArr[] = $value['zn_title'];
		}
		// 模糊搜索
		foreach($titleArr as $value2){
			// 查找字符串是否有该字符
			if(strpos($value2,$keyword) !== false){
				// 储存搜索结果数组
				$res[] = $this->getDataKeyWords($data,$value2);
			}
		}
		return $res;
	}

	// 获取某个搜索的数组
	public function getDataKeyWords($data,$keyword){
		$titleArr = [];
		foreach($data as $key => $value){
			if($value['zn_title'] == $keyword){
				$titleArr = $value;
			}else{
				// 查找字符串是否有该字符
				if(strpos($value,$keyword) !== false){
					$searchResults=  1;
				}
			}
		}
		return $titleArr;
	}


}