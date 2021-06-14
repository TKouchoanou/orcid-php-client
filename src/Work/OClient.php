<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work;

use Exception;
use Orcid\Oauth;
use Orcid\Work\Create\Work;
use Orcid\Work\Create\Works;

class OClient
{
    /**
     * @var Oauth
     */
    private $oauth = null;

    /**
     * OClient constructor.
     * @param Oauth $oauth
     * @param bool $useMemberApi
     * @throws Exception
     */
    public function __construct(Oauth $oauth, $useMemberApi=true)
    {
        try {
            $useMemberApi ? $oauth->useMembersApi() : $oauth->usePublicApi();
            $this->setOauth($oauth);
        } catch (Exception $e) {
            error_log("Panic in ".get_class($this)." : ".$e->getMessage());
            throw $e;
        }
    }

    /**
     * @param Oauth $oauth
     * @throws Exception
     */
    protected function setOauth(Oauth $oauth)
    {
        if (!$oauth->getAccessToken()) {
            throw new Exception('You must first set an access token or authenticate to exchange Work with ORCID');
        }
        $this->oauth = $oauth;
    }


    /**
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     */
    public function ReadSummary($dataIsJsonFormat=true)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $this->oauth->http->initialize(true);
        $response=  $this->oauth->http->setUrl($this->oauth->getApiEndpoint('works'))
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }

    /**
     * @param int|string $putCode
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     */
    public function readSingle($putCode, $dataIsJsonFormat=true)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $this->oauth->http->initialize(true);
        $response=  $this->oauth->http->setUrl($this->oauth->getApiEndpoint('work').'/'.$putCode)
            ->setHeader([
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken(),
                'Content-Type'  => $contentType
            ])->execute();
        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }

    /**
     * @param array $worksIdArray
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     * @throws Exception
     */
    public function readMultiple(array $worksIdArray, $dataIsJsonFormat=true)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        if (empty($worksIdArray)) {
            throw new Exception("the work put-code array (worksIdArray) must not be empty");
        }
        if (count($worksIdArray)>50) {
            throw new Exception("you can't read more than 50 Work your work id array length is more than 50");
        }

        $workList="";
        foreach ($worksIdArray as $workId) {
            $workList.=(string)$workId.',';
        }
        $workList=rtrim($workList, ',');
        $response=$this->oauth->http->initialize(true)
            ->setUrl($this->oauth->getApiEndpoint('works').'/'.$workList)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();
        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }

    /**
     * @param int|string|array $putCode
     * @return Oresponse
     * @throws Exception
     */
    public function read($putCode)
    {
        if (is_array($putCode)) {
            return  $this->readMultiple($putCode);
        }
        return $this->readSingle($putCode);
    }


    /**
     * @param Work|Works|Work[] $works
     * @return Oresponse
     * @throws Exception
     */
    public function send($works)
    {
        if ($works instanceof Work) {
            $data=$works->getXMLData();
            return  $this->postOne($data);
        } elseif ($works instanceof works) {
            $data=$works->getXMLData();
            return  $this->postMultiple($data);
        } elseif (is_array($works)) {
            $newWorks=new Works();
            foreach ($works as $work) {
                if ($work instanceof Work) {
                    $newWorks->append($work);
                } else {
                    throw new Exception("All values of your array must be instance of Work");
                }
            }
            $data=$newWorks->getXMLData();
            return  $this->postMultiple($data);
        } else {
            throw new Exception("Orcid client Send Method parameter can  be :
             instance of Work, instance of works or array of work instance. Your work(s) type is not accepted ");
        }
    }

    /**
     * @param $data
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     */
    protected function postOne(string $data, $dataIsJsonFormat=false)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response= $this->oauth->http->initialize(true)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])
            ->setUrl($this->oauth->getApiEndpoint('work'))
            ->setPostData($data)
            ->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }

    /**
     * @param $data
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     */
    protected function postMultiple(string $data, $dataIsJsonFormat=false)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response=$this->oauth->http->initialize(true)
            ->setUrl($this->oauth->getApiEndpoint('works'))
            ->setPostData($data)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }

    /**
     * @param Work $work
     * @return Oresponse
     * @throws Exception
     */
    public function update(Work $work)
    {
        $putCode=$work->getPutCode();
        $data=$work->getXMLData();
        return$this->updateOneWork($data, $putCode);
    }

    /**
     * @param string $data //xml string
     * @param $putCode
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     */
    protected function updateOneWork(string $data, $putCode, $dataIsJsonFormat=false)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response=$this->oauth->http->initialize(true)->setPut()
            ->setUrl($this->oauth->getApiEndpoint('work/'.$putCode))
            ->setPostData($data)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }

    /**
     * @param $putCode
     * @param bool $dataIsJsonFormat
     * @return Oresponse
     */
    public function delete($putCode, $dataIsJsonFormat=true)
    {
        $contentType=$dataIsJsonFormat ? 'application/vnd.orcid+json' : 'application/vnd.orcid+xml';
        $response=$this->oauth->http->initialize(true)->setDelete()
            ->setUrl($this->oauth->getApiEndpoint('work/'.$putCode))
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new Oresponse($response, $infos);
    }
}
