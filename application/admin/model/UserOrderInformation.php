<?php

namespace app\admin\model;

use think\Model;


class UserOrderInformation extends Model
{

    

    

    // 表名
    protected $name = 'user_order_information';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];
    

    







    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function grade()
    {
        return $this->belongsTo('Grade', 'grade_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
