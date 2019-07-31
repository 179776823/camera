<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use app\index\model\Device as Devices;
use think\facade\Session; 

class Device extends Common
{
    public function index()
    {
        $post = request()->param();
        // 查询抓拍车辆设备
        $device_list = Devices::where('deviceType',1)->select();
        $newList = [];
        if($device_list){
        	foreach ($device_list as $key => $device) {
	        	$row = [
	                    'deviceId' => $device->deviceId,
	                    'deviceCode' => $device->deviceCode,
	                    'deviceNumber' => $device->deviceNumber,
	                    'note'  =>  $device->note,
	                    'lat'   => $device->lat,
	                    'lon'   =>  $device->lon,
	                    'alt'   =>  $device->alt,
	                    'gisType'   =>  $device->gisType ? $device->gisType - 1 : 0,
	                    'isHidden' => $device->isHidden,
	                    'floor' => $device->floor
	                ];
	                $newList[] = $row;
	        }
        }
        
        $this->assign('active','capture');
        $this->assign('device_list',$newList);

        $this->assign('title', 'DATA CENTER-vigia');
        return $this->fetch();
    }

    public function deviceOne(){
        $post = request()->param();
        $deviceId = $post['deviceId'];
        $device = Devices::where('deviceId',$deviceId)->where('deviceType',1)->find();
        if(!$device){
            $result = array('status'=>0,'msg'=>'设备不存在');
            return json($result);
        }
        $row = [];
        if($device){           
            $row = [
                    'deviceId' => $device->deviceId,
                    'deviceCode' => $device->deviceCode,
                    'deviceNumber' => $device->deviceNumber,
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
