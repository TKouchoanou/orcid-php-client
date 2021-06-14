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
use Orcid\Work\OAwork;
use Orcid\Work\ExternalId;

class Work extends OAwork
{
    public const FULL_NAME    = 'fullName';
    public const ORCID_ID     = 'orcidID';
    public const ROLE         = 'role';
    public const SEQUENCE     = 'sequence';
    public const ORCID_ID_ENV = 'orcidIdEnv';
    public const HOSTNAME     = 'orcid.org';

    public static $namespaceWork= "http://www.orcid.org/ns/work";
    public static $namespaceCommon = "http://www.orcid.org/ns/common";
    public static $namespaceBulk ="http://www.orcid.org/ns/bulk";

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
     * @var string []
     */
    protected $authors;
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
    protected $citationType;

    /**
     * @var string
     */
    protected $country;
    /**
     * @var string
     */
    protected $workUrl;


    /**
     * An empty fullName string value will not be added
     * to be sure to add an author check on your side that his full name is not empty.
     * if you added the author orcid ID and is from sandBox put false
     * for the last parameter $orcidIdOfProductionEnv (his default value is true)
     * this value will be use if only you add orcid ID
     * by default you can put empty string for $role and $sequence
     * but in this case we will add author for empty role
     * and we will not add sequence to the sent data
     * example : $work->('authorName','','0000-1111-2222-3333','',false)
     * @param string $fullName
     * @param string $role
     * @param string $orcidID
     * @param string $sequence
     * @param bool $orcidFromProductionEnv
     * @return $this
     * @throws Exception
     */
    public function addAuthor(string $fullName, string $role='', string $orcidID='', string $sequence='', $orcidFromProductionEnv=true)
    {
        $orcid_id_env=$orcidFromProductionEnv ? '' : 'sandbox.';

        $role=empty($role) ? 'author' : self::tryToNormalizeAuthorRole($role);

        if (!in_array($role, self::AUTHOR_ROLE_TYPE)) {
            throw new Exception('The author '.$fullName.' role '.$role.' is not valid here are author valid role: ['.
                implode(",", self::AUTHOR_ROLE_TYPE)."]");
        }

        if (!empty($orcidID) && !preg_match("/(\d{4}-){3,}/", $orcidID)) {
            throw new Exception('The author '.$fullName.' Orcid '.$orcidID.' is not valid');
        }

        if (!empty($sequence) && !in_array(self::tryToNormalizeAuthorSequence($sequence), self::AUTHOR_SEQUENCE_TYPE)) {
            throw new Exception('The author '.$fullName.' sequence '.$sequence.' is not valid here are sequence valid values : ['
                .implode(",", self::AUTHOR_SEQUENCE_TYPE).']');
        }
        if (!empty($fullName)) {
            $this->authors []= [self::FULL_NAME =>$fullName, self::ROLE=>$role,self::ORCID_ID =>$orcidID,
                self::SEQUENCE =>self::tryToNormalizeAuthorSequence($sequence),self::ORCID_ID_ENV=>$orcid_id_env];
        }
        return $this;
    }

    /**
     * An empty string value will not be added
     * @param string $journalTitle
     * @return $this
     */
    public function setJournalTitle(string $journalTitle)
    {
        if (!empty($journalTitle)) {
            $this->journalTitle = $journalTitle;
        }
        return $this;
    }

    /**
     * An empty string value will not be added
     * @param string $shortDescription
     * @return $this
     * @throws Exception
     */
    public function setShortDescription(string $shortDescription)
    {
        if (mb_strlen($shortDescription)>5000) {
            throw new Exception('The short description length must not be than 5000 characters');
        } elseif (!empty($shortDescription)) {
            $this->shortDescription = $shortDescription;
        }
        return $this;
    }

    /**
     * an exception is thrown if you try to add invalid value
     * An empty string value will not be added
     * @param string $languageCode
     * @return $this
     * @throws Exception
     */
    public function setLanguageCode(string $languageCode)
    {
        if (!empty($languageCode)) {
            if (self::isValidLanguageCode($languageCode)) {
                $this->languageCode = self::tryToNormalizeLanguageCode($languageCode);
            } else {
                throw new Exception("Your language code is not valid. here are valid language code: [".implode(",", self::LANGAGE_CODES)."] ".
                   "if you want to set it by force use the method setPropertyByForce('property','value')");
            }
        }
        return $this;
    }

    /**
     * @param string | string[] $principalAuthors
     * @return $this
     */
    public function setPrincipalAuthors($principalAuthors)
    {
        if (!empty($principalAuthors)) {
            $this->principalAuthors = $principalAuthors;
        }
        return $this;
    }

    /**
     * An empty string value will not be added like citation
     * @param string $citation
     * @param string $citationType
     * @return $this
     * @throws Exception
     */
    public function setCitation(string $citation, $citationType='formatted-unspecified')
    {
        if (!empty($citation)) {
            $this->citation = $citation;
            if (empty($this->citationType)) {
                $this->setCitationType($citationType);
            }
        }
        return $this;
    }

