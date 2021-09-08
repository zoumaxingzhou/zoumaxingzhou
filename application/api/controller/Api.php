<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/5/7
 * Time: 10:05
 */

namespace app\api\controller;

use think\Db;
use think\Controller;
use think\Session;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class Api extends Controller
{
    protected $noNeedLogin = ['*'];
    protected $user_id = false;
    protected $page = 1;
    protected $pagesize = 10;

    public function _initialize()
    {
        $page        = input('page') ? input('page') : 1; //默认第一页
        $pagesize     = input('pagesize') ? input('pagesize') : 10; //默认取12条
        $this->page = $page;
        $this->pagesize = $pagesize;


        //获取当前方法名
        $action = $this->request->action();
        $NoLogin = $this->noNeedLogin;

        //判断登录
        $token = input('token');
        if (isset($token) && !empty($token) && $token != false && $token != 'false'){
            //如果存在登录信息（token）
            $tokenInfo = \app\common\library\Token::get($token);
            $user_id = $tokenInfo['user_id'];
            $check = \app\common\library\Token::check($token, $user_id);
            if (!$check){$this->redefine('用户信息错误,请重新登录');}
            $this->user_id = $user_id;      //当前登录用户

            $user = Db::name('user')->where('id',$user_id)->find();
            if (!$user || $user['status'] == "hidden"){     //用户被禁用了
                $this->redefine('您的账号已禁用，请联系平台客服');
            }
        }else{
            //如果没登录信息（token）
            //判断当前方法是否需要登录验证
            if ( (isset($NoLogin[0]) && $NoLogin[0] == '*') || in_array($action,$NoLogin) ) {
//                return;     //不需要验证登录
            }else{
                $this->redefine('请先登录');        //接口方式post请求的，返回重新登录的code=-1
            }

        }
    }


    /**
     * 操作成功返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为1
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function success($msg = '', $data = null, $code = 1, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 操作失败返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function error($msg = '', $data = null, $code = 0, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }
    /**
     * 未登录返回的数据
     * @param string $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型
     * @param array  $header 发送的 Header 信息
     */
    protected function redefine($msg = '', $data = null, $code = -1, $type = null, array $header = [])
    {
        $this->result($msg, $data, $code, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed  $msg    提示信息
     * @param mixed  $data   要返回的数据
     * @param int    $code   错误码，默认为0
     * @param string $type   输出类型，支持json/xml/jsonp
     * @param array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($msg, $data = null, $code = 0, $type = null, array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => Request::instance()->server('REQUEST_TIME'),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        $type = $type ? $type : ($this->request->param(config('var_jsonp_handler')) ? 'jsonp' : 'json');

        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        } else {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }

}