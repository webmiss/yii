<?php
namespace app\modules\admin\controllers;

/**
* 后台：登录
*/
class WelcomeController extends UserBase {
	/* 首页 */
	function actionIndex(){
		// 跳转用户首页
		$this->redirect($this->getUrl('desktop'));
	}
}