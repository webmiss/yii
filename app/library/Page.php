<?php

/**
* 分页类
*/

namespace app\library;

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class Page{
	/* Page */
	static public function get($config=array()){
		// 必须参数
		if(!isset($config['model']))die('请传入模型');
		// 命名空间
		$namespace = isset($config['namespace'])?$config['namespace']:'app\\modules\\'.MODULE.'\\models\\';
		$config['model'] = $namespace.$config['model'];
		// 默认值
		$field = isset($config['field'])?$config['field']:'*';
		$where = isset($config['where'])?$config['where']:'';
		$order = isset($config['order'])?$config['order']:'';
		$limit = isset($config['limit'])?$config['limit']:15;
		$getUrl = isset($config['getUrl'])?$config['getUrl']:'';
		// Page
		$page = !isset($_GET['page'])||empty(intval($_GET['page']))||$_GET['page']<0?1:intval($_GET['page']);
		$rows = $config['model']::find()->where($where)->count();
		$page_count = ceil($rows/$limit);
		$page = $page>=$page_count?$page_count:$page;
		// 数据
		$start=$limit*($page-1);
		$data = $config['model']::find()->select($field)->where($where)->orderBy($order)->limit($limit)->offset($start)->all();
		// 分页菜单
		$html = '';
		if($page==1 || $page==0){
			$html .= '<span>首页</span>';
			$html .= '<span>上一页</span>';
		}else{
			$html .= '<a href="'.Inc::BaseUrl(MODULE.'/'.CONTROLLER.'?search&page=1'.$getUrl).'">首页</a>';
			$html .= '<a href="'.Inc::BaseUrl(MODULE.'/'.CONTROLLER.'?search&page='.($page-1).$getUrl).'">上一页</a>';
		}
		if($page==$page_count){
			$html .= '<span>下一页</span>';
			$html .= '<span>末页</span>';
		}else{
			$html .= '<a href="'.Inc::BaseUrl(MODULE.'/'.CONTROLLER.'?search&page='.($page+1).$getUrl).'">下一页</a>';
			$html .= '<a href="'.Inc::BaseUrl(MODULE.'/'.CONTROLLER.'?search&page='.$page_count.$getUrl).'">末页</a>';
		}
		$html .= '第'.$page.'/'.$page_count.'页, 共'.$rows.'条';
		// 结果
		return array('data'=>$data,'page'=>$html);
	}
	// Page Where
	static public function where(){
		$getUrl = '';
		$like = $_GET;
		$page = isset($like['page'])?$like['page']:1;
		unset($like['r']);
		unset($like['page']);
		foreach($like as $key=>$val){if($val==''){unset($like[$key]);}else{$getUrl .= '&'.$key.'='.$val;}}
		unset($like['search']);
		return array('getUrl'=>$getUrl,'data'=>$like,'search'=>'?search&page='.$page.$getUrl);
	}
}