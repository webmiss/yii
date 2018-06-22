<?php
namespace app\modules\admin\controllers;

use app\modules\admin\models\SysMenu;
use app\modules\admin\models\SysMenuAction;

class SysmenusController extends UserBase {

	/* 首页 */
	function actionIndex(){
		// 分页
		if(isset($_GET['search'])){
			$like = $this->pageWhere();
			// 生成搜索条件
			$where = '';
			foreach ($like['data'] as $key => $val){
				$where .= $key." LIKE '%".$val."%' AND ";
			}
			$where = rtrim($where,'AND ');
			$getUrl = $like['getUrl'];
		}else{
			$where = '';
			$getUrl = '';
		}
		// 数据
		self::setVar('List',$this->page([
			'model'=>'SysMenu',
			'where'=>$where,
			'getUrl'=>$getUrl
		]));
		// 获取菜单
		$this->view->params['Menus'] = $this->getMenus();
		// 视图
		$this->view->params['LoadJS'] = ['system/sys_menus.js'];
		$this->layout = 'main';
		return $this->view('/system/menus/index');
	}

	/* 搜索 */
	function actionSearch(){
		return $this->view('/system/menus/sea');
	}

	/* 添加 */
	function actionAdd(){
		// 所有权限
		$this->setVar('perm',SysMenuAction::find()->select(['name','perm'])->all());
		return $this->view('/system/menus/add');
	}
	function actionAdddata(){
		if(!$this->request->isPost) return false;
		// 采集数据
		$data = [
			'fid'=>trim($this->request->post('fid')),
			'title'=>trim($this->request->post('title')),
			'url'=>trim($this->request->post('url')),
			'perm'=>trim($this->request->post('perm')),
			'ico'=>trim($this->request->post('ico')),
			'sort'=>trim($this->request->post('sort')),
			'remark'=>trim($this->request->post('remark')),
			'ctime'=>date('YmdHis'),
		];
		// 实例化
		$model = new SysMenu();
		foreach($data as $key=>$val) $model->$key = $val;
		// 执行
		if($model->save()===false){
			return $this->setJsonContent(['state'=>'n','msg'=>'添加失败！']);
		}else{
			return $this->setJsonContent(['state'=>'y','url'=>'sysmenus','msg'=>'添加成功！']);
		}
	}

	/* 编辑 */
	function actionEdit(){
		// 所有权限
		self::setVar('perm',SysMenuAction::find()->select('name,perm')->all());
		// 视图
		self::setVar('edit',SysMenu::find()->where('id='.$_POST['id'])->one());
		return $this->view('/system/menus/edit');
	}
	function actionEditdata(){
		if(!$this->request->isPost) return false;
		// 采集数据
		$data = [
			'fid'=>trim($this->request->post('fid')),
			'title'=>trim($this->request->post('title')),
			'url'=>trim($this->request->post('url')),
			'perm'=>trim($this->request->post('perm')),
			'ico'=>trim($this->request->post('ico')),
			'sort'=>trim($this->request->post('sort')),
			'remark'=>trim($this->request->post('remark')),
			'ctime'=>date('YmdHis'),
		];
		// 实例化
		$model = SysMenu::findOne($this->request->post('id'));
		foreach($data as $key=>$val) $model->$key = $val;
		// 执行
		if($model->save()===false){
			return $this->setJsonContent(['state'=>'n','msg'=>'编辑失败！']);
		}else{
			return $this->setJsonContent(['state'=>'y','url'=>'sysmenus','msg'=>'编辑成功！']);
		}
	}

	/* 删除 */
	function actionDel(){
		return $this->view('/system/menus/del');
	}
	function actionDeldata(){
		if(!$this->request->isPost) return false;
		// 获取ID
		$id = implode(',',json_decode($_POST['id']));
		// 执行
		if( SysMenu::deleteAll('id IN ('.$id.')')===false){
			return $this->setJsonContent(['state'=>'n','msg'=>'删除失败！']);
		}else{
			return $this->setJsonContent(['state'=>'y','url'=>'sysmenus','msg'=>'删除成功！']);
		}
	}

	/* 联动菜单数据 */
	function actionGetmenu(){
		if(!$this->request->isPost) return false;
		// 实例化
		$menus = SysMenu::find()->where('fid='. $_POST['fid'])->select('id,title')->all();
		$data = [];
		foreach($menus as $val){
			$data[] = [$val->id,$val->title];
		}
		// 返回数据
		return json_encode($data);
	}

}