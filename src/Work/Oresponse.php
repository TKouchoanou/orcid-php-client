<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work;

use Exception;
use Orcid\Work\Read\Records;

class Oresponse
{
    public const SUCCESS_CODE=[200,201,202,203,204];
    /**
     * @var int
     */
    protected $code;
    /**
     * @var string
     */
    protected $headers;
    /**
     * @var string
     */
    protected $headerSize;
    /**
     * @var string
     */
    protected $body='';
    /**
     * @var array
     */
    protected $infos=[];
    /**
     * @var array
     */
    protected $bodyInfos;
    /**
     * @var string
     */
    protected $fullResponse;
    /**
     * @var string
     */
    protected $developperMessage;
    /**
     * @var string
     */
    protected $userMessage;
    /**
     * @var string
     */
    protected $errorCode;
    /**
     * @var string
     */
    protected $moreInfo;
    /**
     * @var Records
     */

    protected $workRecordList;

    /**
     * Oresponse constructor.
     * @param string $fullResponse
     * @param array $responseInfos
     */
    public function __construct(string $fullResponse, array $responseInfos)
    {
        $this->fullResponse=$fullResponse;
        $this->infos=$responseInfos;
        $this->init();
    }

    /**
     *
     */
    private function init()
    {
        $this->headerSize=$this->getparamInfos('header_size', false);
        $this->code=$this->getparamInfos('http_code', false);
        $this->headers=substr($this->fullResponse, 0, $this->headerSize);
        $this->body=substr($this->fullResponse, $this->headerSize);
        $jsonString=$this->body;
        //
        if (self::isXmlString($jsonString)) {
            $xmlObject = simplexml_load_string($jsonString);
            $jsonString = json_encode($xmlObject);
        }
        $this->bodyInfos=json_decode($jsonString, true);
        $this->developperMessage=$this->getParamBodyInfos('developer-message', '');
        $this->userMessage=$this->getParamBodyInfos('user-message', '');
        $this->errorCode=$this->getParamBodyInfos('error-code', '');
        $this->moreInfo=$this->getParamBodyInfos('more-info', '');
        if (empty($this->developperMessage)&&!empty($this->getParamBodyInfos('error', ''))) {
            $this->developperMessage=$this->getParamBodyInfos('error', '');
        }
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    protected function getParamInfos(string $key, $default)
    {
        return self::getParamValueByKey($this->infos, $key, $default) ;
    }

    /**
     * @param string $key
     * @param $default
     * @return mixed
     */
    protected function getParamBodyInfos(string $key, $default)
    {
        return self::getParamValueByKey($this->bodyInfos, $key, $default) ;
    }

    private static function getParamValueByKey($array, string $key, $default)
    {
        if (is_array($array)&&!empty($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }
        return $default;
    }


    /**
     * @return $this
     */
    protected function setWorkRecordList()
    {
        $workRecordsArray=null;
        $workRecords= new Records();
        $this->workRecordList=$workRecords;
        try {
            $workRecordsArray=json_decode($this->getBody(), true);
        } catch (Exception $e) {
            error_log("Panic in ".get_class($this)." : ".$e->getMessage());
            return $this;
        }
        if (isset($workRecordsArray)
            && isset($workRecordsArray['last-modified-date'])
            && isset($workRecordsArray['group'])
            && isset($workRecordsArray['path'])) {
            $workRecords->buildWorkRecords($workRecordsArray);
        }
        return $this;
    }

    /**
     * @return Records
     */
    public function getWorkRecordList()
    {
        if (empty($this->workRecordList)) {
            $this->setWorkRecordList();
        }
        return $this->workRecordList;
    }

    /**
     * @return array|mixed
     */
    public function getSingleReadWork()
    {
        $workSingleReadWork=json_decode($this->getBody(), true);
        if ($workSingleReadWork) {
            return $workSingleReadWork;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getBodyInfos()
    {
        return $this->bodyInfos;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDevelopperMessage()
    {
        return $this->developperMessage;
    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        if (empty($this->errorCode) && !$this->hasSuccess()
            && !$this->hasConflict()) {
            return $this->code;
        }
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
     * @return string
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
     * @return string
     */
    public function getHeaderSize()
    {
        return $this->headerSize;
    }

    /**
     * @return string
     */
    public function getMoreInfo()
    {
        return $this->moreInfo;
    }

    /**
     * @return string
     */
    public function getReadWorkXML()
    {
        if (self::isXmlString($this->getBody()) && empty($this->getErrorCode())) {
            return $this->body;
        }
        return '';
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return !empty($this->getErrorCode());
    }

    /**
     * @return bool
     */
    public function hasSuccess()
    {
        return in_array($this->code, self::SUCCESS_CODE);
    }

    /**
     * @return bool
     */
    public function hasConflict()
    {
        return$this->code==409;
    }

    /**
     * @return bool
     */
    public function hasNotFound()
    {
        return$this->code==404;
    }

    /**
     * @param string $xmlString
     * @return false|int
     */
    private static function isXmlString(string $xmlString)
    {
        $regex="/<\?xml .+\?>/";
        return  preg_match($regex, $xmlString);
    }
}
