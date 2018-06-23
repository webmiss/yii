<?php

namespace app\modules\admin\controllers;

class WebController extends UserBase {
	// 首页
	function actionIndex(){
		// 获取菜单
		$this->view->params['Menus'] = $this->getMenus();
		// 视图
		$this->layout = 'main';
		return $this->view('/web/index');
	}
}