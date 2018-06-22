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

	/* 分页 */
	protected function page($config=[]){
		// 必须参数
		if(!isset($config['model']))die('请传入模型');
		// 命名空间
		$namespace = isset($config['namespace'])?$config['namespace']:'app\\modules\\'.$this->module->id.'\\models\\';
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
			$html .= '<a href="'.$this->getUrl($this->id.'?search&page=1'.$getUrl).'">首页</a>';
			$html .= '<a href="'.$this->getUrl($this->id.'?search&page='.($page-1).$getUrl).'">上一页</a>';
		}
		if($page==$page_count){
			$html .= '<span>下一页</span>';
			$html .= '<span>末页</span>';
		}else{
			$html .= '<a href="'.$this->getUrl($this->id.'?search&page='.($page+1).$getUrl).'">下一页</a>';
			$html .= '<a href="'.$this->getUrl($this->id.'?search&page='.$page_count.$getUrl).'">末页</a>';
		}
		$html .= '第'.$page.'/'.$page_count.'页, 共'.$rows.'条';
		// 结果
		return array('data'=>$data,'page'=>$html);
	}
	// 分页条件
	protected function pageWhere(){
		$getUrl = '';
		$like = $_GET;
		$page = isset($like['page'])?$like['page']:1;
		unset($like['r']);
		unset($like['page']);
		// 条件字符串
		foreach($like as $key=>$val){
			if($val==''){
				unset($like[$key]);
			}else{
				$getUrl .= '&'.$key.'='.$val;
			}
		}
		unset($like['search']);
		// 传递搜索条件
		$this->view->params['getUrl'] = '?search&page='.$page.$getUrl;
		// 返回数据
		return array('getUrl'=>$getUrl,'data'=>$like);
	}

}