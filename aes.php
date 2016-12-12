<?PHP
/**
 * AES���ܡ�������
 * @author hushangming
 *
 * �÷���
 * <pre>
 * // ʵ������
 * // ����$_bit����ʽ��֧��256��192��128��Ĭ��Ϊ128�ֽڵ�
 * // ����$_type������/���ܷ�ʽ��֧��cfb��cbc��nofb��ofb��stream��ecb��Ĭ��Ϊecb
 * // ����$_key����Կ��Ĭ��Ϊabcdefghijuklmno
 * $tcaes = new TCAES();
 * $string = 'laohu';
 * // ����
 * $encodeString = $tcaes->encode($string);
 * // ����
 * $decodeString = $tcaes->decode($encodeString);
 * </pre>
 */
class TCAES{
    private $_bit = MCRYPT_RIJNDAEL_128;
    private $_type = MCRYPT_MODE_CBC;
    //private $_key = 'abcdefghijuklmno0123456789012345';
    private $_key = 'abcdefghijuklmno'; // ��Կ
    private $_use_base64 = true;
    private $_iv_size = null;
    private $_iv = null;

    /**
     * @param string $_key ��Կ
     * @param int $_bit Ĭ��ʹ��128�ֽ�
     * @param string $_type ���ܽ��ܷ�ʽ
     * @param boolean $_use_base64 Ĭ��ʹ��base64���μ���
     */
    public function __construct($_key = '', $_bit = 128, $_type = 'ecb', $_use_base64 = true){
        // �����ֽ�
        if(192 === $_bit){
            $this->_bit = MCRYPT_RIJNDAEL_192;
        }elseif(128 === $_bit){
            $this->_bit = MCRYPT_RIJNDAEL_128;
        }else{
            $this->_bit = MCRYPT_RIJNDAEL_256;
        }
        // ���ܷ���
        if('cfb' === $_type){
            $this->_type = MCRYPT_MODE_CFB;
        }elseif('cbc' === $_type){
            $this->_type = MCRYPT_MODE_CBC;
        }elseif('nofb' === $_type){
            $this->_type = MCRYPT_MODE_NOFB;
        }elseif('ofb' === $_type){
            $this->_type = MCRYPT_MODE_OFB;
        }elseif('stream' === $_type){
            $this->_type = MCRYPT_MODE_STREAM;
        }else{
            $this->_type = MCRYPT_MODE_ECB;
        }
        // ��Կ
        if(!empty($_key)){
            $this->_key = $_key;
        }
        // �Ƿ�ʹ��base64
        $this->_use_base64 = $_use_base64;

        $this->_iv_size = mcrypt_get_iv_size($this->_bit, $this->_type);
        $this->_iv = mcrypt_create_iv($this->_iv_size, MCRYPT_RAND);
    }

    /**
     * ����
     * @param string $string �������ַ���
     * @return string
     */
    public function encode($string){
        if(MCRYPT_MODE_ECB === $this->_type){
            $encodeString = mcrypt_encrypt($this->_bit, $this->_key, $string, $this->_type);
        }else{
            $encodeString = mcrypt_encrypt($this->_bit, $this->_key, $string, $this->_type, $this->_iv);
        }
        if($this->_use_base64)
            $encodeString = base64_encode($encodeString);
        return $encodeString;
    }

    /**
     * ����
     * @param string $string �������ַ���
     * @return string
     */
    public function decode($string){
        if($this->_use_base64)
            $string = base64_decode($string);

        $string = $this->toHexString($string);
        if(MCRYPT_MODE_ECB === $this->_type){
            $decodeString = mcrypt_decrypt($this->_bit, $this->_key, $string, $this->_type);
        }else{
            $decodeString = mcrypt_decrypt($this->_bit, $this->_key, $string, $this->_type, $this->_iv);
        }
        return $decodeString;
    }

    /**
     * ��$stringת����ʮ������
     * @param string $string
     * @return stream
     */
    private function toHexString ($string){
        $buf = "";
        for ($i = 0; $i < strlen($string); $i++){
            $val = dechex(ord($string{$i}));
            if(strlen($val)< 2)
                $val = "0".$val;
            $buf .= $val;
        }
        return $buf;
    }

    /**
     * ��ʮ��������$stringת�����ַ���
     * @param stream $string
     * @return string
     */
    private function fromHexString($string){
        $buf = "";
        for($i = 0; $i < strlen($string); $i += 2){
            $val = chr(hexdec(substr($string, $i, 2)));
            $buf .= $val;
        }
        return $buf;
    }
}