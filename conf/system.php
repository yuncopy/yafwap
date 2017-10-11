<?php
//Viettel 加密KDY
$host = $_SERVER['HTTP_HOST'];
return [
    'NetType'       => '_NetType'.md5(getRealIp().$host),  //网路方式键
    'GetTelco'      => '_GetTelco'.md5(getRealIp().$host),  // 网络运营商键
    'GetMsisdn'     => '_GetMsisdn'.md5(getRealIp().$host),
    'UserSub'       => '_UserSub'.md5(getRealIp().$host),
    'SeesionID'     => '_SeesionID'.md5(getRealIp().$host),
    'IsLogin'       => '_IsLogin'.md5(getRealIp().$host),
    'Options'       => '_Options'.md5(getRealIp().$host),
    'Menus'         => '_Menus'.md5(getRealIp().$host),
    'UserLogin'     => '_UserLogin'.md5(getRealIp().$host),
    'AppsLogin'     => '_AppsLogin'.md5(getRealIp().$host),
    
];


