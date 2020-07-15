<?php


namespace Orcid;

use Orcid\Work\Create\Works;
use Orcid\Work\Read\Records;
use XMLReader;
class OResponse
{
    const successCode=[200,201,202,203,204];
    
    protected $code;
    /**
     * @var string
     */
    protected $headers;
    protected $headerSize;
    protected $body='';
    protected $infos=[];
    protected $bodyInfos;
    protected $fullResponse;
    protected $developperMessage;
    protected $userMessage;
    protected $errorCode;
    protected $moreInfo;
    /**
     * @var Records
     */

    protected $workRecordList;



    public function __construct(string $fullResponse,array $responseInfos)
    {
        $this->fullResponse=$fullResponse;
        $this->infos=$responseInfos;
        $this->init();
    }
    private function init(){
        $this->headerSize=$this->getparamInfos('header_size',false);
        $this->code=$this->getparamInfos('http_code',false);
        $this->headers=substr($this->fullResponse, 0, $this->headerSize);
        $this->body=substr($this->fullResponse, $this->headerSize);
        $this->readedWorks=null;
        $jsonString=$this->body;
        //
        if(self::isXmlString($jsonString)){
            $xmlObject = simplexml_load_string($jsonString);
            $jsonString = json_encode($xmlObject);
        }
        $this->bodyInfos=json_decode($jsonString, true);
        $this->developperMessage=$this->getParamBodyInfos('developer-message','');
        $this->userMessage=$this->getParamBodyInfos('user-message','');
        $this->errorCode=$this->getParamBodyInfos('error-code','');
        $this->moreInfo=$this->getParamBodyInfos('more-info','');

    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    protected function getParamInfos(string $key,$default){
        return self::getParamValueByKey($this->infos,$key,$default) ;
     }

    protected function getParamBodyInfos(string $key,$default){
        return self::getParamValueByKey($this->bodyInfos,$key,$default) ;
    }

    private static function getParamValueByKey($array, string $key,$default){
        if(is_array($array)&&!empty($array) && array_key_exists($key,$array)){
            return $array[$key];
        }
        return $default;
    }


    /**
     * @return $this
     */
    protected function setWorkRecordList(){
        $workRecordsArray=null;
        $workRecords= new Records();
        try {
            $workRecordsArray=json_decode($this->getBody(),true);
        }catch (\Exception $e){
            return $this;
        }
        if(isset($workRecordsArray)
            && isset($workRecordsArray['last-modified-date'])
            && isset($workRecordsArray['group'])
            && isset($workRecordsArray['path'])){
            try {
                $workRecords->buildWorkRecords($workRecordsArray);
                $this->workRecordList=$workRecords;
            }catch (\Exception $e){
                echo $e;
                return $this;
            }
            return $this;
        }

    }

    /**
     * @return Records|null
     */
    public function getWorkRecordList()
    {
        if(empty($this->workRecordList)){
            $this->setWorkRecordList();
        }
        return $this->workRecordList;
    }

    /**
     * @return mixed
     */
    public function getBodyInfos()
    {
        return $this->bodyInfos;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getDevelopperMessage()
    {
        return $this->developperMessage;
    }

    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getFullResponse()
    {
        return $this->fullResponse;
    }

    /**
     * @return mixed
     */
    public function getUserMessage()
    {
        return $this->userMessage;
    }

    /**
     * @return array
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return mixed
     */
    public function getHeaderSize()
    {
        return $this->headerSize;
    }

    /**
     * @return mixed
     */
    public function getMoreInfo()
    {
        return $this->moreInfo;
    }

    /**
     * @return string
     */
    public function getReadedWorkXML()
    {
        if($this->isXmlString($this->getBody()) && empty($this->getErrorCode()))
        {return $this->body; }
        return '';
    }

    public function hasError(){
        return !empty($this->getErrorCode());
    }

    public function hasSuccess(){
        return in_array($this->code,self::successCode);
    }
    public function hasConflict(){
        return$this->code==409;
    }

    public function hasNotFound(){
        return$this->code==404;
    }

    /**
     * @param string $xmlString
     * @return false|int
     */
    private function isXmlString(string $xmlString){
        $regex="/<\?xml .+\?>/";
        return  preg_match($regex,$xmlString);

    }
}
