<?php
/**
 *
 */
/**
 * Created by PhpStorm.
 * User: anna
 * Date: 2018/7/10
 * Time: 上午12:21
 * 用于云服务器项目
 */

namespace app\agbox\controller;

use app\agbox\model\Camera;
use app\agbox\model\DeviceServer;
use app\agbox\model\FaceEvent;
use app\agbox\model\FaceImage;
use Bluerhinos\phpMQTT;
use think\Cache;
use think\Db;
use think\Exception;
use think\facade\Config;
use think\facade\Request;
use think\worker\Server;

class Device
{
    //1400协议请求注册设备
    public function register(){
        $post = request()->header();
        $arr = file_get_contents("php://input");
        try{
            $register_arr = json_decode($arr,true);
            if(!array_key_exists('authorization',$post)){
                header('WWW-Authenticate: Digest realm="realm@host.com", qop="auth", nonce="dcd98b7102dd2f0e8b11d0f600bfb0c093", opaque="5ccc069c403ebaf9f0171e9517f40e41"');
                die;
            }
            $username = Config::get('digest_user');
            $password = Config::get('digest_pwd');
            $uri = "/VIID/System/Register";
            $auth_header_array = explode(',', str_replace('Digest','',$post['authorization']));
            $parsed = array();
            foreach ($auth_header_array as $pair)
            {
                $vals = explode('=', $pair);
                $parsed[trim($vals[0])] = trim($vals[1], '" ');
            }
            $response_realm     = (isset($parsed['realm'])) ? $parsed['realm'] : "";
            $response_nonce     = (isset($parsed['nonce'])) ? $parsed['nonce'] : "";
            $response_opaque    = (isset($parsed['opaque'])) ? $parsed['opaque'] : "";
            $response_cnonce    = (isset($parsed['cnonce'])) ? $parsed['cnonce'] : "";
            $authenticate1 = md5($username.":".$response_realm.":".$password);
            $authenticate2 = md5("POST:".$uri);
            $authenticate_response = md5($authenticate1.":".$response_nonce.":00000001:".$response_cnonce.":auth:".$authenticate2);
            if($authenticate_response!=$parsed['response']){
                $registerData = $this->return_result($post,1);
//                echo json_encode($post);die;
            }else{
                if($post['user-identify']){
                    $registerData = $this->return_result($post,0);
                    $keep = Db::table('camerakeep')->where('deviceCode',$post['user-identify'])->find();
                    if($keep){
                        Db::table('camerakeep')->where('id',$keep['id'])->update(['createTime'=>date('Y-m-d H:i:s',time())]);
                    }else{
                        $data['deviceCode'] = $post['user-identify'];
                        $data['createTime'] = date('Y-m-d H:i:s',time());
                        Db::table('camerakeep')->insert($data);
                    }

                }else{
                    $registerData = $this->return_result($post,1);
                }
            }
//            $log = ['deviceLog'=>json_encode($registerData),'createTime'=>date('Y-m-d H:i:s',time())];
//            Db::table('cameralog')->insert($log);
//            echo json_encode($registerData);
            return json($registerData)->header(['Content-Length'=>strlen(json_encode($registerData))]);
        }catch (Exception $exception){
            return $exception;
        }

    }

    //1400协议请求注消设备
    public function unRegister(){
        $post = request()->header();
        $arr = file_get_contents("php://input");
        try{
            $register_arr = json_decode($arr,true);
            if($post['user-identify'] && $register_arr['UnRegisterObject']['DeviceID']==$post['user-identify']){
                //查询注册保活的设备删除
                $keep = Db::table('camerakeep')->where('deviceCode',$post['user-identify'])->find();
                if($keep){
                    Db::table('camerakeep')->where('id',$keep['id'])->delete();
                }
                $registerData = $this->return_result($post,0);

            }else{
                $registerData = $this->return_result($post,1);
            }

            return json($registerData)->header(['Content-Length'=>strlen(json_encode($registerData))]);
        }catch (Exception $exception){
            return $exception;
        }

    }

