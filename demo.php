<?php
/**
 * AES128¼Ó½âÃÜÀà
 * @author dy
 *
 */
define("SUITE_KEY","suitegjgug0ptwfmd62z8");
//$eee = "fR6q7k8fdFYJWnS0Mt3osnN9j4WNLnhEPSBQlsUoZq1Uaz0476W103MZHlwAwTwHdeuUV1dcuNXcPV0CpfSHmmHsJErCcPndlnOf5krWwAEoHmzs6QI0FZg4mT9lmhsKLfR40YpCVd89ZyaxLhbABYPr05IiLSya190WhT5dyi/XybRNylRLOKyx8T4rIjmNUW1B+bscGfjRsQGq6Mg19w==";
//$eee = "6GKfVKgjQpKgsyVDJPej31RlUsGn6+3BCsfnr9TC2ZDG3DhtJz9V2dqRehA120ugcRvLvkcBoEIweqBxPAHp+UF6CGBI+O9ab1qxNpaT/PB2bQ4ugjMUzWy7kUVu9ccCKrtxsAlPPX2+jGKd/+Mkn1mJeqzwWcshPyFZlKGukRhbrF/gyNJx4obkqgS7IV1mqqc4MSAx+dIQEMCgpV/wFw==";
//$eee = "p+JW12HVcUrYf9Q1SA0mBvwpYuU1xl9pZQ91TXhaKPUHFyx0S3O4zd80i4627fKEgfM8x7MFW++ExgVnFxvQLw==";
//$eee = "D\/KHkUOsRP5az2ufHW\/hyd6QAy0cBPgw\/U7JNkXztTxfuYIyndQ5SOCohRSD2MYyx5q5DUGFCZtkcFxB3yN7Ew==";
//$eee = "cG45/GFhCQneturbhLOHYj9WdXoguf2IT0xV455RZ/EVWptzfChFOes+hP8cHIIrNX6+IK1bv2aX/G2J+PvW+0fnX60yURg1JKpwzivzdiPghrzajBLRxdZGq6mSTlsdtj6+VM3pbCgnGomq2YGBzpmPybroBdOUW04GOkR2uvY3+ZiaDNZ9yxptGKphauNQIATA6FeOv9KpEfGUWgKBbLaGHACmlGJQ98tjH1bJ/Si5ctXnsbE/pLnXqKdU+3tXI+/KoofEFXTJARbZXahnoCneA4fiNbY3UTM4+qsYGNafoaShqKsGI7fHdkj3Rw7YiP2AXAJMyTNf6i+HK6MJWg==";
$eee = "ofonSZEBbY9YlUbhpmo+voiITSWWoIt3+xbiKvP+XQv8sRPHFR8Culzn9h6q\/K6ws4ixLwCWlOs+7RYs5tOh6w==";
//$eee = "fzEsP8mTDSUAa6tnZk55ag==";
$eee = base64_decode($eee);
$key = base64_decode("9wl3am20zw7435pr94dyy40f95s2p7gcamnragd3icx=");
$iv = substr($key,0,16);
$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
mcrypt_generic_init($module, $key, $iv);
$decrypted = mdecrypt_generic($module, $eee);
mcrypt_generic_deinit($module);
mcrypt_module_close($module);
$pad = ord(substr($decrypted, -1));
if ($pad < 1 || $pad > 32) {
    $pad = 0;
}
$decrypted =  substr($decrypted, 0, (strlen($decrypted) - $pad));
$decrypted = substr($decrypted,20);
$decrypted = substr($decrypted,0,strlen($decrypted)-strlen(SUITE_KEY));
echo $decrypted;
$aa = base64_decode("fzEsP8mTDSUAa6tnZk55ag==");
echo $aa;
//echo gettype($decrypted);

//$res = json_encode(array("Content-type: text/xml"));

//var_dump($res);

//var_dump(json_decode($res));


//var_dump(array("Content-Type:application/json"));

/*if (1479444934 < time())
{
    echo 1;
}
else
{
    echo 2;
}*/
//$obj = json_decode($decrypted);

/*
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

//$Random = $obj->Random;
$Random = "PhZ3xi4t";
$TestSuiteKey = SUITE_KEY;

$rr = getRandomStr();

$text = $rr . pack("N", strlen($Random)) . $Random . $TestSuiteKey;

$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
$iv = substr($key, 0, 16);

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
echo $encrypted;*/