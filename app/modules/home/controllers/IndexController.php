<?php
namespace app\modules\home\controllers;

/**
* 网站：首页
*/
class IndexController extends ControllerBase {

	function actionIndex() {
		// 视图
		$this->layout = 'main';
		return $this->render('/index/index');
	}

}
