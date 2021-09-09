<?php

namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;

class SchoolGradeCourse extends Model
{

    use SoftDelete;

    

    // 表名
    protected $name = 'school_grade_course';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = 'deletetime';

    // 追加属性
    protected $append = [

    ];
    

    







    public function school()
    {
        return $this->belongsTo('School', 'school_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function grade()
    {
        return $this->belongsTo('Grade', 'grade_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }


    public function course()
    {
        return $this->belongsTo('Course', 'course_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }
}
