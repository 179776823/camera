<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

Route::get('think', function () {
    return 'hello,ThinkPHP5!';
});

Route::rule('user/add','base/user/add');
// Route::rule('image/get','base/image/get');

Route::rule('api/import/addDevice','agbox/import/addDevice');
Route::rule('api/import/updateDevice','agbox/import/updateDevice');
Route::rule('api/import/delDevice','agbox/import/delDevice');

Route::rule('api/user/login','agbox/user/login');
Route::rule('api/user/edit','agbox/user/updateUser');

Route::rule('VIID/System/Register','agbox/device/register');
Route::rule('VIID/System/Keepalive','agbox/device/keepalive');
Route::rule('VIID/Faces','agbox/device/face');
Route::rule('VIID/System/Time','agbox/device/timezone');
Route::rule('VIID/System/UnRegister','agbox/device/unRegister');
return [

];
