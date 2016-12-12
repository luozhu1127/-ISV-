<?php
header("Content-type:text/html;charset=utf-8");
define("TOKEN","1234");//创建套件填的TOKEN
define("ENCODING_AES_KEY","9wl3am20zw7435pr94dyy40f95s2p7gcamnragd3icx");//创建套件填的密钥
define("SUITE_KEY","suitewl2kttz40zl6uyhi");//成功创建套件后得到的套件密钥
define("SUITE_SECRET","x74gYC5C-TW6wlBFqrkErSl_bluBn0dtStDq_Ooykcp_PONSiNu7AjW64DtuL1e-");//套件SECRET
define("CREATE_SUITE_KEY","suite4xxxxxxxxxxxxxxx");//创建套件前默认的套件密钥
define("JSON_HEADER",json_encode(array('Content-Type:application/json')));//创建套件前默认的套件密钥

/**
 * 接收post数据
 */
$postdata = file_get_contents("php://input");

$suite_access_token = suiteAccessToken();

/**
 * 判断是否是空
 */
if(!empty($postdata))
{
    testLog($postdata,"POST");
}

function testLog($text,$a)
{
    $file = "log.txt";
    $fp = fopen($file,"a+");
    fwrite($fp,"TIME:".time()."\r\n".$a.":".$text."\r\n\r\n");
    fclose($fp);
}