    /**
     * 1- by default your citation type will be formatted-unspecified
     * if you add citation without citation-type.
     * 2-it makes no sense to add citation type without adding citation
     * @param string $citationType
     * @return $this
     * @throws Exception
     */
    public function setCitationType(string $citationType)
    {
        if (!empty($citationType)) {
            if (self::isValidCitationType($citationType)) {
                $this->citationType = self::tryToNormalizeCitationType($citationType);
            } else {
                throw new Exception("The citation format : ".$citationType."  is not valid here are the valid values : [".
                    implode(",", self::CITATION_FORMATS)."] if you want to set it by force use the method setPropertyByForce('property','value')");
            }
        }
        return $this;
    }


    /**
     * to be sure to add a country check on your side that it is not empty.
     * An empty string value will not be added
     * @param string $country
     * @return $this
     * @throws Exception
     */
    public function setCountry(string $country)
    {
        if (!empty($country)) {
            if (self::isValidCountryCode($country)) {
                $this->country = self::tryToNormalizeCountryCode($country);
            } else {
                throw new Exception("The country is not valid it must be a  code of  two characters and must respect ISO 3166 standard for country.".
                 " here are valid values : [" .implode(",", self::COUNTRY_CODES).
                 "] if you want to set it by force use the method setPropertyByForce('property','value')");
            }
        }
        return $this;
    }

