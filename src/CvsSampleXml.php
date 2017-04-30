<?php


namespace Simon801109\Cvs;


abstract class CvsSampleXml
{
    protected $xml;

    protected $url;

    /**
     * 初始化，參數send為送出件，back為重出件
     *
     *
     */
    public function __construct(string $type = "send")
    {
        if( $type === "send" ){
            $this->url = "https://cvsweb.cvs.com.tw/B2C_WS/Service_B2C_1.asmx?wsdl";
            $this->xml = file_get_contents(__Dir__.'/sample/cvs_example_one.xml');
        }else if($type === "back"){
            $this->url = 'https://cvsweb.cvs.com.tw/webservice/service.asmx?wsdl';
            $this->xml = file_get_contents(__Dir__.'/sample/cvs_example_back.xml');
        }
    }
    /**
     * 取得訂單格式
     *
     * @return String
     *
     */
    public function getXml()
    {
        return $this->xml;
    }
    /**
     * 設定傳送訂單內容
     * @param String $attribute
     * @param String $value
     *
     */
    public function setOption($attribute , $value)
    {
        $this->xml = str_replace($attribute, $value, $this->xml);
    }

}