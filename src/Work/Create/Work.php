<?php


namespace Orcid\Work\Create;

use DOMDocument;
use DOMElement;
use DOMNode;
use Orcid\Work\OAwork;
use Orcid\Work\ExternalId;

class Work extends OAwork
{
    const FULL_NAME = 'fullName';
    const ORCID_ID = 'orcidID';
    const SEQUENCE = 'sequence';
    const HOSTNAME  = 'orcid.org';

    public static $namespaceWork= "http://www.orcid.org/ns/work";
    public static $namespaceCommon = "http://www.orcid.org/ns/common";
    public static $namespacebulk ="http://www.orcid.org/ns/bulk";

    /**
     * @var string
     */
    protected $journalTitle;
    /**
     * @var string
     */
    protected $shortDescription;
    /**
     * @var string
     */
    protected $citation;


    /**
     * @var string
     */
    protected $authors=[];
    /**
     * @var string
     */
    protected $principalAuthors;
    /**
     * @var string
     */
    protected $languageCode;
    /**
     * @var string
     */
    protected $citationType='formatted-unspecified';

    /**
     * @var string
     */
    protected $country;


    public function __construct()
    {
    }

    /**
     * @param string $authors
     * @return \Orcid\Work
     */
    public function addAuthor(string $fullName,string $orcidID='',string $sequence='')
    {
        $this->authors []= [self::FULL_NAME =>$fullName, self::ORCID_ID =>$orcidID, self::SEQUENCE =>$sequence];
        return $this;
    }


    /**
     * @param string $citation
     * @param string $citationType
     * @return $this
     */
    public function setCitation($citation, string $citationType='formatted-unspecified')
    {
        if(!empty($citation)){
            $this->citation = $citation;
            $this->citationType=$citationType;
        }
        return $this;
    }

    /**
     * @param string $journalTitle
     * @return $this
     */
    public function setJournalTitle(string $journalTitle)
    {
        $this->journalTitle = $journalTitle;
        return $this;
    }


    /**
     * @param string $shortDescription
     * @return $this
     */
    public function setShortDescription(string $shortDescription)
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    /**
     * @param string $languageCode
     * @return $this
     */
    public function setLanguageCode(string $languageCode)
    {
        $this->languageCode = $languageCode;
        return $this;
    }

    /**
     * @param string $principalAuthors
     */
    public function setPrincipalAuthors(string $principalAuthors)
    {
        $this->principalAuthors = $principalAuthors;
        return $this;;
    }

    /**
     * @param string $citationType
     */
    public function setCitationType(string $citationType)
    {
        if (!empty($citationType)) {
            $this->citationType = $citationType;
        }
    }


    /**
     * @param string $country
     */
    public function setCountry(string $country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getPutCode()
    {
        return $this->putCode;
    }

    /**
     * @param DOMDocument $dom
     * @param DOMNode $work
     * @return DOMNode
     */
    public function addMetaToWorkNode (DOMDocument $dom,DOMNode $work)
    {
        $this->checkMetaValueAndThrowExceptionIfNecessary();
        if( isset($this->putCode)){
            $work->setAttribute("put-code", (int)$this->putCode  );
        }
        $workTitle = $work->appendChild( $dom->createElementNS(self::$namespaceWork, "title") );
        $title = $workTitle->appendChild( $dom->createElementNS(self::$namespaceCommon, "title") );
        $title->appendChild( $dom->createCDATASection( $this->title ) ) ;

        if(isset($this->translatedTitle) && isset($this->translatedTitleLanguageCode)){
            $translatedTitle=  $workTitle->appendChild( $dom->createElementNS(self::$namespaceCommon, "translated-title") );
            $translatedTitle->setAttribute('language-code',$this->translatedTitleLanguageCode);
        }

        if(isset($this->subTitles)){
            $subtitle = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon,"subtitle") );
            foreach ($this->subTitles as $subTitle){
                $subtitle->appendChild( $dom->createCDATASection($subTitle) ) ;
            }
        }

