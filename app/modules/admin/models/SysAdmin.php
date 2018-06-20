<?php

namespace app\modules\admin\models;

use yii\db\ActiveRecord;

class SysAdmin extends ActiveRecord{
	// 数据表
	static public function tableName() {
		return 'sys_admin';
	}
}