    //1400协议请求设备保活功能
    public function keepalive(){
        $post = request()->header();
        $arr = file_get_contents("php://input");
        try{
            $keepalive = json_decode($arr,true);

            if($post['user-identify'] && $keepalive['KeepaliveObject']['DeviceID']==$post['user-identify']){
                $registerData = $this->return_result($post,0);
                $keep = Db::table('camerakeep')->where('deviceCode',$post['user-identify'])->find();
                if($keep){
                    Db::table('camerakeep')->where('id',$keep['id'])->update(['createTime'=>date('Y-m-d H:i:s',time())]);
                }else{
                    $data['deviceCode'] = $post['user-identify'];
                    $data['createTime'] = date('Y-m-d H:i:s',time());
                    Db::table('camerakeep')->insert($data);
                }

            }else{
                $registerData = $this->return_result($post,1);
            }
            return json($registerData)->header(['Content-Length'=>strlen(json_encode($registerData))]);
        }catch (Exception $exception){
            return $exception;
        }
    }

    //1400协议请求发送抓拍人脸数据
    public function face(){
        $post = request()->header();
        $arr = file_get_contents("php://input");
        try{
            $facelist = json_decode($arr,true);
            $time = time();
            if($post['user-identify']){
                $keep = Db::table('camerakeep')->where('deviceCode',$post['user-identify'])->find();
                //保活时间在5分钟内，时间超出需要重新保活
                if(!$keep || $time-strtotime($keep['createTime'])>300){
                    //保活失败
                    $registerData = $this->return_result($post,1);
                    return json($registerData)->header(['Content-Length'=>strlen(json_encode($registerData))]);
                }
                if($facelist){
                    foreach ($facelist['FaceListObject']['FaceObject'] as $key=>$val){
                        $camera = Camera::where('deviceCode',$val['DeviceID'])->find();
                        $souce = FaceEvent::where('SourceID',$val['SourceID'])->find();
                        if($camera && !$souce){
                            $faceEvent = new FaceEvent();
                            $faceEvent->deviceId = $camera->id;
                            $faceEvent->deviceCode = $camera->deviceCode;
                            $faceEvent->dataTime = date('Y-m-d H:i:s',time());
                            $faceEvent->eventTime = date('Y-m-d H:i:s',time());
                            $faceEvent->faceContrast = 0;//人脸比对结果
                            $faceEvent->personCode = 12;
                            $faceEvent->featureInfo = '';
                            $faceEvent->lon = $camera->lon;
                            $faceEvent->lat = $camera->lat;
                            $faceEvent->alt = $camera->alt;
                            $faceEvent->gisType = $camera->gisType;
                            $faceEvent->SourceID = $val['SourceID'];
                            $faceEvent->FaceID = $val['FaceID'];
                            $faceEvent->count = 1;
                            $faceEvent->target = '';
                            $faceEvent->groupId = uuid();
                            $faceEvent->similarity = 0;
                            $faceEvent->createTime = date('Y-m-d H:i:s',time());
                            $faceEvent->credentialType = 1;//证件类型默认是1-身份证
                            $faceEvent->save();
                            if($faceEvent->id){
                                foreach ($val['SubImageList']['SubImageInfoObject'] as $k=>$v){
                                    $data['faceEventId'] = $faceEvent->id;
                                    $data['deviceCode'] = $v['DeviceID'];
                                    $data['eventSort'] = $v['EventSort'];
                                    $data['triggerTime'] = date('Y-m-d H:i:s',strtotime($v['ShotTime']));
                                    $data['fileFormat'] = $v['FileFormat'];
                                    $data['imageID'] = $camera->deviceCode;
                                    $data['sub'] = $v['Type'];
                                    $data['height'] = $v['Height'];
                                    $data['width'] = $v['Width'];
                                    if($v['Data']){
                                        $eventPic = saveBase64Image2($v['Data'],'snap');
                                        $data['eventPicUrl'] = $eventPic;
                                    }
                                    $data['createTime']=date('Y-m-d H:i:s',time());
                                    $faceimage = FaceImage::create($data);
                                    if($faceimage->id){
                                        $server = DeviceServer::find();
                                        if($server){
                                            $send_data['id'] = $faceimage->id;
                                            $send_data['jsonrpc'] = '2.0';
                                            $send_data['method'] = 'addEvent';
                                            //发送事件数据
                                            $params['channel'] = 0;
                                            $params['count'] = 1;
                                            $params['credentialType'] = 1;
                                            $params['credentialNo'] = '';
                                            $params['dataTime'] = $faceEvent->dataTime;
                                            $params['deviceCode'] = $v['DeviceID'];
                                            $params['deviceId'] = $v['DeviceID'];
                                            $params['eventCode'] = 12;
                                            $params['eventPic'] = '';
                                            $params['eventTime'] = $faceEvent->eventTime;
                                            $params['eventType'] = 23;
                                            $params['faceContrast'] = 0;
                                            $params['gisType'] = $camera->gisType;
                                            $params['groupId'] = $faceEvent->groupId;
                                            $params['lat'] = $camera->lat;
                                            $params['lon'] = $camera->lon;
                                            $params['alt'] = $camera->alt;
                                            $params['personType'] = 12;
                                            $params['picUrl'] = 'http://'.$server->server_ip.$eventPic;
                                            $params['similarity'] = 0;
                                            $params['sub'] = false;
                                            if($v['Type']==14){
                                                $params['target'] = array(array(
                                                    'hight'=> $v['Height'],
                                                    'left'=> $val['LeftTopX'],
                                                    'top'=> $val['LeftTopY'],
                                                    'width'=>  $v['Width']
                                                ));
                                            }
                                            $params['triggerTime'] = $data['triggerTime'];
                                            $params['note'] = array(
                                                'Feature'=>array(
                                                    'sex'=> 0,
                                                    'age' => 0,
                                                    'express' => 0,
                                                    'mask' => 0,
                                                    'glass' => 0
                                                ));
                                            $params['feature'] = "sex:0,age:0,express:0,mask:0,glass:0";
                                            $params['scene'] = $v['Type']==11 ? false : true;//数据服务器中1-false是小图，0-true是全景图
                                            $send_data['params'] = $params;
                                            $url = 'http://'.$server->data_ip .'/agbox/device/capture';
                                            $port = $server->data_port;
                                            $send_datas['key'] = uuid();
                                            $send_datas['json'] = json_encode($send_data);
                                            send_post($url,$port,$send_datas,'',false);
                                        }
                                    }
                                }
                            }

                        }else{
                            continue;
                        }
                    }
                }
                //保活成功
                $registerData = $this->return_result($post,0);
                return json($registerData)->header(['Content-Length'=>strlen(json_encode($registerData))]);
            }
        }catch (Exception $exception){
            return $exception;
        }
    }

    //1400协议请求设备校时
    public function timezone(){
        $post = request()->header();
        $registerData['SystemTimeObject']['VIIDServerID'] = $post['user-identify'];
        $registerData['SystemTimeObject']['TimeMode'] = '2';
        $registerData['SystemTimeObject']['TimeZone'] = 'UTC+8';
        $registerData['SystemTimeObject']['LocalTime'] = date('YmdHis',time());
        return json($registerData)->header(['Content-Length'=>strlen(json_encode($registerData))]);
    }

    //返回结果方法定义
    function return_result($post,$type=0){
        $registerData['ResponseStatus']['DeviceID'] = $post['user-identify'];
        $registerData['ResponseStatus']['StatusCode'] = $type;
        $registerData['ResponseStatus']['StatusString'] = $type==0 ? 'success':'fail';
        $registerData['ResponseStatus']['LocalTime'] = date('Y-m-d H:i:s',time());
        return $registerData;
    }
}