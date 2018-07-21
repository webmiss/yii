<?php
namespace app\modules\admin\controllers;


use yii\web\Controller;
use yii\web\Request;

/* 公共控制器 */
class ControllerBase extends Controller{

	private static $var = [];
	// 设置
	protected $request;
	protected $response;
	protected $session;
	public $enableCsrfValidation=false;

	/* 构造函数 */
	function init() {
		// 常量
		define('MODULE',$this->module->id);
		define('CONTROLLER',$this->id);
		define('ACTION',$this->action);
		// 请求、响应、session
		$this->request = \Yii::$app->request;
		$this->response = \Yii::$app->response;
		$this->session = \Yii::$app->session;
		// 搜索条件
		$this->view->params['getUrl']='';
	}

	/* 获取网址 */
	function getUrl($url=''){
		return $this->request->hostInfo.'/'.$this->module->id.'/'.$url;
	}

	/* 设置参数 */
	static function setVar($name,$value=''){
		self::$var[$name] = $value;
	}

	/* 获取参数 */
	static function getVar($name){
		return self::$var[$name];
	}

	/* 视图 */
	protected function view($file=''){
		return $this->render($file,self::$var);
	}

	/* 返回JSON */
	protected function setJsonContent($data=[]){
		return \Yii::createObject([
			'class' => 'yii\web\Response',
			'format' => \yii\web\Response::FORMAT_JSON,
			'data' => $data,
		]);
	}

}