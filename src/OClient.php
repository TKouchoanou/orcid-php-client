<?php


namespace Orcid;


use Orcid\Http\Curl;
use Orcid\Work\Create\Work;
use Orcid\Work\Create\Works;

class OClient
{
    /**
     * @var Oauth
     */
    private $oauth = null;
    /**
     * @var Curl
     */
    private $http=null;

    public function __construct(Oauth $oauth)
    {
        $this->setOauth($oauth);
    }

    /**
     * @param Oauth $oauth
     */
    protected function setOauth(Oauth $oauth)
    {
        if (!$oauth->getAccessToken()) {
            throw new Exception('You must first set an access token or authenticate to exchange Work with ORCID');
        }
        $oauth->useMembersApi();
        $this->oauth = $oauth;
    }


    /**
     * @param bool $jsonformat
     * @return mixed
     */
    public function ReadSummary($jsonFormat=true)
    {
        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        $this->oauth->http->initialize(true);
       $response=  $this->oauth->http->setUrl($this->oauth->getApiEndpoint('works'))
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
       return  new OResponse($response,$infos);

    }

    /**
     * @param $putCode
     * @param bool $jsonformat
     * @return OResponse
     */
    public function ReadSingle($putCode,$jsonformat=true){
        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        $this->oauth->http->initialize(true);
      $response=  $this->oauth->http->setUrl($this->oauth->getApiEndpoint('work').'/'.$putCode)
            ->setHeader([
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken(),
                'Content-Type'  => $contentType
            ])->execute();
        $infos=$this->oauth->http->getResponseInfos();
        return  new OResponse($response,$infos);
    }

    /**
     * @param array $worksIdArray
     * @param bool $jsonformat
     * @return mixed
     * @throws Exception
     */
    public function ReadMultiple(array $worksIdArray,$jsonformat=true){
        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        if($worksIdArray->length<0){
            throw new Exception("the work id array must not be empity");
        }
        if($worksIdArray->length>50){
            throw new Exception("you can't read more than 50 Work yourwork id array length is more than 50");
        }

        $workList="";
        foreach ($worksIdArray as $workId){
            $workList.=(string)$workId.',';
        }
        $workList=rtrim($workList,',');
        $response=$this->oauth->http->initialize(true)
            ->setUrl($this->oauth->getApiEndpoint('works').'/'.$workList)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();
        $infos=$this->oauth->http->getResponseInfos();
        return  new OResponse($response,$infos);


    }

    /**
     * @param Work $work
     * @param bool $dataIsJsonFormat
     * @return OResponse
     */
    public function postOne(Work $work,$dataIsJsonFormat=true){
        $data=$work->getXMLData();
        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        $response= $this->oauth->http->initialize(true)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])
            ->setUrl($this->oauth->getApiEndpoint('work'))
            ->setPostData($data)
            ->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new OResponse($response,$infos);
    }

    /**
     * @param Works $works
     * @param bool $dataIsJsonFormat
     * @return OResponse
     */
    public function postMultiple(Works $works,$dataIsJsonFormat=true){
        $data=$works->getXMLData();
        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        $response=$this->oauth->http->initialize(true)
            ->setUrl($this->oauth->getApiEndpoint('works'))
            ->setPostData($data)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getInfos();
        return  new OResponse($response,$infos);
    }

    /**
     * @param Work $work
     * @param bool $dataIsJsonFormat
     * @return OResponse
     * @throws \Exception
     */
    public function update(Work $work,$dataIsJsonFormat=true){

        $putCode=$work->getPutCode();
        $data=$work->getXMLData();

        if(empty($putCode)||!is_numeric($putCode)){
            throw new \Exception("putcode must be numéric and not empity to update ORCID Work");
        }

        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        $response=$this->oauth->http->initialize(true)->setPut()
            ->setUrl($this->oauth->getApiEndpoint('work/'.$putCode))
            ->setPostData($data)
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new OResponse($response,$infos);
    }

    /**
     * @param $putCode
     * @param bool $dataIsJsonFormat
     * @return OResponse
     */
    public function delete($putCode,$dataIsJsonFormat=true){
        $contentType=$jsonFormat?'application/vnd.orcid+json':'application/vnd.orcid+xml';
        $response=$this->oauth->http->initialize(true)->setDelete()
            ->setUrl($this->oauth->getApiEndpoint('work/'.$putCode))
            ->setHeader([
                'Content-Type'  => $contentType,
                'Authorization' => 'Bearer ' . $this->oauth->getAccessToken()
            ])->execute();

        $infos=$this->oauth->http->getResponseInfos();
        return  new OResponse($response,$infos);

    }




}