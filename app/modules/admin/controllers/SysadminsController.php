<?php

namespace app\modules\admin\controllers;

use app\library\Safety;
use app\modules\admin\models\SysAdmin;
use app\modules\admin\models\SysMenu;
use app\modules\admin\models\SysMenuAction;

class SysadminsController extends UserBase {

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
			'model'=>'SysAdmin',
			'where'=>$where,
			'getUrl'=>$getUrl,
			'order'=>'id desc',
		]));
		// 获取菜单
		$this->view->params['Menus'] = $this->getMenus();
		// 视图
		$this->view->params['LoadJS'] = ['system/sys_admin.js'];
		$this->layout = 'main';
		return $this->view('/system/admin/index');
	}

	/* 搜索 */
	function actionSearch(){
		return $this->view('/system/admin/sea');
	}

	/* 添加 */
	function actionAdd(){
		return $this->view('/system/admin/add');
	}
	function actionAdddata(){
		if(!$this->request->isPost) return false;
		// 采集数据
		$data = [
			'uname'=>trim($this->request->post('uname')),
			'password'=>md5($this->request->post('passwd')),
			'email'=>trim($this->request->post('email')),
			'tel'=>trim($this->request->post('tel')),
			'name'=>trim($this->request->post('name')),
			'department'=>trim($this->request->post('department')),
			'position'=>trim($this->request->post('position')),
			'rtime'=>date('YmdHis'),
		];
		// 验证
		$res = Safety::isRight('uname',$data['uname']);
		if($res!==true) return json_encode(['state'=>'n','msg'=>$res]);
		$res = Safety::isRight('passwd',$_POST['passwd']);
		if($res!==true) return json_encode(['state'=>'n','msg'=>$res]);
		$res = Safety::isRight('email',$data['email']);
		if($res!==true) return json_encode(['state'=>'n','msg'=>$res]);
		$res = Safety::isRight('tel',$data['tel']);
		if($res!==true) return json_encode(['state'=>'n','msg'=>$res]);
		// 是否存在用户
		$isNull =SysAdmin::find()->where('uname="'.$data['uname'].'" OR tel="'.$data['tel'].'" OR email="'.$data['email'].'"')->select('id')->one();
		if($isNull) return json_encode(['state'=>'n','msg'=>'该用户已经存在！']);
		// 实例化
		$model = new SysAdmin();
		foreach($data as $key=>$val) $model->$key = $val;
		// 执行
		if($model->save()===true){
			return $this->setJsonContent(['state'=>'y','url'=>'sysadmins','msg'=>'添加成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'添加失败！']);
		}
	}

	/* 编辑 */
	function actionEdit(){
		// 视图
		self::setVar('edit',SysAdmin::find()->where('id='.$_POST['id'])->one());
		return $this->view('/system/admin/edit');
	}
	function actionEditdata(){
		if(!$this->request->isPost) return false;
		// 采集数据
		$data = [
			'name'=>trim($this->request->post('name')),
			'department'=>trim($this->request->post('department')),
			'position'=>trim($this->request->post('position')),
		];
		// 是否修改密码
		if(!empty($this->request->post('passwd'))){
			$res = Safety::isRight('passwd',$this->request->post('passwd'));
			if($res!==true) return json_encode(['state'=>'n','msg'=>$res]);
			// 原密码判断
			$isNull =SysAdmin::find()->where('id="'.$this->request->post('id').'" AND password="'.md5($this->request->post('passwd1')).'"')->select('id')->one();
			if($isNull){
				$data['password'] = md5($this->request->post('passwd'));
			}else{
				return json_encode(['state'=>'n','msg'=>'原密码错误！']);
			}
		}
		// 实例化
		$model = SysAdmin::findOne($this->request->post('id'));
		foreach($data as $key=>$val) $model->$key = $val;
		// 执行
		if($model->update()!==false){
			return $this->setJsonContent(['state'=>'y','url'=>'sysadmins','msg'=>'编辑成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'编辑失败！']);
		}
	}

	/* 删除 */
	function actionDel(){
		return $this->view('/system/admin/del');
	}
	function actionDeldata(){
		if(!$this->request->isPost) return false;
		// 获取ID
		$id = implode(',',json_decode($this->request->post('id')));
		// 执行
		if(SysAdmin::deleteAll('id IN ('.$id.')')!==false){
			return $this->setJsonContent(['state'=>'y','url'=>'sysadmins','msg'=>'删除成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'删除失败！']);
		}
	}

	/* 审核 */
	function actionAudit(){
		return $this->view('/system/admin/audit');
	}
	function actionAuditdata(){
		if(!$this->request->isPost) return false;
		// 获取ID
		$id = implode(',',json_decode($this->request->post('id')));
		// 执行
		if(SysAdmin::updateAll(['state'=>$this->request->post('state')],'id IN ('.$id.')')!==false){
			return $this->setJsonContent(['state'=>'y','url'=>'sysadmins','msg'=>'审核成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'审核失败！']);
		}
	}

	/* 是否存在 */
	function actionIsuname(){
		// 是否提交
		if(!isset($_POST['name']) || !isset($_POST['val'])) return false;
		// 条件
		$where = '';
		if($_POST['name']=='uname'){
			$where = 'uname="'.trim($_POST['val']).'"';
		}elseif($_POST['name']=='tel'){
			$where = 'tel="'.trim($_POST['val']).'"';
		}elseif($_POST['name']=='email'){
			$where = 'email="'.trim($_POST['val']).'"';
		}
		// 查询
		if($where){
			$data = SysAdmin::find()->where($where)->select('id')->one();
			return $data?json_encode(['state'=>'y']):json_encode(['state'=>'n']);
		}
	}

	/* 权限 */
	function actionPerm(){
		// 拆分权限
		$permArr=[];
		$arr = explode(' ',$this->request->post('perm'));
		foreach($arr as $val){
			$a=explode(':',$val);
			$permArr[$a[0]]=$a[1];
		}
		self::setVar('permArr',$permArr);
		self::setVar('Perm',SysMenuAction::find()->select('name,perm')->all());
		self::setVar('Menus',$this->Menus());
		return $this->view('/system/admin/perm');
	}
	function actionPermdata(){
		if(!$this->request->isPost) return false;
		// 实例化
		$model = SysAdmin::findOne($this->request->post('id'));
		$model->perm = trim($this->request->post('perm'));
		// 执行
		if($model->update()!==false){
			return $this->setJsonContent(['state'=>'y','url'=>'sysadmins','msg'=>'权限编辑成功！']);
		}else{
			return $this->setJsonContent(['state'=>'n','msg'=>'权限编辑失败！']);
		}
	}
	// 递归全部菜单
	private function Menus($fid='0'){
		$data=[];
		$M = SysMenu::find()->where('fid='.$fid)->select('id,title,perm')->asArray()->all();
		foreach($M as $val){
			$val['menus'] = $this->Menus($val['id']);
			$data[] = (object)$val;
		}
		return (object)$data;
	}

}