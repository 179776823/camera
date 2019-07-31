<?php


namespace app\index\model;


use think\Model;

class Device extends Model
{
    protected $table='device';
    
    public function parking()
    {
        return $this->hasOne('Parking','id','deviceObjId');
    }
    
}