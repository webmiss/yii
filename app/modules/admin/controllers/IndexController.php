<?php
namespace app\modules\admin\controllers;

use app\library\Images;
use app\modules\admin\models\SysAdmin;

/**
* 后台：登录
*/
class IndexController extends ControllerBase{
	
	/* 首页 */
	function actionIndex(){
		// 视图
		return $this->view('/layouts/login');
	}

	/* 登录 */
	public function actionLogin(){
		// 是否有提交
		if(!$this->request->isPost) return false;
		// 用户信息
		$uname = trim($this->request->post('uname'));
		$password = md5($this->request->post('passwd'));
		$vcode = strtolower($this->request->post('vcode'));
		$remember = $this->request->post('remember');
		// 判断验证码
		if($vcode != $this->session->get('V_CODE')){
			return $this->setJsonContent(['status'=>'v','msg'=>'验证码错误！']);
		}else{
			$this->session->set('V_CODE',rand(1000,9999));
		}
		$data = SysAdmin::find()
		->where('(uname="'.$uname.'" or tel="'.$uname.'" or email="'.$uname.'") and password="'.$password.'"')
		->select('id,name,department,position,state,perm')->one();
		// 判断结果
		if(empty($data)) return $this->setJsonContent(['status'=>'n','msg'=>'用户名或密码错误！']);
		// 是否禁用
		if($data->state!='1') return $this->setJsonContent(['status'=>'n','msg'=>'该用户已被禁用！']);
		// 记住用户名
		if($remember=='true') setcookie("uname", $uname);
		// 保存SESSION
		$this->session->set('Admin',[
			'id'=>$data->id,
			'uname'=>$uname,
			'name'=>$data->name,
			'department'=>$data->department,
			'position'=>$data->position,
			'ltime'=>time()+1800,
			'login'=>TRUE,
		]);
		// 返回跳转URL
		return $this->setJsonContent(['status'=>'y','url'=>'welcome']);
	}

	/* 退出 */
	public function actionLogout(){
		$this->session->remove('Admin');
		$this->redirect($this->getUrl('index'));
	}

	/* 验证码 */
	function actionVcode(){
		Images::getCode(90,36);
	}
}
