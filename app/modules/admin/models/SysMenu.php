<?php

namespace app\modules\admin\models;

use yii\db\ActiveRecord;

class SysMenu extends ActiveRecord{
	// 数据表
	static public function tableName() {
		return 'sys_menus';
	}
}
