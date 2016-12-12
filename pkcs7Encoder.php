<?php

include_once "errorCode.php";


class PKCS7Encoder
{
    public static $block_size = 32;

    function encode($text)
    {
        $block_size = PKCS7Encoder::$block_size;
        $text_length = strlen($text);
        $amount_to_pad = PKCS7Encoder::$block_size - ($text_length % PKCS7Encoder::$block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = PKCS7Encoder::block_size;
        }
        $pad_chr = chr($amount_to_pad);
        $tmp = "";
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    function decode($text)
    {
        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > PKCS7Encoder::$block_size) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }

}


class Prpcrypt
{
    public $key;

    function Prpcrypt($k)
    {
        $this->key = base64_decode($k . "=");
    }

    public function encrypt($text, $corpid)
    {

        try {
            //���16λ����ַ�������䵽����֮ǰ
            $random = $this->getRandomStr();
            $text = $random . pack("N", strlen($text)) . $text . $corpid;
            // �����ֽ���
            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            //ʹ���Զ������䷽ʽ�����Ľ��в�λ���
            $pkc_encoder = new PKCS7Encoder;
            $text = $pkc_encoder->encode($text);
            mcrypt_generic_init($module, $this->key, $iv);
            //����
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            //print(base64_encode($encrypted));
            //ʹ��BASE64�Լ��ܺ���ַ������б���
            return array(ErrorCode::$OK, base64_encode($encrypted));
        } catch (Exception $e) {
            print $e;
            return array(ErrorCode::$EncryptAESError, null);
        }
    }

    public function decrypt($encrypted, $corpid)
    {

        try {
            $ciphertext_dec = base64_decode($encrypted);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            mcrypt_generic_init($module, $this->key, $iv);

            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return array(ErrorCode::$DecryptAESError, null);
        }


        try {
            //ȥ����λ�ַ�
            $pkc_encoder = new PKCS7Encoder;
            $result = $pkc_encoder->decode($decrypted);
            //ȥ��16λ����ַ���,�����ֽ����AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_corpid = substr($content, $xml_len + 4);
        } catch (Exception $e) {
            print $e;
            return array(ErrorCode::$DecryptAESError, null);
        }
        if ($from_corpid != $corpid)
            return array(ErrorCode::$ValidateSuiteKeyError, null);
        return array(0, $xml_content);

    }

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

}

?>