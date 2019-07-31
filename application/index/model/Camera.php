<?php


namespace app\index\model;

use think\Model;

class Camera extends Model
{
    protected $table='camera';
    protected $hidden = ['updateTime','createTime'];

}