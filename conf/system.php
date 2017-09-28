<?php
//Viettel 加密KDY
return [
    'NetType'       => '_NetType'.md5(getRealIp()),  //网路方式键
    'GetTelco'      => '_GetTelco'.md5(getRealIp()),  // 网络运营商键
    'GetMsisdn'     => '_GetMsisdn'.md5(getRealIp()),
    'UserSub'       => '_UserSub'.md5(getRealIp()),
    'SeesionID'     => '_SeesionID'.md5(getRealIp()),
    'IsLogin'       => '_IsLogin'.md5(getRealIp()),
    'Options'       => '_Options'.md5(getRealIp()),
    'Menus'         => '_Menus'.md5(getRealIp()),
    'UserLogin'     => '_UserLogin'.md5(getRealIp()),
    'AppsLogin'     => '_AppsLogin'.md5(getRealIp()),
    
];


