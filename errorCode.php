
<?php
/**
 * error code ˵��.
 * <ul>
 *    <li>-900004: encodingAesKey �Ƿ�</li>
 *    <li>-900005: ǩ����֤����</li>
 *    <li>-900006: sha��������ǩ��ʧ��</li>
 *    <li>-900007: aes ����ʧ��</li>
 *    <li>-900008: aes ����ʧ��</li>
 *    <li>-900010: suiteKey У�����</li>
 * </ul>
 */
class ErrorCode
{
    public static $OK = 0;

    public static $IllegalAesKey = 900004;
    public static $ValidateSignatureError = 900005;
    public static $ComputeSignatureError = 900006;
    public static $EncryptAESError = 900007;
    public static $DecryptAESError = 900008;
    public static $ValidateSuiteKeyError = 900010;
}
?>