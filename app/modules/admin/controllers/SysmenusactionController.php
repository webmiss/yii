<?php
namespace app\modules\admin\controllers;

use app\library\Page;
use app\modules\admin\models\SysMenuAction;

class SysmenusactionController extends UserBase {

	/* 首页 */
	function actionIndex(){
		// 分页
		if(isset($_GET['search'])){
			$like = Page::where();
			// 生成搜索条件
			$where = '';
			foreach ($like['data'] as $key => $val){
				$where .= $key." LIKE '%".$val."%' AND ";
			}
			$where = rtrim($where,'AND ');
			$getUrl = $like['getUrl'];
			$this->view->params['getUrl'] = $like['search'];
		}else{
			$where = '';
			$getUrl = '';
		}
		// 数据
		self::setVar('List', Page::get([
			'model'=>'SysMenuAction',
			'where'=>$where,
			'getUrl'=>$getUrl
		]));
		// 获取菜单
		$this->view->params['Menus'] = $this->getMenus();
		// 视图
		$this->view->params['LoadJS'] = ['system/sys_menus_action.js'];
		$this->layout = 'main';
		return $this->view('/system/action/index');
	}

	/* 搜索 */
	function actionSearch(){
		return $this->view('/system/action/sea');
	}

	/* 添加 */
	function actionAdd(){
		return $this->view('/system/action/add');
	}
	function actionAdddata(){
		if(!$this->request->isPost) return false;
		// 采集数据
		$data = [
			'name'=>trim($this->request->post('name')),
			'perm'=>trim($this->request->post('perm')),
			'ico'=>trim($this->request->post('ico')),
		];
		// 实例化
		$model = new SysMenuAction();
		foreach($data as $key=>$val) $model->$key = $val;
		// 执行
		if($model->save()===true){
			return $this->setJsonContent(['state'=>'y','url'=>'sysmenusaction','msg'=>'添加成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'添加失败！']);
		}
	}

	/* 编辑 */
	function actionEdit(){
		// 视图
		self::setVar('edit',SysMenuAction::find()->where('id='.$_POST['id'])->one());
		return $this->view('/system/action/edit');
	}
	function actionEditdata(){
		if(!$this->request->isPost) return false;
		// 采集数据
		$data = [
			'name'=>trim($this->request->post('name')),
			'perm'=>trim($this->request->post('perm')),
			'ico'=>trim($this->request->post('ico')),
		];
		// 实例化
		$model = SysMenuAction::findOne($this->request->post('id'));
		foreach($data as $key=>$val) $model->$key = $val;
		// 执行
		if($model->update()!==false){
			return $this->setJsonContent(['state'=>'y','url'=>'sysmenusaction','msg'=>'编辑成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'编辑失败！']);
		}
	}
	/* 删除 */
	function actionDel(){
		return $this->view('/system/action/del');
	}
	function actionDeldata(){
		if(!$this->request->isPost) return false;
		// 获取ID
		$id = implode(',',json_decode($this->request->post('id')));
		// 执行
		if(SysMenuAction::deleteAll('id IN ('.$id.')')!==false){
			return $this->setJsonContent(['state'=>'y','url'=>'sysmenusaction','msg'=>'删除成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'删除失败！']);
		}
	}
}