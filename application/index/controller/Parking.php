<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model\Device;
use app\index\model\ParkingEvent;
use app\index\model\HeartbeatParking;
use think\facade\Session; 

class Parking extends Common
{
    public function index()
    {
        $post = request()->param();
        // 查询抓拍车辆设备
        $park_list = Device::where('deviceType',5)->select();
        $this->assign('active','parking');
        $this->assign('park_list',$park_list);

        $this->assign('title', 'DATA CENTER-vigia');
        return $this->fetch();
    }
   
    
    public function parking_Event()
    {
        $post = request()->param();
        // 查询所有服务器，分页
        $keyword = '';
        if(array_key_exists('keyword', $post)){
            $keyword = $post['keyword'];
        }
        $event_list = ParkingEvent::where('deviceId|deviceCode','like','%'.$keyword.'%')->select();
        $eventMap = ParkingEvent::getEventTypeMap();
        if($event_list){
            foreach ($event_list as &$l) {
                $l['picUrl'] = isset($l['eventPicUrl']) ? $l['eventPicUrl'] : '';
                if($eventMap){
                    if($l['eventType']){
                        $l['eventType'] = isset($eventMap[$l['eventType']]) ?  $eventMap[$l['eventType']] : $l['eventType'];
                    }elseif($l['eventCode']){
                        $l['eventType'] = isset($eventMap[$l['eventCode']]) ?  $eventMap[$l['eventCode']] : $l['eventCode'];
                    }
                }
            } 
        }
        $device = Device::where('deviceCode',$keyword)->where('deviceType',5)->find();
        if($device->deviceId){
            $heart = HeartbeatParking::where('deviceId',$device->deviceId)
                ->order('id','desc')
                ->limit(1)
                ->find();
        }
        $row = [];
        if($device->parking){
            $row = [
                    'deviceId' => $device->deviceId,
                    'deviceCode' => $device->deviceCode,
                    'deviceNumber' => $device->deviceNumber,
                    'address'   => $device->parking->address,
                    'parkName' => $device->parking->parkName,
                    'parkNum' => $device->parking->parkNum,
                    'note'  =>  $device->note,
                    'lat'   => $device->lat ? $device->lat : $device->parking->lat,
                    'lon'   =>  $device->lon ? $device->lon : $device->parking->lon,
                    'alt'   =>  $device->alt ? $device->alt : $device->parking->alt,
                    'gisType'   =>  $device->gisType ? $device->gisType - 1 : 0,
                    'isCloudDevice' => $device->isCloudDevice,
                    'isHidden' => $device->isHidden,
                    'floor' => $device->floor ? $device->floor : $device->parking->floor,
                    'triggerTime' => $heart ? $heart->heartTime:''
                ];
        }else{
            $row = [
                    'deviceId' => $device->deviceId,
                    'deviceCode' => $device->deviceCode,
                    'deviceNumber' => $device->deviceNumber,
                    'address'   => '',
                    'parkName' => '',
                    'parkNum' => '',
                    'note'  =>  $device->note,
                    'lat'   => $device->lat,
                    'lon'   =>  $device->lon,
                    'alt'   =>  $device->alt,
                    'gisType'   =>  $device->gisType ? $device->gisType - 1 : 0,
                    'isCloudDevice' => $device->isCloudDevice,
                    'isHidden' => $device->isHidden,
                    'floor' => $device->floor,
                    'triggerTime' => $heart ? $heart->heartTime:''
                ];
        }
        $result['list'] = $event_list;
        $result['info'] = $row;
        return json($result);     
       
    }

    public function parkingOne(){
        $post = request()->param();
        $deviceId = $post['deviceId'];
        $device = Device::where('deviceId',$deviceId)->where('deviceType',5)->find();
        if(!$device){
            $result = array('status'=>0,'msg'=>'设备不存在');
        }
        $row = [];
        if($device->parking){
            $row = [
                    'deviceId' => $device->deviceId,
                    'deviceCode' => $device->deviceCode,
                    'deviceNumber' => $device->deviceNumber,
                    'address'   => $device->parking->address,
                    'parkName' => $device->parking->parkName,
                    'parkNum' => $device->parking->parkNum,
                    'note'  =>  $device->note,
                    'lat'   => $device->lat ? $device->lat : $device->parking->lat,
                    'lon'   =>  $device->lon ? $device->lon : $device->parking->lon,
                    'alt'   =>  $device->alt ? $device->alt : $device->parking->alt,
                    'gisType'   =>  $device->gisType ? $device->gisType - 1 : 0,
                    'isCloudDevice' => $device->isCloudDevice,
                    'isHidden' => $device->isHidden,
                    'floor' => $device->floor ? $device->floor : $device->parking->floor
                ];
        }else{
            $row = [
                    'deviceId' => $device->deviceId,
                    'deviceCode' => $device->deviceCode,
                    'deviceNumber' => $device->deviceNumber,
                    'address'   => '',
                    'parkName' => '',
                    'parkNum' => '',
                    'note'  =>  $device->note,
                    'lat'   => $device->lat,
                    'lon'   =>  $device->lon,
                    'alt'   =>  $device->alt,
                    'gisType'   =>  $device->gisType ? $device->gisType - 1 : 0,
                    'isCloudDevice' => $device->isCloudDevice,
                    'isHidden' => $device->isHidden,
                    'floor' => $device->floor
                ];
        }

        $result['status']=1;
        $result['info'] = $row;
        return json($result);
    }
}
