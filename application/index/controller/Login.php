<?php
/**
 * Created by PhpStorm.
 * User: ye21st
 * Email: ye21st@gmail.com
 * Date: 2018/1/19
 * Time: 11:52
 */

namespace app\index\controller;

use app\index\model\User as UserModel;
use think\captcha\Captcha;
use think\Controller;
use think\facade\Request;
use think\facade\Session;
use think\Validate;

/**
 * 登录控制器
 * Class Login
 * @package app\index\controller
 */
class Login extends Controller
{
    /**
     * 登录操作
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        //  判断请求是否是 POST 请求
        if ( Request::isPost() ){
            //  获取用户登录时候的用户名、密码、验证码
            $username = $this -> request -> param('username');
            $password = $this -> request -> param('password');

            $user = new UserModel();
            $result = $user -> login($username,$password);

            /**
             * 通过获得的条件去进行不同的操作
             */
            switch ($result){
                case ResultCode::$LOGIN_SUCCESS:
                    // $this -> success('用户登录成功！',url('index/index/index'));
                    $this -> redirect(url('index/index/index'));
                    break;

                case ResultCode::$USER_DOES_NOT_EXIST:
                    $this->error('用户名不存在！');
                    break;

                case ResultCode::$PASSWORD_ERROR:
                    $this->error('密码错误，请确认后再次输入！');
                    break;
            }
        }
        return view('',['active'=>'login','title'=>'后台登录界面']);
    }

    /**
     * 编辑账号
     */
    public function edit(){
        $post = $this->request->param();
        if(empty($post['username']) || empty($post['password'])){
            return json(['status'=>0,'message'=>'账户名或密码不能为空']);
        }
        if(empty($post['newpwd'])){
            return json(['status'=>0,'message'=>'新密码不能为空']);
        }
        $result = UserModel::searchUser($post['userid'],$post['password']);
        /**
         * 通过获得的条件去进行不同的操作
         */
        switch ($result){
            case ResultCode::$LOGIN_SUCCESS:
                $msg = [
                    'username.chsAlphaNum' => '账号必须是汉字、字母和数字',
                    'newpwd.alphaNum'   => '密码必须是字母和数字',
                ];
                $validate = Validate::make([
                    'username'  => 'chsAlphaNum',
                    'newpwd'=>'alphaNum'
                ],$msg);

                $data = [
                    'username'  => $post['username'],
                    'newpwd'=>$post['newpwd']
                ];
                if (!$validate->check($data)) {
                    return json(['status'=>0,'message'=>$validate->getError()]);
                }
                $salt = makeRandomNum(4);
                $user = UserModel::where('id','eq',$post['userid'])->find();
                $user->username = $post['username'];
                $user->password = md5($post['newpwd'].$salt);
                $user->salt = $salt;
                $user->save();
                return json(['status'=>1,'message'=>'success']);
                break;
            case ResultCode::$USER_DOES_NOT_EXIST:
                return json(['status'=>0,'message'=>'用户名不存在！']);
                break;
            case ResultCode::$PASSWORD_ERROR:
                return json(['status'=>0,'message'=>'旧密码错误，请确认后再次输入！']);
                break;
        }
    }

    
    /**
     * 安全退出
     */
    public function logout(){
        Session::pull('id');
        Session::pull('name');
        $this->redirect('index/login/index');
    }
}