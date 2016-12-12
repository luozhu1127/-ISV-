<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/19 0019
 * Time: 17:30
 */
header("Content-type:text/html;charset=utf-8");
define("TOKEN",'HigDataDing');
define("AGENT_ID",49788662);
define("CORP_ID","ding86d199a40a10ba6935c2f4657eb6378f");
define("CORP_SECRET","p26Mhc5yL2Xuc_kN3PU9V0ATS-Rfgq6aCUqU1l5loZBey8c0kWDiFYdg_E_NeG8H");
define("SSO_SECRET","yyp84m9luLDy2sut1fUsRPi7Qfy4GQEQgBnRkx3RyLfg68R4vvlVijgvDEQP95_F");
define("JSON_HEADER",json_encode(array('Content-Type:application/json')));//创建套件前默认的套件密钥
//钉钉服务端建立连接

set_time_limit(0);

$Access_Token = getAccessToken();

$res = attendance($Access_Token,date("Y-m-d 0:0:0",strtotime("-1 day")),date("Y-m-d 0:0:0",strtotime("0 day")));


flush();
ob_flush();
sleep(1);

/*if(isset($_GET['page'])&&!empty($_GET['page']))
{
    $page = (int) $_GET['page'];

    $userInfo = userInfo();

    if($page<0)
    {
        exit("您所查看的时间范围已经超出");
    }
    if(isset($_GET['show'])&&!empty($_GET['show']))
    {
        $show = (int) $_GET['show'];
        if($show > 7)
        {
            exit("时间跨度不能超过7天");
        }
    }
    else
    {
        $show = 1;
    }

    if($show<0)
    {
        exit("您所查看的时间范围已经超出");
    }

    if(isset($_GET['userid'])&&!empty($_GET['userid']))
    {
        $userid = $_GET['userid'];
        foreach($userInfo as $val)//11用户ID
        {
            if($val['userid'] == $userid||$val['name'] == $userid)
            {
                $userid = $val['userid'];
                $userName = "<td>{$val['name']}</td>";
                break;
            }
        }

    }else
    {
        $userid = "";
    }

    if($userid != "")
    {
        $shows = [
            "<li><a href='?page={$page}&show=1&userid={$userid}' id='tolerate'>1天</a></li>",
            "<li><a href='?page={$page}&show=2&userid={$userid}' id='tolerate'>2天</a></li>",
            "<li><a href='?page={$page}&show=3&userid={$userid}' id='tolerate'>3天</a></li>",
            "<li><a href='?page={$page}&show=4&userid={$userid}' id='tolerate'>4天</a></li>",
            "<li><a href='?page={$page}&show=5&userid={$userid}' id='tolerate'>5天</a></li>",
            "<li><a href='?page={$page}&show=6&userid={$userid}' id='tolerate'>6天</a></li>",
            "<li><a href='?page={$page}&show=7&userid={$userid}' id='tolerate'>7天</a></li>",
        ];
    }
    else
    {
        $shows = [
            "<li><a href='?page={$page}&show=1' id='tolerate'>1天</a></li>",
            "<li><a href='?page={$page}&show=2' id='tolerate'>2天</a></li>",
            "<li><a href='?page={$page}&show=3' id='tolerate'>3天</a></li>",
            "<li><a href='?page={$page}&show=4' id='tolerate'>4天</a></li>",
            "<li><a href='?page={$page}&show=5' id='tolerate'>5天</a></li>",
            "<li><a href='?page={$page}&show=6' id='tolerate'>6天</a></li>",
            "<li><a href='?page={$page}&show=7' id='tolerate'>7天</a></li>",
        ];
    }

    $k = $show-1;

    $page1 = -$page*$show+1;
    $page2 = -$page*$show+$show;

    $pagea = $page-1;
    $pageb = $page+1;

    //echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>';
    echo "<h3 align='center'>当前显示".$show."天的数据<h3>";
    echo "<h2 align='center'>时间:".date("Y-m-d",strtotime("$page1 day"))."~".date("Y-m-d",strtotime("$page2 day"))."</h2>";
    echo "<div align='center'>
<input style='width: 200px; height: 30px; background: none; background: rgba(0,0,0,0.3); border-radius: 3px;color: aliceblue' title='用户ID\用户名' placeholder='请输入用户ID或用户名' type='text' id='userid' value='$userid'>
<button style='width: 50px; height: 30px;' onclick='see({$page},{$show})'>查看</button>
</div>";
    echo "<script>
	    function see(page,show)
	    {
            var userObj = document.getElementById('userid');
            var userid= userObj.value;
            window.location.href='server.php?page='+page+'&show='+show+'&userid='+userid;
	    }
    </script>";
    $res = attendance($Access_Token,date("Y-m-d 0:0:0",strtotime("$page1 day")),date("Y-m-d 0:0:0",strtotime("$page2 day")),$userid);
    if($res->errcode == 0)
    {
        $result = $res->recordresult;
        if(empty($result))
        {
            include_once "template.php";
            exit;
        }
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>';
        include_once "template.php";
    }
    else
    {
        echo "(".$res->errcode.")".$res->errmsg."<br/>";
    }
}
else
{
    header("Location:http://www.higdata.com/ding/server.php?page=1");
}*/

//echo getJsApiTicket($Access_Token);

