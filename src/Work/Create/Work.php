<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Create;

use DOMDocument;
use DOMElement;
use DOMNode;
use Exception;
use Orcid\Work\Contributor;
use Orcid\Work\ExternalId;

class Work extends CreateAbstractWork
{

    const HOSTNAME     = 'orcid.org';
    public static $namespaceWork= "http://www.orcid.org/ns/work";
    public static $namespaceCommon = "http://www.orcid.org/ns/common";
    public static $namespaceBulk ="http://www.orcid.org/ns/bulk";

    /**
     * @param DOMDocument $dom
     * @param DOMNode $work
     * @return DOMNode
     * @throws Exception
     */
    public function addMetaToWorkNode (DOMDocument $dom,DOMNode $work)
    {
        $this->checkMetaValueAndThrowExceptionIfNecessary();

        if( isset($this->putCode)){
            $work->setAttribute("put-code", (int)$this->putCode);
        }

        //add work title
        $workTitle = $work->appendChild( $dom->createElementNS(self::$namespaceWork, "title") );
        $title = $workTitle->appendChild( $dom->createElementNS(self::$namespaceCommon, "title") );
        $title->appendChild( $dom->createCDATASection( $this->title ) ) ;

        if(isset($this->subTitle)){
            $subtitle = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon,"subtitle") );
            $subtitle->appendChild($dom->createCDATASection($this->subTitle));
        }

        //translatedTitleLanguageCode is required to send translatedTitle
        if(isset($this->translatedTitle) && isset($this->translatedTitleLanguageCode)){
            $translatedTitle = $workTitle->appendChild( $dom->createElementNS(self::$namespaceCommon, "translated-title"));
            $translatedTitle->appendChild($dom->createCDATASection($this->translatedTitle));
            $translatedTitle->setAttribute('language-code',$this->translatedTitleLanguageCode);
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

        // add work Type
        $work->appendChild( $dom->createElementNS(self::$namespaceWork, "type", $this->type) );

        // add publication date
        if(isset($this->publicationDate)){
            $year=$this->publicationDate->getYear();
            $month =$this->publicationDate->getMonth();
            $day=$this->publicationDate->getDay();
            $work->appendChild($this->dateNode($dom,$year,$month,$day));
        }

        //add external ident
        $externalIds = $work->appendChild( $dom->createElementNS(self::$namespaceCommon, "external-ids" ) );
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

        if(isset($this->workUrl))
        {
            $work->appendChild( $dom->createElementNS(self::$namespaceWork, "url",$this->workUrl ) );
        }

       //add authors (contributor)
        if(isset($this->authors)){
            $contributors = $work->appendChild( $dom->createElementNS(self::$namespaceWork,"contributors") );
            if(isset($this->authors) && is_array($this->authors)){
                /**
                 * @var Contributor $author
                 */
                foreach($this->authors as $author){
                    $contributors->appendChild($this->nodeContributor($dom,$author->getCreditName(),$author->getRole(),$author->getOrcid(),$author->getSequence(),$author->getEnv()) );
                }
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
     * built an external identifier node
     * @param DOMDocument $dom
     * @param string $type
     * @param string $value
     * @param string $url
     * @param string $relationship
     * @return DOMNode
     */

    protected function externalIdNode(DOMDocument $dom, string $type, string $value, string $url="", string $relationship="self")
    {
        $externalIdNode = $dom->createElementNS(self::$namespaceCommon, "external-id");
        //Type Node
        $externalIdTypeNode=$dom->createElementNS(self::$namespaceCommon,"external-id-type");
        $externalIdTypeNodeValue=$dom->createTextNode($type);
        $externalIdTypeNode->appendChild($externalIdTypeNodeValue);
        $externalIdNode->appendChild( $externalIdTypeNode);
       // Value Node
        $externalIdValueNode=$dom->createElementNS(self::$namespaceCommon, "external-id-value");
        $externalIdValueNodeValue=$dom->createTextNode($value) ;
        $externalIdValueNode->appendChild($externalIdValueNodeValue);
        $externalIdNode->appendChild($externalIdValueNode);

        if(!empty($url)){
            //url Node
            $externalIdUrlNode=$dom->createElementNS(self::$namespaceCommon, "external-id-url" );
            $externalIdUrlNodeValue=$dom->createTextNode($url);
            $externalIdUrlNode->appendChild($externalIdUrlNodeValue);
            $externalIdNode->appendChild($externalIdUrlNode);
        }

        $externalIdNode->appendChild( $dom->createElementNS(self::$namespaceCommon,"external-id-relationship",$relationship) );

        return $externalIdNode ;
    }

    /**
     * built an author node
     * @param DOMDocument $dom
     * @param string $name
     * @param string $role
     * @param string $orcidID
     * @param string $sequence
     * @param string $orcidIdEnv
     * @return DOMNode
     */
    protected function nodeContributor(DOMDocument $dom, string $name, string $role, string $orcidID='', string $sequence='',string $orcidIdEnv='')
    {
        $contributor = $dom->createElementNS(self::$namespaceWork, "contributor");
        if(!empty($orcidID)){
            $contributorOrcid=$contributor->appendChild($dom->createElementNS(self::$namespaceCommon,"contributor-orcid"));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon,"uri",'http://'.$orcidIdEnv.self::HOSTNAME.'/'.$orcidID));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon,"path",$orcidID));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon,"host",$orcidIdEnv.self::HOSTNAME));
        }
        $creditName = $contributor->appendChild( $dom->createElementNS(self::$namespaceWork,"credit-name"));
        $creditName->appendChild( $dom->createCDATASection( $name ) ) ;
        $attributes = $contributor->appendChild( $dom->createElementNS( self::$namespaceWork,"contributor-attributes" ));
        if(!empty($sequence)){
            $attributes->appendChild($dom->createElementNS( self::$namespaceWork , "contributor-sequence", $sequence));
        }
        $attributes->appendChild($dom->createElementNS( self::$namespaceWork , "contributor-role", $role) );
        return $contributor ;
    }

    /**
     * built an citation node
     * @param DOMDocument $dom
     * @param string $type
     * @param string $value
     * @return DOMElement
     */
    protected function nodeCitation(DOMDocument $dom, string $type,string $value){

        $citation = $dom->createElementNS(self::$namespaceWork, "citation");
        if($type!==''){
            $citation->appendChild($dom->createElementNS(self::$namespaceWork, "citation-type",$type));
        }
        $citationValue=$dom->createElementNS(self::$namespaceWork, "citation-value");
        $citationValue->appendChild($dom->createTextNode($value));
        $citation->appendChild($citationValue);
        return $citation;
    }

    /**
     * built an date Node
     * @param DOMDocument $dom
     * @param string $year
     * @param string $month
     * @param string $day
     * @return DOMNode
     */
    protected function dateNode(DOMDocument $dom, string $year, string $month='', string $day='')
    {
        $validDateMonth=false;
        $publicationDate =  $dom->createElementNS(self::$namespaceCommon, "publication-date");

        if (strlen($month) === 1) {
            $month = '0' . $month;
        }
        if (strlen($day )=== 1) {
            $day =  '0' . $day;
        }

        $publicationDate->appendChild( $dom->createElementNS(self::$namespaceCommon, "year", $year ));

        if($month!=='') {
            $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, "month", $month));
            $validDateMonth=true;
        }

        if($day!==''&& $validDateMonth) {
            $publicationDate->appendChild( $dom->createElementNS(self::$namespaceCommon, "day", $day ) );
        }

        return  $publicationDate ;
    }


    /**
     * @return false|string
     * @throws Exception
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
    public static function getNewOrcidCommonDomDocument( $preserveWhiteSpace=false, $formatOutput=true){
        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->preserveWhiteSpace = $preserveWhiteSpace;
        $dom->formatOutput = $formatOutput;
        return $dom;
    }

    /**
     * @throws Exception
     */
    public function checkMetaValueAndThrowExceptionIfNecessary()
    {
        $response="";
        if(empty($this->title)) {
            $response .=" Title recovery failed: Title value cannot be empty";
        }
        if(empty($this->type)) {
            $response .=" Work Type recovery failed: Type value cannot be empty";
        }
        if(empty($this->externals)) {
            $response .=" externals Ident recovery failed: externals values cannot be empty";
        }
        if($response!==""){
            throw new Exception($response);
        }
    }

    /**
     * @return Work
     */
    public static function  getCreateWorkInstanceWithDataFilter(){
        return (new Work())->setFilterData(true);
    }
}