        if(isset($this->journalTitle)){
            $journalTitle = $work->appendChild( $dom->createElementNS(self::$namespaceWork,"journal-title") );
            $journalTitle->appendChild( $dom->createCDATASection( $this->journalTitle) );
        }

        if(isset($this->shortDescription)){
            $shortDescription = $work->appendChild( $dom->createElementNS(self::$namespaceWork,"short-description") );
            $shortDescription->appendChild( $dom->createCDATASection($this->shortDescription ) ) ;
        }

        if(isset($this->citation)){
            $work->appendChild( $this->nodeCitation($dom,$this->citationType,$this->citation));
        }

        $work->appendChild( $dom->createElementNS(self::$namespaceWork, "type", $this->type) );

        if(isset($this->publicationDate)){
            $year=$this->publicationDate['year'];
            $month =$this->publicationDate['month'];
            $day=$this->publicationDate['day'];
            $work->appendChild($this->dateNode($dom,$year,$month,$day));
        }

        //rajout des identifiants externes
        $externalIds = $work->appendChild( $dom->createElementNS(self::$namespaceCommon, "external-ids" ) );

        if(isset($this->externals)){

            foreach ($this->externals as $externalId){
                /**
                 * @var ExternalId $externalId
                 */
                $idType=$externalId->getIdType();
                $idValue=$externalId->getIdValue();
                $idUrl=$externalId->getIdUrl();
                $relationship=$externalId->getIdRelationship();
                $externalIds->appendChild( $this->externalIdNode($dom, $idType, $idValue,$idUrl,$relationship)) ;

            }

        }

       //rajout des auteurs
        if(isset($this->authors)&& is_array($this->authors)){
            $contributors = $work->appendChild( $dom->createElementNS(self::$namespaceWork,"contributors") );
            foreach($this->authors as $author){
                $contributors->appendChild( $this->nodeContributor($dom,$author[self::FULL_NAME], "author",$author[self::ORCID_ID],$author[self::SEQUENCE]) );
            }
        }
        if(isset($this->principalAuthors)&&is_array($this->principalAuthors)){
            foreach($this->principalAuthors as $name){
                $contributors->appendChild( $this->nodeContributor($dom, $name, "principal-investigator") );
            }
        }