/**
 * 获取 jsApiTicket
 * @param string $Access_Token
 * @return mixed
 * @throws Exception
 */
function getJsApiTicket($Access_Token)
{
    $tokenFile = 'js_api_ticket';//缓存文件名
    $tokenObj = json_decode(file_get_contents($tokenFile));
    if ($tokenObj->expires_in < time() or !$tokenObj->expires_in)//是否超过两小时
    {
        $arr = array("access_token"=>$Access_Token);
        $url = "https://oapi.dingtalk.com/get_jsapi_ticket";
        include_once "Http.php";
        $res = http($url,$arr);
        $res = json_decode($res);
        $errcode = $res->errcode;
        $errmsg = $res->errmsg;
        $ticket = $res->ticket;
        if($errcode == 0) {
            $token['expires_in'] = time() + 7000;
            $token['ticket'] = $ticket;
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
        $ticket = $tokenObj->ticket;
    }
    return $ticket;
}

/**
 * 获取企业接入 access_token
 * @return mixed
 * @throws Exception
 */
function getAccessToken()
{
    $tokenFile = 'corp_access_token';//缓存文件名
    $tokenObj = json_decode(file_get_contents($tokenFile));
    if ($tokenObj->expires_in < time() or !$tokenObj->expires_in)//是否超过两小时
    {
        $arr = array("corpid"=>CORP_ID,"corpsecret"=>CORP_SECRET);
        $url = "https://oapi.dingtalk.com/gettoken";
        include_once "Http.php";
        $res = http($url,$arr);
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
 * 获取企业接入 sso_token
 * @return mixed
 * @throws Exception
 */
function getSsoToken()
{
    $tokenFile = 'sso_token';//缓存文件名
    $tokenObj = json_decode(file_get_contents($tokenFile));
    if ($tokenObj->expires_in < time() or !$tokenObj->expires_in)//是否超过两小时
    {
        $arr = array("corpid"=>CORP_ID,"corpsecret"=>SSO_SECRET);
        $url = "https://oapi.dingtalk.com/sso/gettoken";
        include_once "Http.php";
        $res = http($url,$arr);
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
 * 获取部门列表
 * @param string $Access_Token 调用接口凭证
 * @param string $id 父部门id
 * @param string $lang 通讯录语言(默认zh_CN另外支持en_US)
 * @return mixed
 * @throws Exception
 */
function getDepartList($Access_Token,$id,$lang = "zh_CN")
{
    $arr = array("access_token"=>$Access_Token);
    $url = "https://oapi.dingtalk.com/department/list?=".$Access_Token;
    include_once "Http.php";
    $res = http($url,$arr);
    return json_decode($res);
}

/**
 * 获取部门详情
 * @param string $Access_Token 调用接口凭证
 * @param string $id 父部门id
 * @param string $lang 通讯录语言(默认zh_CN另外支持en_US)
 * @return mixed
 * @throws Exception
 */
function getDepartInfo($Access_Token,$id,$lang = "zh_CN")
{
    $arr = array("access_token"=>$Access_Token,"id"=>$id);
    $url = "https://oapi.dingtalk.com/department/get";
    include_once "Http.php";
    $res = http($url,$arr);
    return json_decode($res);
}

/**
 * 获取打卡数据
 * @param string $Access_Token 调用接口凭证
 * @param string $workDateFrom 查询考勤打卡记录的起始时间
 * @param string $workDateTo
 * @param string $userId 用户ID
 * @return mixed
 * @throws Exception
 */
function attendance($Access_Token,$workDateFrom,$workDateTo,$userId = "")
{
    if($userId == "")
    {
        $json = json_encode(array(
            "workDateFrom"=>$workDateFrom,
            "workDateTo"=>$workDateTo,
        ));
    }
    else
    {
        $json = json_encode(array(
            "workDateFrom"=>$workDateFrom,
            "workDateTo"=>$workDateTo,
            "userId"=>$userId,
        ));
    }

    $url = "https://oapi.dingtalk.com/attendance/list?access_token=".$Access_Token;
    include_once "Http.php";
    $res = http($url,$json,"POST",json_decode(JSON_HEADER),true);
    return json_decode($res);
}

/**
 *
 * @param string $Access_Token 接口调用凭证
 * @param string $UserID 用户id
 * @param string $lang 语言
 * @return mixed
 * @throws Exception
 */
function userInfo($lang = "zh_CN")
{
    include_once "MySQL.php";
    $config = array("host"=>"576d019344a40.bj.cdb.myqcloud.com","port"=>"10119","user"=>"cdb_outerroot","password"=>"higdata123","db"=>"Higdata","prefix"=>"","debug"=>"");
    $mysql = new MySQL($config);
    $res = $mysql->select_array("ding_user","*");
    /*if(!$res)
    {
        $arr = array("access_token"=>$Access_Token,"userid"=>$UserID);
        $url = "https://oapi.dingtalk.com/user/get";
        include_once "Http.php";
        $res = json_decode(http($url,$arr));
    }*/
    return $res;
}


function signature($timeStamp,$nonce,$echoStr)
{
    $arr = [TOKEN,$timeStamp,$nonce,$echoStr];
    sort($arr);
    $str = sha1(implode($arr));
    return $str;
}
