<?php
/**
 *
 */
/**
 * Created by PhpStorm.
 * User: anna
 * Date: 2018/7/10
 * Time: 上午12:21
 */

namespace app\agbox\controller;

use app\agbox\model\Devices;
use app\agbox\model\FaceIdentificationEvent;
use app\agbox\model\AccessControlEvent;
use app\agbox\model\Parking;
use app\agbox\model\ParkingEvent;
use app\agbox\model\UsbEvent;
use app\agbox\model\Villages;
use think\Exception;

class Import
{
    /**
     * 添加设备
     * $deviceType 1-人脸抓拍 2-视频安防监控 3-USB防插拔 4-出入口控制 5-停车库 6-入侵和紧急报警 7-实时电子巡检 8-状态感知检测 9-状态采集检测
     */
    public function addDevice(){

        $post = request()->param();
//        die;
        $type = $post['type'];
        if(!$type || !$post['deviceCode']){
            return json(array('code'=>500,'message'=>'failed'));
        }
        if($type == 'snap'){
            // 添加被动抓拍设备
            $deviceType = 1;
        }else if($type == 'entrance'){
            // 添加出入口设备
            $deviceType = 4;
        }else if($type == 'parking') {
            $deviceType = 5;
        }else if($type == 'usb'){
            $deviceType = 3;
        }else if($type == 'perceptin'){
            $deviceType = 8;
        }
        $device = Devices::get(['deviceCode'=>$post['deviceCode'],'deviceType'=>$deviceType]);
        if($device){
            return json(array('code'=>500,'message'=>'该设备已存在'));
        }
        try {
            $village = Villages::get(1);
            // 添加设备
            $deviceCode = $post['deviceCode'];
            $address = isset($post['address']) ? $post['address'] : '';
            $note = isset($post['note']) ? $post['note'] : '';
            $lat = isset($post['lat']) ? $post['lat'] : 0;
            $lon = isset($post['lon']) ? $post['lon'] : 0;
            $alt = isset($post['alt']) ? $post['alt'] : 0;
            $floor = isset($post['floor']) ? $post['floor'] : '';
            $gisType = isset($post['gisType']) ? $post['gisType'] + 1 : 0;
            $gisArea = isset($post['gisArea']) ? $post['gisArea'] : [];
            $isHidden = isset($post['isHidden']) ? $post['isHidden'] : 0;

            $device = new Devices();
            $device->deviceCode = $deviceCode;
            $device->deviceType = $deviceType;
            if($village){
                $device->villageId = $village->villageId;
                $device->villageCode = $village->villageCode;
            }
            
            $device->lat = $lat;
            $device->lon = $lon;
            $device->alt = $alt;
            $device->address = $address;
            $device->floor = $floor;
            $device->note = $note;
            $device->gisType = $gisType;
            $device->isHidden = $isHidden;

            if ($gisArea) {
                $device->gisArea = json_encode($gisArea);
            }
            $device->createTime = date('Y-m-d H:i:s');
            $device->save();

            if($type == 'parking'){
                //添加停车场设备
                $parkName = isset($post['parkName']) ? $post['parkName'] : '';
                $parkNum = isset($post['parkNum']) ? $post['parkNum'] : 0;
                $isCloudDevice = isset($post['isCloudDevice']) ? $post['isCloudDevice'] : 0;

                $device->isCloudDevice = $isCloudDevice;
                $device->save();

                $park = new Parking();
                if($village){
                    $park->villageId = $village->villageId;
                    $park->villageCode = $village->villageCode;
                }
                
                $park->parkName = $parkName;
                $park->parkNum = $parkNum;
                $park->lat = $lat;
                $park->lon = $lon;
                $park->alt = $alt;
                $park->address = $address;
                $park->note = $note;
                $park->gisType = $gisType;
                $park->floor = $floor;
                if ($gisArea) {
                    $park->gisArea = json_encode($gisArea);
                }
                $park->createTime = date('Y-m-d H:i:s');
                $park->save();

                $device->deviceObjId = $park->id;
                $device->save();
            }
            return json([
                'code'=>200,
                'message'=>'success',
                'result' => [
                    'deviceId'=>$device->deviceId
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
     * $deviceType 1-人脸抓拍 2-视频安防监控 3-USB防插拔 4-出入口控制 5-停车库 6-入侵和紧急报警 7-实时电子巡检 8-状态感知检测 9-状态采集检测
     */
    public function updateDevice(){
        $post = request()->param();
        $type = $post['type'];
        if(!$type || !$post['deviceCode'] || !$post['deviceId']){
            return json(array('code'=>500,'message'=>'param failed'));
        }
        try {
            $device = Devices::get(['deviceId'=>$post['deviceId']]);
            if(!$device){
                return json(array('code'=>500,'message'=>'该设备不存在,无法更新'));
            }
            $deviceCode = $post['deviceCode'];
            $deviceNumber = isset($post['deviceNumber']) ? $post['deviceNumber'] : uuid();
            $address = isset($post['address']) ? $post['address'] : '';
            $note = isset($post['note']) ? $post['note'] : '';
            $lat = isset($post['lat']) ? $post['lat'] : 0;
            $lon = isset($post['lon']) ? $post['lon'] : 0;
            $alt = isset($post['alt']) ? $post['alt'] : 0;
            $floor = isset($post['floor']) ? $post['floor'] : '';
            $gisType = isset($post['gisType']) ? $post['gisType'] + 1 : 0;
            $gisArea = isset($post['gisArea']) ? $post['gisArea'] : [];
            $isHidden = isset($post['isHidden']) ? $post['isHidden'] : 0;

            $device->deviceCode = $deviceCode;
            $device->deviceNumber = $deviceNumber;
            $device->lat = $lat;
            $device->lon = $lon;
            $device->alt = $alt;
            $device->address = $address;
            $device->floor = $floor;
            $device->note = $note;
            $device->gisType = $gisType;
            $device->isHidden = $isHidden;
            if ($gisArea) {
                $device->gisArea = json_encode($gisArea);
            }
            $device->save();

            if($type == 'parking'){
                //停车场设备
                $parkName = isset($post['parkName']) ? $post['parkName'] : '';
                $parkNum = isset($post['parkNum']) ? $post['parkNum'] : 0;
                $isCloudDevice = isset($post['isCloudDevice']) ? $post['isCloudDevice'] : 0;

                $device->isCloudDevice = $isCloudDevice;
                $device->save();

                $parkingUpdate = [
                    'parkName' => $parkName,
                    'parkNum' => $parkNum,
                    'lat' => $lat,
                    'lon' => $lon,
                    'alt' => $alt,
                    'address' => $address,
                    'note' => $note,
                    'gisType' => $gisType,
                    'gisArea' => $gisArea ? json_encode($gisArea): '',
                    'updateTime' => date('Y-m-d H:i:s')
                ];
                $device->parking->save($parkingUpdate);
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

    /**
     * 删除设备
     * $deviceType 1-人脸抓拍 2-视频安防监控 3-USB防插拔 4-出入口控制 5-停车库 6-入侵和紧急报警 7-实时电子巡检 8-状态感知检测 9-状态采集检测
     */
    public function delDevice(){
        $post = request()->param();
        $type = $post['type'];
        if(!$type || !isset($post['deviceId'])){
            return json(array('code'=>500,'message'=>'param failed'));
        }
        $device = Devices::get(['deviceId'=>$post['deviceId']]);
        if(!$device){
            return json(array('code'=>500,'message'=>'该设备不存在,无法删除'));
        }
        $deviceCode = $device->deviceCode;
        try {
            if($type == 'parking'){
                $device->together('parking')->delete();
                ParkingEvent::destroy(['deviceCode' => $deviceCode]);
            }else{
                $device->delete();
                if($type == 'snap'){
                    FaceIdentificationEvent::destroy(['deviceCode' => $deviceCode]);
                }elseif($type == 'entrance'){
                    AccessControlEvent::destroy(['deviceCode' => $deviceCode]);
                }elseif($type == 'usb'){
                    UsbEvent::destroy(['deviceCode' => $deviceCode]);
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