        if(isset($this->languageCode))
        {
            $work->appendChild( $dom->createElementNS(self::$namespaceCommon, "language-code",$this->languageCode ) );
        }
        if(isset($this->country))
        {
            $work->appendChild( $dom->createElementNS(self::$namespaceCommon, "country",$this->country ) );
        }
        return $work;
    }
    /**
     * construction d'un noeud d'identifiant externe
     * @param DOMDocument $dom
     * @param $type
     * @param $value
     * @param $relationship
     * @param string $url
     * @return DOMNode
     */

    protected function externalIdNode(DOMDocument $dom, $type, $value, $url="",$relationship)
    {
        $externalId = $dom->createElementNS(self::$namespaceCommon, "external-id");
        $externalId->appendChild( $dom->createElementNS(self::$namespaceCommon,"external-id-type", $type) );
        $externalId->appendChild( $dom->createElementNS(self::$namespaceCommon, "external-id-value", $value ) );
        if(!empty($url)){
            $externalId->appendChild( $dom->createElementNS(self::$namespaceCommon, "external-id-url", $url ));
        }
        $externalId->appendChild( $dom->createElementNS(self::$namespaceCommon,"external-id-relationship",$relationship) );
        return $externalId ;
    }

    /**
     * construction d'un noeud d'autheur
     * @param DOMDocument $dom
     * @param $name
     * @param $role
     * @return DOMNode
     */
    protected function nodeContributor(DOMDocument $dom, string $name, string $role,string $orcidID='',string $sequence='')
    {
        $contributor = $dom->createElementNS(self::$namespaceWork, "contributor");
        if(!empty($orcidID)){
            $contributorOrcid=$contributor->appendChild($dom->createElementNS(self::$namespaceWork,"contributor-orcid"));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceWork,"uri",'https://'.self::HOSTNAME.'/'.$orcidID));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceWork,"path",$orcidID));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceWork,"host",self::HOSTNAME));
        }
        $creditName = $contributor->appendChild( $dom->createElementNS(self::$namespaceWork,"credit-name"));
        $creditName->appendChild( $dom->createCDATASection( $name ) ) ;
        $attributes = $contributor->appendChild( $dom->createElementNS( self::$namespaceWork,"contributor-attributes" ));
        $attributes->appendChild($dom->createElementNS( self::$namespaceWork , "contributor-role", $role) );
        if(!empty($sequence)){
            $attributes->appendChild($dom->createElementNS( self::$namespaceWork , "contributor-sequence", $sequence));
        }
        return $contributor ;
    }

    /**
     * construction d'un noeud de citation
     * @param DOMDocument $dom
     * @param $type
     * @param $value
     * @return DOMElement
     */
    protected function nodeCitation(DOMDocument $dom, $type, $value){

        $citation = $dom->createElementNS(self::$namespaceWork, "citation");
        if($type!==''){
            $citation->appendChild($dom->createElementNS(self::$namespaceWork, "citation-type",$type));
        }

        $citation->appendChild($dom->createElementNS(self::$namespaceWork, "citation-value",$value));
        return $citation;
    }

    /**
     * construction d'un noeud de date
     * @param DOMDocument $dom
     * @param $year
     * @param string $month
     * @param string $day
     * @return DOMNode
     */
    protected function dateNode(DOMDocument $dom, $year, $month='', $day=''): DOMNode
    {
        $valiDate=1;
        $publicationDate =  $dom->createElementNS(self::$namespaceCommon, "publication-date");

        if (strlen((string)$month) === 1) {
            $month = '0' . $month;
        }
        if (strlen((string)$day )=== 1) {
            $day =  '0' . $day;
        }

        if(strlen((string)$year)===4){
            $publicationDate->appendChild( $dom->createElementNS(self::$namespaceCommon, "year", (int)$year ) );
            $valiDate++;
        }

        if($month!==''&&(int)$month>0 &&(int)$month<13 && $valiDate>1) {
            $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, "month", (string)$month));
            $valiDate++;
        }

        if($day!==''&&(int)$day>0 &&(int)$day<32 && $valiDate>2)  {
            $publicationDate->appendChild( $dom->createElementNS(self::$namespaceCommon, "day", (string)$day ) );
        }
        return  $publicationDate ;
    }



    /**
     * @param Work $work
     * @return DOMDocument
     */
    public function getXMLData()
    {
        $dom = self::getNewOrcidCommonDomDocument();
        $workNode= $dom->appendChild($dom->createElementNS(self::$namespaceWork,"work:work"));
        $dom->createAttributeNS(self::$namespaceCommon, "common:common");
        $workNode->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:schemaLocation", self::$namespaceWork . "/ work-2.0.xsd ");
        $this->addMetaToWorkNode($dom,$workNode);
        return $dom->saveXML() ;
    }

    /**
     * @param bool $preserveWhiteSpace
     * @param bool $formatOutput
     * @return DOMDocument
     */
    public static function getNewOrcidCommonDomDocument(bool $preserveWhiteSpace=false,bool $formatOutput=true){
        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->preserveWhiteSpace = $preserveWhiteSpace;
        $dom->formatOutput = $formatOutput;
        return $dom;
    }


    public function checkMetaValueAndThrowExceptionIfNecessary()
    {
        $reponse="";
        if(empty($this->title)) {
            $reponse .=" Echec récupération du titre";
        }
        if(empty($this->type)) {
            $reponse .=" Echec récupération du type de travail";
        }

        if(empty($this->externals)) {
            $reponse .=" Echec récupération d'un identifiant externe";
        }
        if($reponse!==""){
            throw new \Exception($reponse);
        }
    }

}