<?php
namespace app\index\controller;
use app\agbox\model\FaceImage;
use app\index\model\FaceEvent;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Camera as Cameras;
use think\facade\Session; 

class Camera extends Common
{
    public function index()
    {
        $post = request()->param();
        // 查询摄像机设备
        $camera_list = Db::table('camera')->select();
        if($camera_list){
        	foreach ($camera_list as $key => $camera) {
                $camera_list[$key]['gisType'] = $camera['gisType'] ? $camera['gisType'] - 1 : 0;
	        }
        }
        $image_list = Db::table('faceImage')->where('sub',11)->order('id','desc')->limit(200)->select();

        $faceImage= array();
        if($image_list){
            foreach ($image_list as $key=>$val){
                $image_list[$key]['eventPicUrl'] = 'http://'.request()->ip().$val['eventPicUrl'];
            }
            for($i=0;$i<ceil(count($image_list));$i++)
            {
                $faceImage[] = array_slice($image_list, $i * 2 ,2);
            }
        }
        $data = ['active'=>'camera',
            'camera_list'=>$camera_list,
            'faceImage'=>$faceImage,
            'title'=>'摄像机管理'
            ];
        return view('',$data);
    }

    public function faceImageList()
    {
        $post = request()->param();
        $begin_time = date('Y-m-d H:i:s',time()-5);
        $new_total = Db::table('faceImage')->whereBetweenTime('createTime',$begin_time,date('Y-m-d H:i:s',time()))->count();
        $faceImage= array();
        if($new_total || $post['type']==0){
            $image_list = Db::table('faceImage')->where('sub',11)->order('id','desc')->limit(100)->select();
            $server = Db::table('deviceserver')->find();
            if($image_list){
                foreach ($image_list as $key=>$val){
                    $image_list[$key]['eventPicUrl'] = 'http://'.$server['server_ip'].$val['eventPicUrl'];
                }
                for($i=0;$i<ceil(count($image_list));$i++)
                {
                    $faceImage[] = array_slice($image_list, $i * 2 ,2);
                }
            }
        }

        $result['faceImage'] = $faceImage;
        return json($result)->header(['Content-Length'=>strlen(json_encode($result))]);
    }

    public function cameraone(){
        $post = request()->param();
        if($post['id']){
            $camera = Db::table('camera')->where('id',$post['id'])->find();
            if(!$camera){
                $result = array('status'=>0,'msg'=>'设备不存在');
                return json($result);
            }
            if($camera){           
                $camera['gisType'] = $camera['gisType'] ? $camera['gisType'] - 1 : 0;
            }

            $result['status']=1;
            $result['info'] = $camera;
        }else{
            $result = array('status'=>0,'msg'=>'Fail');
        }
        return json($result)->header(['Content-Length'=>strlen(json_encode($result))]);
    }

    /**
     * 添加设备
     * direction 安装的方向 0是入口 1是出口
     */
    public function addCamera(){

        $post = request()->param();
        if(!$post['deviceCode']){
            return json(array('code'=>500,'message'=>'failed'));
        }
        $camera = Cameras::get(['deviceCode'=>$post['deviceCode']]);
        if($camera){
            return json(array('code'=>500,'message'=>'该摄像机已存在'));
        }
        try {
            // 添加设备
            $camera = new Cameras();
            $camera->deviceCode = $post['deviceCode'];
            $camera->IP = isset($post['IP']) ? $post['IP'] : 0;
            $camera->port = isset($post['port']) ? $post['port'] : 0;
            $camera->lat = isset($post['lat']) ? $post['lat'] : 0;
            $camera->lon = isset($post['lon']) ? $post['lon'] : 0;
            $camera->alt = isset($post['alt']) ? $post['alt'] : 0;
            $camera->address = isset($post['address']) ? $post['address'] : '';
            $camera->direction = isset($post['direction']) ? $post['direction'] : 0;
            $camera->gisType = isset($post['gisType']) ? $post['gisType'] + 1 : 0;
            $camera->createTime = date('Y-m-d H:i:s');
            $camera->save();

            return json([
                'code'=>200,
                'message'=>'success',
                'result' => [
                    'deviceId'=>$camera->id
                ]
            ]);
        }catch (Exception $exception){
            return json([
                'code'=>500,
                'message'=>$exception->getMessage()
            ]);
        }
    }

    /**
     * 更新设备
     * direction 安装的方向 0是入口 1是出口
     */
    public function updateCamera(){
        $post = request()->param();
        if(!$post['deviceCode'] || !$post['id']){
            return json(array('code'=>500,'message'=>'param failed'));
        }
        try {
            $camera = Cameras::get(['id'=>$post['id']]);
            if(!$camera){
                return json(array('code'=>500,'message'=>'该摄像机不存在,无法更新'));
            }
            $camera->deviceCode = $post['deviceCode'];
            $camera->IP = isset($post['IP']) ? $post['IP'] : 0;
            $camera->port = isset($post['port']) ? $post['port'] : 0;
            $camera->lat = isset($post['lat']) ? $post['lat'] : 0;
            $camera->lon = isset($post['lon']) ? $post['lon'] : 0;
            $camera->alt = isset($post['alt']) ? $post['alt'] : 0;
            $camera->address = isset($post['address']) ? $post['address'] : '';
            $camera->direction = isset($post['direction']) ? $post['direction'] : 0;
            $camera->gisType = isset($post['gisType']) ? $post['gisType'] + 1 : 0;
            $camera->save();

            return json([
                'code'=>200,
                'message'=>'success',
            ]);
        }catch (Exception $exception){
            return json([
                'code'=>500,
                'message'=>$exception->getMessage()
            ]);
        }
    }

    /**
     * 删除设备
     * direction 安装的方向 0是入口 1是出口
     */
    public function delCamera(){
        $post = request()->param();
        if(!isset($post['id'])){
            return json(array('code'=>500,'message'=>'param failed'));
        }
        $camera = Cameras::get(['id'=>$post['id']]);
        if(!$camera){
            return json(array('code'=>500,'message'=>'该摄像机不存在,无法删除'));
        }
        try {
            $camera->delete();
            $faceEvent = FaceEvent::where('deviceId',$post['id'])->select();
            if($faceEvent){
                foreach ($faceEvent as $k=>$v){
                    $faceImages = FaceImage::where('faceEventId',$v['id'])->select();
                    FaceEvent::where('id',$v['id'])->delete();
                    foreach ($faceImages as $key=>$val){
                        $imageUrl = __PUBLIC__.$val['eventPicUrl'];
                        if(file_exists($imageUrl)){
                            unlink($imageUrl);
                        }else{
                            continue;
                        }
                        FaceImage::where('id',$val->id)->delete();
                    }
                }
            }
            return json([
                'code'=>200,
                'message'=>'success',
            ]);
        }catch (Exception $exception){
            return json([
                'code'=>500,
                'message'=>$exception->getMessage()
            ]);
        }
    }

}
