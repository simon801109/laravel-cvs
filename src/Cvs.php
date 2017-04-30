<?php

namespace Simon801109\Cvs;

use SoapClient;
use stdClass;

class Cvs extends CvsSampleXml
{
    //FTP位址
    const FTP_URL = 'cvsftp.cvs.com.tw:8821';


    /**
     * 將訂單送出，回傳結果
     *
     * @return String
     *
     */
    public function send()
    {
        $client = new SoapClient($this->url);
        //傳遞資料給便利達康
        if($client){
            $parameters = new stdClass();
            $parameters->xmlStr = $this->getXml();
            $result = $client->ORDERS_ADD($parameters);
            return $result;
        }
        return false;
    }
    /**
     * 下載FTP端資料
     *
     * @param String $remote
     * @param String $local
     * @return String
     *
     */
    public function downloadData($remote, $local) {
        if ($fp = fopen($local, 'w')) {
            $ftp_server = 'ftps://' . self::FTP_URL . '/' . $remote;
            $ch = curl_init();
            $config = config('cvs');

            curl_setopt($ch, CURLOPT_URL, $ftp_server);
            curl_setopt($ch, CURLOPT_USERPWD, $config['FTP_ACCOUNT'] . ':' . $config['FTP_PASSWORD']);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
            curl_setopt($ch, CURLOPT_FTPSSLAUTH, CURLFTPAUTH_TLS);
            curl_setopt($ch, CURLOPT_UPLOAD, 0);
            curl_setopt($ch, CURLOPT_FILE, $fp);

            $result = curl_exec($ch);
            if (curl_error($ch)) {
                curl_close($ch);
                return false;
            } else {
                curl_close($ch);
                return $result;
            }
        }
        return false;
    }
    /**
     * 取得物流名稱
     *
     * @param String $cvs_code
     * @return array
     *
     */
    public static function getLogisticsName($cvs_code)
    {
        $logistics=['name'=>'','img'=>''];
        $data = substr($cvs_code,0,1);
        switch ($data) {
            case 'F':
                $logistics['name'] = '日翊物流';
                $logistics['img'] = 'TFM';
                break;
            case 'L':
                $logistics['name'] = '萊爾富物流';
                $logistics['img'] = 'TLF';
                break;
            case 'K':
                $logistics['name'] = '來來物流';
                $logistics['img'] = 'TOK';
                break;
        }
        return $logistics;
    }
    /**
     * 檢查是否有關閉超商
     *
     * @return array
     *
     */
    public static function checkCloseCvs()
    {
        $cvs_store_number = [];
        //下載店鋪檔
        $remote = 'F01/F01ALLCVS'.date('Ymd').'.xml';
        $local = config('cvs')['REAL_FILES_PATH'].'F01/F01ALLCVS'.date('Ymd').'.xml';
        Cvs::downloadData($remote ,$local);
        $data = str_replace('big5', 'UTF-8', file_get_contents($local));
        $data = mb_convert_encoding($data,"utf-8", "big5");
        $xml = simplexml_load_string($data);
        foreach ($xml->F01CONTENT as $child){
            array_push($cvs_store_number, (string)$child->STNO);
        }
       return $cvs_store_number;
    }
    /**
     * 轉換訂單為11碼訂單
     *
     * @param String $order_id
     * @return String
     *
     */
    public static function convertOrderId($order_id)
    {
        $check_1 = array_sum(str_split($order_id))%11;
        if($check_1 === 10){
            $check_1 = 1;
        }elseif($check_1 === 0){
            $check_1 = 0;
        }
        return $order_id.$check_1;
    }
}