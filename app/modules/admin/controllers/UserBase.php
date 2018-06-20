<?php
namespace app\modules\admin\controllers;

use app\modules\admin\models\SysAdmin;
use app\modules\admin\models\SysMenu;
use app\modules\admin\models\SysMenuAction;

class UserBase extends ControllerBase {

	static private $perm = '';
	static private $mid=[];
	static private $cid=[];
	
	/* 构造函数 */
	public function init(){
		parent::init();
		// 是否登录
		$admin = $this->session->get('Admin');
		if(!$admin || !$admin['login'] || $admin['ltime']<time()){
			return $this->redirect($this->getUrl('index'));
		}else{
			$_SESSION['Admin']['ltime'] = time()+1800;
		}
		// 菜单权限
		$perm = SysAdmin::find()->where('id='.$admin['id'])->select('perm')->one();
		$data = [];
		$arr = explode(' ',$perm->perm);
		foreach($arr as $val){
			$a = explode(':',$val);
			$data[$a[0]] = $a[1];
		}
		// 判断权限
		self::$mid = SysMenu::find()->where('url="'.$this->id.'"')->select('id,fid,title')->one();
		if(!isset($data[self::$mid->id])){
			return $this->redirect($this->getUrl('index/logout'));
		}
		// 赋值权限
		self::$perm = $data;
		// 用户信息
		$this->view->params['Uinfo'] = $admin;
	}

	/* 获取菜单 */
	function getMenus(){
		// CID
		self::$cid[] = self::$mid->id;
		$fids = self::getCid(self::$mid->fid);
		krsort(self::$cid);
		self::$cid = array_values(self::$cid);
		// 数据
		return [
			'Ctitle'=>self::$mid->title,
			'CID'=>self::$cid,
			// 获取菜单动作
			'action'=>self::actionMenus(self::$perm[self::$mid->id]),
			'Data'=>self::getMenu()
		];
	}
	// 递归菜单
	static private function getMenu($fid=0){
		$data=[];
		$M = SysMenu::find()->where('fid='.$fid)->select('id,fid,title,url,ico')->asArray()->all();
		foreach($M as $val){
			if(isset(self::$perm[$val['id']])){
				$val['menus'] = self::getMenu($val['id']);
				$data[] = (object)$val;
			}
		}
		return (object)$data;
	}
	// 动作菜单
	static private function actionMenus($perm=''){
		$data = array();
		// 全部动作菜单
		$aMenus = SysMenuAction::find()->select('name,ico,perm')->all();
		foreach($aMenus as $val){
			// 匹配权限值
			if(intval($perm)&intval($val->perm)){
				$data[] = array('name'=>$val->name,'ico'=>$val->ico);
			}
		}
		return $data;
	}
	// 递归CID
	static private function getCid($fid){
		if($fid!=0){
			$m = SysMenu::find()->where('id='.$fid)->select('id,fid')->one();
			self::$cid[] = $m->id;
			self::getCid($m->fid);
		}
	}
}