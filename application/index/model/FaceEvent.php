<?php


namespace app\index\model;


use think\Model;

class FaceEvent extends Model
{
    protected $table = 'faceEvent';

    public function faceImage(){
        /*
         * 参数一:关联的模型名
         * 参数二:关联的模型的id
         * 参数三:当前模型的关联字段
         * */
        return $this->hasMany('FaceImage','deviceId','id');
    }
}