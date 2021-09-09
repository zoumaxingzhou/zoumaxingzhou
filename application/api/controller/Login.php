<?php
namespace app\api\controller;
use app\common\controller\Api;
use fast\Random;
use think\Db;

class Login extends Api{
    protected $noNeedLogin = ['*'];

    /**
     * 用户登录    用code值获取用户openid值，返回前端用户ID
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function bind(){
        $params = input('post.');
        $time = time();

        if (!isset($params['code']) || empty($params['code'])){$this->error('缺少微信授权code');}
        if (!isset($params['avatar']) || empty($params['avatar'])){$this->error('缺少微信头像信息');}
        if (!isset($params['nickname']) || empty($params['nickname'])){$this->error('缺少微信昵称信息');}

        //获取openid
        $openid = $this->getOpenid($params['code']);
        if ($openid == 2){$this->error('获取openid失败');}
        //获取用户信息openid对应的user信息，
        $user_find = Db::name('user')->where('openid',$openid)->find();
        if ($user_find){        //如果查询到了用户，则查询用户是否被禁用，如果被禁用，则返回被禁用的信息，如果正常，则返回用户ID
            if ($user_find['status'] == "hidden"){     //用户被禁用了
                $this->redefine('您的账号已禁用');
            }
            $user_id = $user_find['id'];
        }else{                  //没有查询到用户，则把用户信息收集并且存入数据库
            $data=[
                'nickname'=>$params['nickname'] , //昵称
                'avatar'=>$params['avatar'] , //头像
                'createtime'=>$time , //创建时间
                'status'=> 'normal', //状态
                'openid'=> $openid, //微信小程序用户openid
            ];
            $user_id = Db::name('user')->insertGetId($data);
        }
        //
        if ($user_id){
            //创建新Token
            $token = Random::uuid();
            \app\common\library\Token::set($token, $user_id, 992592000);
            $tokenInfo = \app\common\library\Token::get($token);
            $data = [
                'user_id' => $user_id,
                'token' => $tokenInfo['token']
            ];
            $this->success('登录成功',$data);
        }else{
            $this->error('操作失败');
        }

    }


    /**
     *     微信获取openid
     * @param $code
     * @return int
     */
    public function getOpenid($code){
        $appid = config('sys.AppID');
        $secret = config('sys.AppSecret');
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
            if(strpos($response,'errcode') !== false){
            return 2;
        }
        $response = json_decode($response);
        $openid = $response->openid;
        return $openid;
    }
    /**
     * 省信息
     */
    public function get_province(){
        $data=Db::name('area')->where(['pid'=>0])->select();
        $this->success('省信息查询',$data);
    }
    /**
     * 市信息
     */
    public function get_city(){
        $id = input('provinceid');
        $data=Db::name('area')->where(['pid'=>$id])->select();
        $this->success('市信息查询',$data);
    }
    /**
     * 区信息
     */
    public function get_area(){
        $id = input('cityid');
        $data  = Db::name('area')->where(['pid'=>$id])->select();
        $this->success('区信息查询',$data);
    }
}