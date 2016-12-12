<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/16 0016
 * Time: 17:45
 */
$json = '{"suite_key":"suitegjgug0ptwfmd62z8","auth_corpid":"ding890214f493b6fed335c2f4657eb6378f","permanent_code":"s6iiodfbBitB9aESh-sAt-LFs6CTFYuF5p8ppZiYvcxXYthYFx8eGlYtAraan7Rx","agentid":50459779}';


include "Http.php";
$url = "https://oapi.dingtalk.com/service/get_agent?suite_access_token=83dd402fca763abcb512924e41cc8358";
$res  =  http($url,$json,"post",array('Content-Type:application/json'),true);

var_dump($res) ;