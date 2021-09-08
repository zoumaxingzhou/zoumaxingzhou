<?php

namespace app\api\controller;

use app\api\controller\Api;
use fast\Random;
use think\Request;
use Monolog\Handler\DynamoDbHandler;
use think\Db;

/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *  广告轮播图
     */
    public function index()
    {
        $data = Db::name('carousel')->where('deletetime',null)->select();
        $request = Request::instance();
        $domain = $request->domain();
        foreach ($data as $a=>$v){
            $data[$a]['image']=$domain.$v['image'];
        }
        $this->success('请求成功',$data);
    }
    /**
     * 用户信息
     */
    public function user_data(){
        $user_id = $this->user_id;
        $user=Db::name('user')->where(['id'=>$user_id])->find();
        $this->success('获取用户信息成功！',$user);
    }
    /**
     * 接收手机号和密码，生成验证码+订单号+唯一邀请码(练习)
     */
    public function only(){
        $user=input();            //获取传递值
        $uid = $user['phone_id'];
        $verification_code=null;
        //生成6位验证码
        for($i=0;$i<6;$i++) {
            $verification_code.=rand(0,9);
        }

        $data=['phone'=>$user['phone'],'verification_code'=>$verification_code,'createtime'=>time()]; //存入的数据

        //若是扫描二维码来的   即$user['phone_id']!=0    把上级ID加上
        if ($uid!=0){
            $data+=['uid'=>$uid];
        }
        $id=Db::name('only')->insertGetId($data);
        //生成唯一邀请码   35进制   4位邀请码。   问题：连续的邀请码大部分相同
        static $source_string='E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        $num = $id;
        $code = '';
        while ( $num > 0) {
            $mod = $num % 35;
            $num = ($num - $mod) / 35;
            $code = $source_string[$mod].$code;
        }
        if(empty($code[3]))
            $code = str_pad($code,4,'0',STR_PAD_LEFT);  //唯一邀请码code
        //生成唯一订单号   时间戳 1000到9999随机数 (或者可以加账单ID)
        $order_number=time().rand(1000,9999);
        $data+=['invitation_code'=>$code,'order_number'=>$order_number,'id'=>$id];
        Db::name('only')->where(['id'=>$id,'deletetime'=>null])->update($data);
        $this->success('信息存储',$data);
    }

    /**
     * 返回excel表
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function excel(){
        $aaa=Db::name('only')->select();         //需要展示的信息
        $data='<table border="1">
            <tr>
            <td>ID</td>
            <td>手机号</td>
            <td>创建时间</td>
            <td>验证码</td>
            <td>唯一账单号</td>
            <td>唯一邀请码</td>
            <td>邀请者ID</td>
            </tr>';
        foreach ($aaa as $item=>$value){
            $data.='<tr><td>';
            $data.=$value['id'];
            $data.='</td><td>';
            $data.=$value['phone'];
            $data.='</td><td>';
            $data.=date('Y年m月d日',$value['createtime']) ;
            $data.='</td><td>';
            $data.=$value['verification_code'];
            $data.='</td><td>';
            $data.=$value['order_number'];
            $data.='</td><td>';
            $data.=$value['invitation_code'];
            $data.='</td><td>';
            $data.=$value['uid'];
            $data.='</td></tr>';
        }

        $this->success('excel表传递',$data);
    }

    /**
     * 地图上地址坐标
     */
    public function  world(){
        //获取经纬度的数据
        $data = Db::name('config')->where(['name'=>'world'])->find();
        $name = Db::name('config')->where(['name'=>'world_name'])->find();
        $address=Db::name('config')->where(['name'=>'world_address'])->find();
        $aaa=$data['value'];//经纬度的数值
        $var=explode(",",$aaa);
        $bbb=['name'=>$name['value'],$var,'address'=>$address['value']];
        $this->success('11212',$bbb);
    }

    /**
     * 用户订单列表
     */
    public function order_list(){
        $user_id = $this-> user_id;
        $order_list = Db::name('user_order')->where(['deletetime'=>null,'user_id'=>$user_id])->select();   //获取订单信息
        foreach ($order_list as $item=>$value){
            $order_information = Db::name('user_order_information')
                                ->alias('o')
                                ->join('fa_school s','s.id=o.school_id')                   //学校表
                                ->join('fa_grade g','g.id=o.grade_id')                    //年纪表
                                ->join('fa_course c','c.id=o.course_id')                  //课程表
                                ->join('fa_school_grade_course a',['a.school_id=o.school_id','a.grade_id=o.grade_id','a.course_id=o.course_id'])         //学校年级课程表
                                ->where(['o.order_id'=>$value['id'] ] )
                                ->field(['s.name as school_name','g.name as grade_name','c.name as course_name','a.price'])
                                ->select();
            $order_list[$item]+=['course_list'=>$order_information];
        }
        $this->success('sss',$order_list);
    }
    /**
     * 关于我们
     */
    public function contact_us(){
        $data=Db::name('config')->where('name','contact_us')->find();
        $this->success('成功',$data['value']);
    }
    /**
     * 学校列表
     */
    public function school_list(){
        $ret=input('area');   //获取的为区ID
        //判断区ID是否正确
        if (!isset($ret)||$ret<0){
            $this->error('地址错误！');
        }
        $data=Db::name('school')->where(['deletetime'=>null,'area'=>$ret])->select();
        $this->success('111',$data);
    }
    /**
     * 获取年级列表
     */
    public function grade_list(){
        $school_id  = input('school_id');
        if (!isset($school_id)||$school_id<0){
            $this->error('学校错误');
        }
        $data = Db::name('school_grade')
            ->alias('s')
            ->join('fa_grade g','g.id=s.grade_id')
            ->where(['s.deletetime'=>null,'s.school_id'=>$school_id])
            ->select();
        $this->success('获取年级列表成功',$data);
    }
    /**
     * 加载课程
     */
    public function course_list(){
        $school_id=input('school_id');
        $grade_id = input('grade_id');
        $school_name = Db::name('school')->where(['deletetime'=>null,'id'=>$school_id])->find();     //搜索学校信息
        $grade_name = Db::name('grade')->where(['deletetime'=>null,'id'=>$grade_id])->find();        //搜索年级信息
        $course_list = Db::name('school_grade_course')                                               //课程列表
                        ->alias('s')
                        ->join('fa_course c','c.id = s.course_id')
                        ->where(['s.deletetime'=>null,'s.school_id'=>$school_id,'s.grade_id'=>$grade_id])
                        ->field(['c.name,s.id,s.price'])
                        ->select();
        foreach ($course_list as $item=>$value){
            $course_list[$item]+=['state'=>false];
        }
        $data = ['school_name'=>$school_name['name'],'grade_name'=>$grade_name['name'],'list'=>$course_list];

        $this->success('获取课程列表成功',$data);
    }

}


