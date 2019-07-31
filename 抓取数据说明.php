<?php 
<!-- 抓取相机传输保活的数据 -->
Array
(
    [KeepaliveObject] => Array
        (
            [DeviceID] => 34020000001190000001
        )

)


<!-- 抓取相机传输人脸的数据 -->
Array
(
    [FaceListObject] => Array
        (
            [FaceObject] => Array
                (
                    [0] => Array
                        (
                            [DeviceID] => 34020000001190000001
                            [FaceID] => 340200000011900000010220190711150344000600600061
                            [GenderCode] => 0 //0未知 1是男 2是女 9是未说明的性别
                            [InfoKind] => 1 //自动采集为1
                            [IsCriminalInvolved] => 2
                            [IsDetainees] => 2  //是否在押人员 0：否；1：是；2：不确定，人工采集必填
                            [IsDriver] => 2 //是否驾驶人员 0：否；1：是；2：不确定
                            [IsForeigner] => 2  //是否涉外人员 0：否；1：是；2：不确定
                            [IsSuspectedTerrorist] => 2 //是否涉恐人员 0：否；1：是；2：不确定
                            [IsSuspiciousPerson] => 2
                            [IsVictim] => 2  //人工采集时必选 0：否；1：是；2：不确定
                            [LeftTopX] => 163 //左上角 X 坐标
                            [LeftTopY] => 88 //左上角 Y 坐标
                            [RightBtmX] => 1764 //右下角 X 坐标
                            [RightBtmY] => 989  //右下角 Y 坐标
                            [SourceID] => 34020000001190000001022019071115034400060
                            [SubImageList] => Array
                                (
                                    [SubImageInfoObject] => Array
                                        (
                                            [0] => Array
                                                (
                                                    [Data] =>
                                                    [DeviceID] => 34020000001190000001
                                                    [EventSort] => 10
                                                    [FileFormat] => Jpeg
                                                    [Height] => 1080
                                                    [ImageID] => 34020000001190000001022019071115034400060
                                                    [ShotTime] => 20190711150344
                                                    [StoragePath] => 
                                                    [Type] => 14
                                                    [Width] => 1920
                                                )

                                            [1] => Array
                                                (
                                                    [Data] => 
                                                    [DeviceID] => 34020000001190000001
                                                    [EventSort] => 10
                                                    [FileFormat] => Jpeg
                                                    [Height] => 672
                                                    [ImageID] => 34020000001190000001022019071115034400062
                                                    [ShotTime] => 20190711150344
                                                    [StoragePath] => 
                                                    [Type] => 11
                                                    [Width] => 448
                                                )

                                        )

                                )

                        )

                )

        )

)

// 发送给数据服务器的事件数据

{
    "id": 2,
    "jsonrpc": "2.0",
    "method": "addEvent",
    "params": {
        "channel": 3,
        "count": 1,
        "credentialType": 1,
        "credentialNo": "",
        "dataTime": "2019-07-09 17:35:53",
        "deviceCode": "21345678902134567890",
        "deviceId": "21345678902134567890",
        "eventCode": 12,
        "eventPic": "vBtqgtCcSatOSzf5RH3Jhdje",
        "eventTime": "2019-07-09 17:35:55",
        "eventType": 23,
        "faceContrast": 0,
        "gisType": 1,
        "groupId": "f8cd203d-5e93-48f6-b1ed-5a794c648c36",
        "lat": 31.2451,
        "lon": 121.506,
        "alt": 6,
        "personType": 12,
        "picUrl": "http://192.168.36.123:23500/cdbc/2019-07-09/110/17/35-53-1412-sence.jpg",
        "similarity": 0,
        "sub": false,
        "target": [{
            "hight": 333,
            "left": 308,
            "top": 332,
            "width": 386
        }],
        "triggerTime": "2019-07-09 17:35:53",
        "note": {
            "Feature": {
                "sex": 1,
                "age": 29,
                "express": 1,
                "mask": 0,
                "glass": 1
            }
        },
        "feature": "sex:1,age:29,express:1,mask:0,glass:1",
        "scene": true
    }
}

{
    "id": 1,
    "jsonrpc": "2.0",
    "method": "addEvent",
    "params": {
        "channel": 3,
        "count": 1,
        "credentialType": 1,
        "credentialNo": "",
        "dataTime": "2019-07-09 17:35:53",
        "deviceCode": "21345678902134567890",
        "deviceId": "21345678902134567890",
        "eventCode": 12,
        "eventPic": "vBtqgtCcSatOSzf5RH3Jhdje",
        "eventTime": "2019-07-09 17:35:55",
        "eventType": 23,
        "faceContrast": 0,
        "gisType": 1,
        "groupId": "f8cd203d-5e93-48f6-b1ed-5a794c648c36",
        "lat": 31.2451,
        "lon": 121.506,
        "alt": 6,
        "personType": 12,
        "picUrl": "http://192.168.36.123:23500/cdbc/2019-07-09/110/17/35-53-1412-face.jpg",
        "similarity": 0,
        "sub": false,
        "triggerTime": "2019-07-09 17:35:53",
        "note": {
            "Feature": {
                "sex": 1,
                "age": 29,
                "express": 1,
                "mask": 0,
                "glass": 1
            }
        },
        "feature": "sex:1,age:29,express:1,mask:0,glass:1",
        "scene": false
    }
}



