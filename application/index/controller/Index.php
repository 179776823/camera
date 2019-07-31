<?php
namespace app\index\controller;
use think\Controller;
use think\Exception;
use app\index\model\DeviceServer;
use think\facade\Session;
use think\facade\Request;

class Index extends Common
{
    public function index()
    {
        $post = request()->param();
        if(Request::isPost()){
            $server = DeviceServer::find();
            if($server){
                $server->address = $post['address'];
                $server->server_ip = $post['server_ip'];
                $server->server_port = $post['server_port'];
                $server->data_ip = $post['data_ip'];
                $server->data_port = $post['data_port'];
                $server->save();
            }else{
                $post['createTime'] = date('Y-m-d H:i:s',time());
                $server = DeviceServer::create($post);
            }
        }else{
            $server = DeviceServer::find();
        }

        return view('',['active'=>'index','server'=>$server,'title'=>'服务器配置']);
    }

}
