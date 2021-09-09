<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class Commodity extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'commodity';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







    public function scenes()
    {
        return $this->belongsTo('Scenes', 'scenes_ids', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
