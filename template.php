<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="css/work_mark.css"/>
		<title>员工考勤表</title>
	</head>
	<body>
		<div>
            <div class="page_box">
                <ul class="page" style="clear: both">
                    <?php
                    foreach($shows as $key=>$value)
                    {
                        if($key != $k)
                        {
                            echo $value;
                        }
                        else if($k == $key)
                        {
                            echo "<li><a href='javascript:void(0)'>".$show."天</a></li>";
                        }
                    }
                    ;
                    ?>
                </ul>
                <ul class="page" style="width: 350px; margin: auto">
                    <?php
                    $pageshow = "<li><a href='javascript:void(0)' id='tolerate'>第".$page."页"."</a></li>";

                    if($userid == "")
                    {
                        $pages = [
                            "<li><a href='?page={$pagea}&show={$show}'>上一页</a></li>",
                            "<li><a href='?page={$pageb}&show={$show}'>下一页</a></li>"
                        ];
                    }
                    else
                    {
                        $pages = [
                            "<li><a href='?page={$pagea}&show={$show}&userid={$userid}'>上一页</a></li>",
                            "<li><a href='?page={$pageb}&show={$show}&userid={$userid}'>下一页</a></li>"
                        ];
                    }
                    if($page == 1)
                    {
                        echo $pageshow,$pages[1];
                    }
                    else
                    {
                        echo $pages[0],$pageshow,$pages[1];
                    }
                    ?>
                </ul>
            </div>
			<table width="65%" border="1" cellspacing="0" cellpadding="0" class="chart_h">
				<thead>
                    <tr>
                        <td colspan="15" class="tou">员工考勤表</td>
                    </tr>
                    <tr>
                        <th>ID</th>
                        <th>考勤组ID</th>
                        <th>排班ID</th>
                        <th>打卡记录ID</th>
                        <th>用户ID</th>
                        <th>用户名</th>
                        <th>考勤类型</th>
                        <th>时间结果</th>
                        <th>位置结果</th>
                        <th>数据来源</th>
                        <th>审批结果</th>
                        <th>审批id</th>
                        <th>工作日</th>
                        <th>基准时间</th>
                        <th>实际打卡时间</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($result as $value)
                {
                    echo "<tr>";
                    $baseCheckTime = "<td>".date("Y-m-d H:i",($value->baseCheckTime)/1000)."</td>";//1基准时间


                    switch(strtoupper($value->checkType))//2考勤类型
                    {
                        case "ONDUTY":
                            $checkType = "<td>上班</td>";
                            break;
                        case "OFFDUTY":
                            $checkType = "<td>下班</td>";
                            break;
                        default :
                            $checkType = "<td>无结果</td>";
                            break;
                    }


                    $corpId = "<td>".$value->corpId."</td>";//3企业ID
                    $groupId = "<td>".$value->groupId."</td>";//4考勤组ID
                    $id = "<td>".$value->id."</td>";//5唯一标示ID


                    switch(strtoupper($value->locationResult))//6位置结果
                    {
                        case "NORMAL":
                            $locationResult = "<td>范围内</td>";
                            break;
                        case "OUTSIDE":
                            $locationResult = "<td>范围外</td>";
                            break;
                        case "NOTSIGNED":
                            $locationResult = "<td>未打卡</td>";
                            break;
                        default :
                            $locationResult = "<td>无结果</td>";
                            break;
                    }


                    $planId = "<td>".$value->planId."</td>";//7排班ID


                    if(empty($value->recordId))//8打卡记录ID
                    {
                        $recordId = "<td>无结果</td>";
                    }else
                    {
                        $recordId = "<td>".$value->recordId."</td>";
                    }


                    switch(strtoupper($value->timeResult))//9时间结果
                    {
                        case "NORMAL";
                            $timeResult = "<td>正常</td>";
                            break;
                        case "EARLY":
                            $timeResult = "<td>早退</td>";
                            break;
                        case "LATE":
                            $timeResult = "<td>迟到</td>";
                            break;
                        case "SERIOUSLATE":
                            $timeResult = "<td>严重迟到</td>";
                            break;
                        case "NOTSIGNED":
                            $timeResult = "<td>未打卡</td>";
                            break;
                        default :
                            $timeResult = "<td>无结果</td>";
                            break;
                    }


                    $userCheckTime = "<td>".date("Y-m-d H:i:s",($value->userCheckTime)/1000)."</td>";//10实际打卡时间


                    $userId = "<td>".$value->userId."</td>";


                    foreach($userInfo as $val)//11用户ID
                    {
                        if($val['userid'] == $value->userId)
                        {
                            $userName = "<td>{$val['name']}</td>";
                            break;
                        }
                    }


                    $workDate = "<td>".date("Y-m-d 星期w",($value->workDate)/1000)."</td>";//12工作日



                    switch(strtoupper($value->sourceType))//13数据来源
                    {
                        case "ATM":
                            $sourceType = "<td>考勤机</td>";
                            break;
                        case "USER":
                            $sourceType = "<td>用户打卡</td>";
                            break;
                        case "BOSS":
                            $sourceType = "<td>老板改签</td>";
                            break;
                        case "APPROVE":
                            $sourceType = "<td>审批系统</td>";
                            break;
                        case "RECHECK":
                            $sourceType = "<td>重新计算</td>";
                            break;
                        case "SYSTEM":
                            $sourceType = "<td>考勤系统</td>";
                            break;
                        default :
                            $sourceType = "<td>无结果</td>";
                            break;
                    }


                    switch(strtoupper($value->approveResult))//14打卡审批结果
                    {
                        case "LEAVE":
                            $approveResult = "<td>(1, “请假”)</td>";
                            break;
                        case "GOOUT":
                            $approveResult = "<td>(3, “外出”)</td>";
                            break;
                        case "BUSINESSTRIP":
                            $approveResult = "<td>(2, “出差”)</td>";
                            break;
                        case "FREEATTEND":
                            $approveResult = "<td>(6, “免打卡”)</td>";
                            break;
                        default :
                            $approveResult = "<td>无结果</td>";
                            break;
                    }


                    if(empty($value->approveId))//15关联的审批id
                    {
                        $approveId = "<td>无结果</td>";
                    }else
                    {
                        $approveId = "<td>".$value->approveId."</td>";
                    }

                    echo
                    $id,
                    $groupId,
                    $planId,
                    $recordId,
                    $userId,
                    $userName,
                    $checkType,
                    $timeResult,
                    $locationResult,
                    $sourceType,
                    $approveResult,
                    $approveId,
                    $workDate,
                    $baseCheckTime,
                    $userCheckTime;

                    echo "</tr>";
                }
                ?>
                </tbody>
			</table>
        </div>
	</body>
</html>