    /**
     * to be sure to add a work url check on your side that it is not empty.
     * An empty string value will not be added
     * @param string $workUrl
     * @return $this
     */
    public function setWorkUrl(string $workUrl)
    {
        if (!empty($workUrl)) {
            $this->workUrl = $workUrl;
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return string
     */
    public function getCitationType()
    {
        return $this->citationType;
    }

    /**
     * @return string
     */
    public function getWorkUrl()
    {
        return $this->workUrl;
    }

    /**
     * @return string
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getJournalTitle()
    {
        return $this->journalTitle;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param DOMDocument $dom
     * @param DOMNode $work
     * @return DOMNode
     * @throws Exception
     */
    public function addMetaToWorkNode(DOMDocument $dom, DOMNode $work)
    {
        $this->checkMetaValueAndThrowExceptionIfNecessary();

        if (isset($this->putCode)) {
            $work->setAttribute("put-code", (int)$this->putCode);
        }

        //add work title
        $workTitle = $work->appendChild($dom->createElementNS(self::$namespaceWork, "title"));
        $title = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon, "title"));
        $title->appendChild($dom->createCDATASection($this->title)) ;

        if (isset($this->subTitle)) {
            $subtitle = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon, "subtitle"));
            $subtitle->appendChild($dom->createCDATASection($this->subTitle));
        }

        //translatedTitleLanguageCode is required to send translatedTitle
        if (isset($this->translatedTitle) && isset($this->translatedTitleLanguageCode)) {
            $translatedTitle = $workTitle->appendChild($dom->createElementNS(self::$namespaceCommon, "translated-title"));
            $translatedTitle->appendChild($dom->createCDATASection($this->translatedTitle));
            $translatedTitle->setAttribute('language-code', $this->translatedTitleLanguageCode);
        }

        if (isset($this->journalTitle)) {
            $journalTitle = $work->appendChild($dom->createElementNS(self::$namespaceWork, "journal-title"));
            $journalTitle->appendChild($dom->createCDATASection($this->journalTitle));
        }

        if (isset($this->shortDescription)) {
            $shortDescription = $work->appendChild($dom->createElementNS(self::$namespaceWork, "short-description"));
            $shortDescription->appendChild($dom->createCDATASection($this->shortDescription)) ;
        }

        if (isset($this->citation)) {
            $work->appendChild($this->nodeCitation($dom, $this->citationType, $this->citation));
        }

        // add work Type
        $work->appendChild($dom->createElementNS(self::$namespaceWork, "type", $this->type));

        // add publication date
        if (isset($this->publicationDate)) {
            $year=$this->publicationDate[self::YEAR];
            $month =$this->publicationDate[self::MONTH];
            $day=$this->publicationDate[self::DAY];
            $work->appendChild($this->dateNode($dom, $year, $month, $day));
        }

        //add external ident
        $externalIds = $work->appendChild($dom->createElementNS(self::$namespaceCommon, "external-ids"));
        foreach ($this->externals as $externalId) {
            /**
             * @var ExternalId $externalId
             */
            $idType=$externalId->getIdType();
            $idValue=$externalId->getIdValue();
            $idUrl=$externalId->getIdUrl();
            $relationship=$externalId->getIdRelationship();
            $externalIds->appendChild($this->externalIdNode($dom, $idType, $idValue, $idUrl, $relationship)) ;
        }

        if (isset($this->workUrl)) {
            $work->appendChild($dom->createElementNS(self::$namespaceWork, "url", $this->workUrl));
        }

        //add authors
        if (isset($this->authors) || isset($this->principalAuthors)) {
            $contributors = $work->appendChild($dom->createElementNS(self::$namespaceWork, "contributors"));
            if (isset($this->authors) && is_array($this->authors)) {
                foreach ($this->authors as $author) {
                    $contributors->appendChild($this->nodeContributor($dom, $author[self::FULL_NAME], $author[self::ROLE], $author[self::ORCID_ID], $author[self::SEQUENCE], $author[self::ORCID_ID_ENV]));
                }
            }

            if (isset($this->principalAuthors) && is_array($this->principalAuthors)) {
                foreach ($this->principalAuthors as $name) {
                    $contributors->appendChild($this->nodeContributor($dom, $name, "principal-investigator"));
                }
            } elseif (isset($this->principalAuthors) && is_string($this->principalAuthors)) {
                $contributors->appendChild($this->nodeContributor($dom, $this->principalAuthors, "principal-investigator"));
            }
        }

        if (isset($this->languageCode)) {
            $work->appendChild($dom->createElementNS(self::$namespaceCommon, "language-code", $this->languageCode));
        }

        if (isset($this->country)) {
            $work->appendChild($dom->createElementNS(self::$namespaceCommon, "country", $this->country));
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
        $externalIdTypeNode=$dom->createElementNS(self::$namespaceCommon, "external-id-type");
        $externalIdTypeNodeValue=$dom->createTextNode($type);
        $externalIdTypeNode->appendChild($externalIdTypeNodeValue);
        $externalIdNode->appendChild($externalIdTypeNode);
        // Value Node
        $externalIdValueNode=$dom->createElementNS(self::$namespaceCommon, "external-id-value");
        $externalIdValueNodeValue=$dom->createTextNode($value) ;
        $externalIdValueNode->appendChild($externalIdValueNodeValue);
        $externalIdNode->appendChild($externalIdValueNode);

        if (!empty($url)) {
            //url Node
            $externalIdUrlNode=$dom->createElementNS(self::$namespaceCommon, "external-id-url");
            $externalIdUrlNodeValue=$dom->createTextNode($url);
            $externalIdUrlNode->appendChild($externalIdUrlNodeValue);
            $externalIdNode->appendChild($externalIdUrlNode);
        }

        $externalIdNode->appendChild($dom->createElementNS(self::$namespaceCommon, "external-id-relationship", $relationship));

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
    protected function nodeContributor(DOMDocument $dom, string $name, string $role, string $orcidID='', string $sequence='', string $orcidIdEnv='')
    {
        $contributor = $dom->createElementNS(self::$namespaceWork, "contributor");
        if (!empty($orcidID)) {
            $contributorOrcid=$contributor->appendChild($dom->createElementNS(self::$namespaceCommon, "contributor-orcid"));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon, "uri", 'http://'.$orcidIdEnv.self::HOSTNAME.'/'.$orcidID));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon, "path", $orcidID));
            $contributorOrcid->appendChild($dom->createElementNS(self::$namespaceCommon, "host", $orcidIdEnv.self::HOSTNAME));
        }
        $creditName = $contributor->appendChild($dom->createElementNS(self::$namespaceWork, "credit-name"));
        $creditName->appendChild($dom->createCDATASection($name)) ;
        $attributes = $contributor->appendChild($dom->createElementNS(self::$namespaceWork, "contributor-attributes"));
        if (!empty($sequence)) {
            $attributes->appendChild($dom->createElementNS(self::$namespaceWork, "contributor-sequence", $sequence));
        }
        $attributes->appendChild($dom->createElementNS(self::$namespaceWork, "contributor-role", $role));
        return $contributor ;
    }

    /**
     * built an citation node
     * @param DOMDocument $dom
     * @param string $type
     * @param string $value
     * @return DOMElement
     */
    protected function nodeCitation(DOMDocument $dom, string $type, string $value)
    {
        $citation = $dom->createElementNS(self::$namespaceWork, "citation");
        if ($type!=='') {
            $citation->appendChild($dom->createElementNS(self::$namespaceWork, "citation-type", $type));
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
        if (strlen($day)=== 1) {
            $day =  '0' . $day;
        }

        $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, "year", $year));

        if ($month!=='') {
            $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, "month", $month));
            $validDateMonth=true;
        }

        if ($day!==''&& $validDateMonth) {
            $publicationDate->appendChild($dom->createElementNS(self::$namespaceCommon, "day", $day));
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
        $workNode= $dom->appendChild($dom->createElementNS(self::$namespaceWork, "work:work"));
        $dom->createAttributeNS(self::$namespaceCommon, "common:common");
        $workNode->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:schemaLocation", self::$namespaceWork . "/ work-2.0.xsd ");
        $this->addMetaToWorkNode($dom, $workNode);
        return $dom->saveXML() ;
    }

    /**
     * @param bool $preserveWhiteSpace
     * @param bool $formatOutput
     * @return DOMDocument
     */
    public static function getNewOrcidCommonDomDocument($preserveWhiteSpace=false, $formatOutput=true)
    {
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
        if (empty($this->title)) {
            $response .=" Title recovery failed: Title value cannot be empty";
        }
        if (empty($this->type)) {
            $response .=" Work Type recovery failed: Type value cannot be empty";
        }
        if (empty($this->externals)) {
            $response .=" externals Ident recovery failed: externals values cannot be empty";
        }
        if ($response!=="") {
            throw new Exception($response);
        }
    }
}