if(isset($_GET['signature']))
{
    $signature = $_GET["signature"];
    $timeStamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    testLog("signature:".$signature.",timeStamp:".$timeStamp.",nonce:".$nonce,"GET");
//$postdata = $_POST['json'];
    $postList = json_decode($postdata);
    $encrypt = $postList->encrypt;
    $str = signature($timeStamp,$nonce,$encrypt);//加密签名
    if($str === $signature)
    {
        $dejson = decrypt($encrypt);
        $obj = json_decode($dejson);
        $EventType = $obj->EventType;
        testLog($dejson,"DECRYPT");
        if($EventType == "check_create_suite_url")//a验证回调URL有效性事件
        {
            $Random = $obj->Random;
            $TestSuiteKey = CREATE_SUITE_KEY;
            $encrypt = encrypt($Random,$TestSuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            //$data = sprintf($json,$str,time(),$nonce,$encrypt);
            echo $json;
            exit;
        }

        if($EventType == "suite_ticket")//b定时推送Ticket
        {
            $SuiteKey = $obj->SuiteKey;
            $SuiteTicket = $obj->SuiteTicket;
            $TimeStamp = $obj->TimeStamp;
            if($SuiteKey == SUITE_KEY)
            {
                $file = "ticket";
                $fp = fopen($file,"w");
                fwrite($fp,$SuiteTicket);
                fclose($fp);
            }
            $encrypt = encrypt("success",$SuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            echo $json;
            exit;
        }

        if($EventType == "tmp_auth_code")//c回调向ISV推送临时授权码
        {
            $SuiteKey = $obj->SuiteKey;
            $AuthCode = $obj->AuthCode;
            $TimeStamp = $obj->TimeStamp;

            if($SuiteKey == SUITE_KEY)
            {
                getPermanentCode($AuthCode,$suite_access_token);
            }

            $encrypt = encrypt("success",$SuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            echo $json;
            exit;
        }

        if($EventType == "change_auth")//d回调向ISV推送授权变更消息
        {
            $SuiteKey = $obj->SuiteKey;
            $AuthCorpId = $obj->AuthCorpId;
            $TimeStamp = $obj->TimeStamp;

            $encrypt = encrypt("success",$SuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            echo $json;
            exit;
        }

        if($EventType == "check_update_suite_url")//e“套件信息更新”事件
        {
            $Random = $obj->Random;
            $TestSuiteKey = SUITE_KEY;
            $encrypt = encrypt($Random,$TestSuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            //$data = sprintf($json,$str,time(),$nonce,$encrypt);
            echo $json;
            exit;
        }

        if($EventType == "suite_relieve")//f“解除授权”事件
        {
            $SuiteKey = $obj->SuiteKey;
            $AuthCorpId = $obj->AuthCorpId;
            $TimeStamp = $obj->TimeStamp;

            $encrypt = encrypt("success",$SuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            echo $json;
            exit;
        }

        if($EventType == "check_suite_license_code")//“校验序列号”事件
        {
            $SuiteKey = $obj->SuiteKey;
            $AuthCorpId = $obj->AuthCorpId;
            $TimeStamp = $obj->TimeStamp;
            $LicenseCode = $obj->LicenseCode;


            $encrypt = encrypt("success",$SuiteKey);
            $msg_signature = signature($timeStamp,$nonce,$encrypt);
            $json = json_encode(array(
                "msg_signature" => $msg_signature,
                "timeStamp" => $timeStamp,
                "nonce" => $nonce,
                "encrypt" => $encrypt
            ));
            echo $json;

            exit;
        }
    }
    else
    {
        echo "<h1>请求错误~~~~(>_<)~~~~  ╮(╯▽╰)╭</h1>";
        exit;
    }
}
else if(isset($_GET['m'])&&$_GET['m']=="access")
{
    echo "<pre>";
    echo "~~~~(>_<)~~~~";
    $obj = getPermanentCode("qqqwww",$suite_access_token,true);//永久授权码
    exit;
}
else
{
    echo "<pre>";
    echo "<h1>没有请求~~~~(>_<)~~~~  O(∩_∩)O嗯!
               ,
             _/((
    _.---. .'   `\
  .'      `     ^ T=
 /     \       .--'
|      /       )'-.
; ,   <__..-(   '-.)
 \ \-.__)    ``--._)
  '.'-.__.-.
    '-...-'</h1>";
    echo "<h2>
             ,%%%%%%%%,
           ,%%/\%%%%/\%%
          ,%%%\c ‘’ J/%%%
 %.       %%%%/ o  o \%%%
 `%%.     %%%%    _  |%%%
  `%%     `%%%%(__Y__)%%'
  //       ;%%%%`\-/%%%'
 ((       /  `%%%%%%%'
  \\    .'          |
   \\  /       \  | |
    \\/         ) | |
     \         /_ | |__
     (___________)))))))</h2>";
    echo "
╭︿︿︿╮
{/ o  o /}
 ( (oo) )
  ︶︶︶";
    echo "<a href='http://www.higdata.com/ding/index.php?m=access'>获取access_token</a>";
    exit;
}

/**
 * 获取套件访问Token(suite_access_token)
 * @return mixed
 * @throws Exception
 */
function suiteAccessToken(){
    $tokenFile = 'suite_access_token';//缓存文件名
    $tokenObj = json_decode(file_get_contents($tokenFile));
    if ($tokenObj->expires_in < time() or !$tokenObj->expires_in)//是否超过两小时
    {
        $file = "ticket";
        $data = file_get_contents($file);
        $url = "https://oapi.dingtalk.com/service/get_suite_token";

        $json = json_encode(array(
            "suite_key"=>SUITE_KEY,
            "suite_secret"=>SUITE_SECRET,
            "suite_ticket"=>$data,
        ));
        include_once "Http.php";
        $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
        $res = json_decode($res);
        $errcode = $res->errcode;
        $errmsg = $res->errmsg;
        $suite_access_token = $res->suite_access_token;
        if($errcode == 0) {
            $token['expires_in'] = time() + 7000;
            $token['suite_access_token'] = $suite_access_token;
            $fp = fopen($tokenFile, "w");
            fwrite($fp, json_encode($token));
            fclose($fp);
        }
        else
        {
            return json_encode(array("errcod"=>$errcode,"errmsg"=>$errmsg));
        }
    }
    else
    {
        $suite_access_token = $tokenObj->suite_access_token;
    }
    return $suite_access_token;
};

/**
 * 获取企业的永久授权码
 * @param $AuthCode  （tmp_auth_code）回调接口 获取的临时授权码 如果$name为true 则填写企业name
 * @param string $suite_access_token 套件访问Token
 * @param bool $name $AuthCode是否是 企业 name
 * @return mixed
 * @throws Exception
 */
function getPermanentCode($AuthCode,$suite_access_token,$name = false)
{
    if($name)
    {
        $file =  $file = "log/".$AuthCode;
        if(file_exists($file))
        {
            $res = json_decode(file_get_contents($file));
        }
        else
        {
            $res = json_decode('{"errcod":111111,"errmsg":"文件不存在"}');
        }
    }
    else
    {
        $json = json_encode(array(
            "tmp_auth_code"=>$AuthCode
        ));
        $url = "https://oapi.dingtalk.com/service/get_permanent_code?suite_access_token=".$suite_access_token;
        include_once "Http.php";
        $resJson = http($url,$json,"POST",json_decode(JSON_HEADER),true);
        $file = "log/";
        $obj = json_decode($resJson);

        $permanent_code = $obj->permanent_code;
        $ch_permanent_code = $obj->ch_permanent_code;
        $corpid = $obj->auth_corp_info->corpid;
        $corp_name = $obj->auth_corp_info->corp_name;

        $fp = fopen($file.$corp_name,"w");
        $corp = json_encode(array(
            "permanent_code"=>$permanent_code,
            "ch_permanent_code"=>$ch_permanent_code,
            "corpid"=>$corpid,
            "timestamp"=>time()
        ));

        fwrite($fp,$corp."\r\n");
        fclose($fp);
        $res = json_decode($corp);
    }
    return $res;
}

/**
 * 获取企业授权的access_token
 * @param string $auth_corpid 授权方corpid
 * @param string $permanent_code 永久授权码，通过get_permanent_code获取
 * @param string $suite_access_token  套件访问Token
 * @return mixed
 * @throws Exception
 */
function getAccessToken($auth_corpid,$permanent_code,$suite_access_token)
{
    $tokenFile = 'access_token';//缓存文件名
    $tokenObj = json_decode(file_get_contents($tokenFile));
    if ($tokenObj->expires_in < time() or !$tokenObj->expires_in)//是否超过两小时
    {
        $json = json_encode(array(
            "auth_corpid"=>$auth_corpid,
            "permanent_code"=>$permanent_code
        ));
        $url = "https://oapi.dingtalk.com/service/get_corp_token?suite_access_token=".$suite_access_token;
        include_once "Http.php";
        $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
        $res = json_decode($res);
        $errcode = $res->errcode;
        $errmsg = $res->errmsg;
        $access_token = $res->access_token;
        if($errcode == 0) {
            $token['expires_in'] = time() + 7000;
            $token['access_token'] = $access_token;
            $fp = fopen($tokenFile, "w");
            fwrite($fp, json_encode($token));
            fclose($fp);
        }
        else
        {
            return json_encode(array("errcod"=>$errcode,"errmsg"=>$errmsg));
        }
    }else
    {
        $access_token = $tokenObj->access_token;
    }
    return $access_token;
}

/**
 * 获取企业授权的授权数据
 * @param string $auth_corpid 授权方corpid
 * @param string $permanent_code  永久授权码，通过get_permanent_code获取
 * @param string $suite_access_token 套件访问Token
 * @return mixed
 * @throws Exception
 */
function getAuthInfo($auth_corpid,$permanent_code,$suite_access_token)
{
    $json = json_encode(array(
        "auth_corpid"=>$auth_corpid,
        "permanent_code"=>$permanent_code,
        "suite_key"=>SUITE_KEY
    ));

    $url = "https://oapi.dingtalk.com/service/get_auth_info?suite_access_token=".$suite_access_token;
    include_once "Http.php";
    $resJson = http($url,$json,"POST",json_decode(JSON_HEADER),true);
    $obj = json_decode($resJson);
    return $obj;
}

/**
 * 获取企业的应用信息
 * @param string $auth_corpid 授权方corpid
 * @param string $permanent_code 永久授权码，从get_permanent_code接口中获取
 * @param int $agentid 授权方应用id
 * @param string $suite_access_token 套件访问Token
 * @return mixed
 * @throws Exception
 */
function getCorpInfo($auth_corpid,$permanent_code,$agentid,$suite_access_token)
{
    $json = json_encode(array(
        "suite_key"=>SUITE_KEY,
        "auth_corpid"=>$auth_corpid,
        "permanent_code"=>$permanent_code,
        "agentid"=>$agentid
    ));
    $url = "https://oapi.dingtalk.com/service/get_agent?suite_access_token=".$suite_access_token;
    include_once "Http.php";
    $resJson = http($url,$json,"POST",json_decode(JSON_HEADER),true);

    $obj = json_decode($resJson);
    return $obj;
}

/**
 * 激活授权套件
 * @param string $auth_corpid  授权方corpid
 * @param string $permanent_code 永久授权码，从get_permanent_code接口中获取
 * @param string $suite_access_token 套件访问Token
 * @return string
 * @throws Exception
 */
function activeSuite($auth_corpid,$permanent_code,$suite_access_token)
{
    $json = json_encode(array(
        "suite_key"=>SUITE_KEY,
        "auth_corpid"=>$auth_corpid,
        "permanent_code"=>$permanent_code
    ));
    $url = "https://oapi.dingtalk.com/service/activate_suite?suite_access_token=".$suite_access_token;
    include_once "Http.php";
    $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
    return json_decode($res);
}

/**
 * 获取应用未激活的企业列表
 * @param string $app_id 套件下的微应用ID
 * @param string $suite_access_token 套件访问Token
 * @return mixed
 * @throws Exception
 */
function getUnActiveList($app_id,$suite_access_token)
{
    $json = json_encode(array(
        "app_id"=>$app_id
    ));
    $url = "https://oapi.dingtalk.com/service/get_unactive_corp?suite_access_token=".$suite_access_token;
    include_once "Http.php";
    $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
    return json_decode($res);
}

/**
 * 重新授权未激活应用的企业
 * @param string $app_id 套件下的微应用ID
 * @param array $corpid_list 未激活的corpid列表
 * @param string $suite_access_token 套件访问Token
 * @return mixed
 * @throws Exception
 */
function reAuthCorp($app_id,$corpid_list,$suite_access_token)
{
    $json = json_encode(array(
        "app_id"=>$app_id,
        "corpid_list"=>$corpid_list
    ));
    $url = "https://oapi.dingtalk.com/service/reauth_corp?suite_access_token=".$suite_access_token;
    include_once "Http.php";
    $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
    return json_decode($res);
}

/**
 *  ISV为授权方的企业单独设置IP白名单
 * @param string $auth_corpid 授权方corpid
 * @param array $whiteList 要为其设置的IP白名单,格式支持IP段,用星号表示,
 * @param string $suite_access_token 套件访问Token
 * @return mixed
 * @throws Exception
 */
function setIpWhiteList($auth_corpid,$whiteList,$suite_access_token)
{
    $json = json_encode(array(
        "auth_corpid"=>$auth_corpid,
        "ip_whitelist"=>$whiteList
    ));
    $url = "https://oapi.dingtalk.com/service/set_corp_ipwhitelist?suite_access_token=".$suite_access_token;
    include_once "Http.php";
    $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
    return json_decode($res);
}


/**
 * 生成新的签名
 * @param $timeStamp
 * @param $nonce
 * @param string $encrypt 新密文
 * @return string
 */
function signature($timeStamp,$nonce,$encrypt)
{
    $arr = [TOKEN,$timeStamp,$nonce,$encrypt];
    sort($arr);
    $str = sha1(implode($arr));
    return $str;
}

/**
 * 解密方法
 * @param string $encrypt 密文
 * @return string
 */
function decrypt($encrypt)
{
    $encrypt = base64_decode($encrypt);
    $key = base64_decode(ENCODING_AES_KEY."=");
    $iv = substr($key,0,16);
    $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
    mcrypt_generic_init($module, $key, $iv);
    $decrypted = mdecrypt_generic($module, $encrypt);
    mcrypt_generic_deinit($module);
    mcrypt_module_close($module);
    $pad = ord(substr($decrypted, -1));
    if ($pad < 1 || $pad > 32) {
        $pad = 0;
    }
    $decrypted =  substr($decrypted, 0, (strlen($decrypted) - $pad));
    $decrypted = substr($decrypted,20);
    $decrypted = substr($decrypted,0,strlen($decrypted)-strlen(SUITE_KEY));
    return $decrypted;
}

/**
 * 随机生成16位字符
 * @return string
 */
function getRandomStr()
{
    $str = "";
    $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($str_pol) - 1;
    for ($i = 0; $i < 16; $i++) {
        $str .= $str_pol[mt_rand(0, $max)];
    }
    return $str;
}

/**
 * 加密方法
 * @param string $Random 加密字符
 * @param string $SuiteKey 加密秘钥
 * @return string
 */
function encrypt($Random,$SuiteKey)
{
    $rr = getRandomStr();

    $text = $rr . pack("N",strlen($Random)).$Random.$SuiteKey;

    $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

    $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

    $key = base64_decode(ENCODING_AES_KEY."=");

    $iv = substr($key,0,16);

    $text_length = strlen($text);

    $amount_to_pad = 32 - ($text_length % 32);

    if ($amount_to_pad == 0) {
        $amount_to_pad = 32;
    }

    $pad_chr = chr($amount_to_pad);

    $tmp = "";
    for ($index = 0; $index < $amount_to_pad; $index++) {
        $tmp .= $pad_chr;
    }
    $text = $text . $tmp;

    mcrypt_generic_init($module, $key, $iv);

    $encrypted = mcrypt_generic($module, $text);
    mcrypt_generic_deinit($module);
    mcrypt_module_close($module);

    $encrypted = base64_encode($encrypted);
    return $encrypted;
}
