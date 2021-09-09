<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\exception\PDOException;
use think\exception\ValidateException;

/**
 * 设备管理
 *
 * @icon fa fa-circle-o
 */
class Equipment extends Backend
{
    
    /**
     * Equipment模型对象
     * @var \app\admin\model\Equipment
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Equipment;
        $this->view->assign("typeList", $this->model->getTypeList());
        $this->view->assign("bindList", $this->model->getBindList());
        $this->view->assign("statusList", $this->model->getStatusList());
    }

    /**
     * 解绑操作
     * 将user_id、merchant_id、bind_time、name、start_time、consumables、cumulative_times设为空
     * 将bind设为2
     */
    public function jiebang($ids){
        $data = ['user_id'=>null,'merchant_id'=>null,'bind_time'=>null,'name'=>null,'start_time'=>null,'consumables'=>null,'cumulative_times'=>null,'bind'=>2];
        $row = $this->model->allowField(true)->save($data,['id'=>$ids]);;
        $this->success('111',null,$row);
    }

    public function import()
    {
        parent::import();
    }
    /**
     * 查看
     */
    public function check($ids = null)
    {
        $row = $this->model->get($ids);
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
