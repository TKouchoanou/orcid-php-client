<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Work\Read\Full;


use Exception;
use Orcid\Work\Data\Data\Contributor;
use Orcid\Work\Work\Create\AbstractWork;
use Orcid\Work\Work\Read\SingleRecord;

/**
 * Class Record
 * @package Orcid\Work\Read
 */
class Record extends AbstractWork implements SingleRecord
{
    /**
     * @var int
     */
    protected $lastModifiedDate;
    /**
     * @var string
     */
    protected $source;

    /**
     * @var int
     */
    protected $createdDate;
    /**
     * @var string
     */
    protected $visibility;
    /**
     * @var string
     */
    protected $path;


    /**
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param $lastModifiedDate
     * @return $this
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;
        return $this;
    }

    /**
     * @param  $createdDate
     * @return $this
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @param string $visibility
     * @return $this
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * @param string $path
     * @return Record
     */
    public function setPath($path)
    {
        $this->path = $path;
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
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return int
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
     /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param array $orcidFullRecord
     * @return $this
     * @throws Exception
     */
    public function buildRecord(array $orcidFullRecord){
        $createdDate=$orcidFullRecord['created-date']['value'];
        $lastModifiedDate=$orcidFullRecord['last-modified-date']['value'];
        $source=$orcidFullRecord['source']['source-name']['value'];
        $visibility=$orcidFullRecord['visibility'];
        $workPath=$orcidFullRecord['path'];
        
        $putCode=$orcidFullRecord['put-code'];
        $title=$orcidFullRecord['title']['title']['value'];
        $subTitle=isset($orcidFullRecord['title']['subtitle']['value'])?$orcidFullRecord['title']['subtitle']['value']:'';
        $translatedTitle=isset($orcidFullRecord['title']['translated-title']['value'])?$orcidFullRecord['title']['translated-title']['value']:'';
        $translatedTitleLanguageCode=isset($orcidFullRecord['title']['translated-title']['language-code'])?$orcidFullRecord['title']['translated-title']['language-code']:'';
        $languageCode=isset($orcidFullRecord['language-code'])?$orcidFullRecord['language-code']:'';
        $country = isset($orcidFullRecord['country']['value'])?$orcidFullRecord['country']['value']:'';
        
        $journalTitle=isset($orcidFullRecord['journal-title']['value']) ? $orcidFullRecord['journal-title']['value']:'';
        $shortDescription=isset($orcidFullRecord['short-description'])?$orcidFullRecord['short-description']:'';
        $citation=isset($orcidFullRecord['citation']['citation-value'])?$orcidFullRecord['citation']['citation-value']:'';
        $citationType=isset($orcidFullRecord['citation']['citation-type'])?$orcidFullRecord['citation']['citation-type']:'';
        $type=$orcidFullRecord['type'];
        $publicationDate=$orcidFullRecord['publication-date'];
        $pubYear=isset($publicationDate['year']['value'])?$publicationDate['year']['value']:'';
        $pubMonth=isset($publicationDate['month']['value'])?$publicationDate['month']['value']:'';
        $pubDay=isset($publicationDate['day']['value'])?$publicationDate['day']['value']:'';
        
        $workUrl=isset($orcidFullRecord['url']['value'])?$orcidFullRecord['url']['value']:'';
        try {
            $this->setFilterData(true)->setCreatedDate($createdDate)->setLastModifiedDate($lastModifiedDate)
                ->setSource($source)->setVisibility($visibility)->setPath($workPath)
                ->setPutCode($putCode)->setTitle($title)->setSubTitle($subTitle)
                ->setTranslatedTitle($translatedTitle)->setTranslatedTitleLanguageCode($translatedTitleLanguageCode)
                ->setCountry($country)->setJournalTitle($journalTitle)->setShortDescription($shortDescription)
                ->setCitation($citation)->setCitationType($citationType)->setType($type)->setLanguageCode($languageCode)
                ->setPublicationDate($pubYear, $pubMonth, $pubDay)->setWorkUrl($workUrl);
        } catch (Exception $e) {
            error_log("Panic in ".get_class($this)." : ".$e->getMessage());
        }

        $externalIds= $orcidFullRecord['external-ids']['external-id'];

        foreach( $externalIds as $externalId) {
            $relationType=isset($externalId['external-id-relationship'])?$externalId['external-id-relationship']:'';
            $url=isset($externalId['external-id-url']['value'])?$externalId['external-id-url']['value']:'';
            $idtype=$externalId['external-id-type'];
            $value=$externalId['external-id-value'];
            $this->addExternalIdent($idtype,$value,$url,$relationType);
        }

        $contributorArray=isset($orcidFullRecord['contributors']['contributor'])?$orcidFullRecord['contributors']['contributor']:[];

        foreach( $contributorArray as $contributor) {
            $orcidId=$contributor['contributor-orcid']["path"];
            $creditName=$contributor['credit-name']['value'];
            $email=isset($contributor['contributor-email'])?$contributor['contributor-email']:'';
            $sequence=$contributor['contributor-attributes']['contributor-sequence'];
            $role=$contributor['contributor-attributes']['contributor-role'];
            try {
                $this->addNewContributor(new Contributor($creditName,$role,$orcidId,$sequence,$email));
            } catch (Exception $e) {
                error_log("Panic in ".get_class($this)." : ".$e->getMessage());
            }
        }

         return $this;
    }
    /**
     * {
    "bulk" : [ {
    "work" : {
    "created-date" : {
    "value" : 1601653663226
    },
    "last-modified-date" : {
    "value" : 1601653663226
    },
    "source" : {
    "source-orcid" : null,
    "source-client-id" : {
    "uri" : "http://sandbox.orcid.org/client/APP-3TTGG2V0UU3CEEPL",
    "path" : "APP-3TTGG2V0UU3CEEPL",
    "host" : "sandbox.orcid.org"
    },
    "source-name" : {
    "value" : "HAL II"
    }
    },
    "put-code" : 1149829,
    "path" : null,
    "title" : {
    "title" : {
    "value" : "dfghdfgh"
    },
    "subtitle" : null,
    "translated-title" : null
    },
    "journal-title" : null,
    "short-description" : null,
    "citation" : {
    "citation-type" : "FORMATTED_UNSPECIFIED",
    "citation-value" : "Sarah Denoux. dfghdfgh. 2018. <a target=\"_blank\" href=\"http://halv3-local.ccsd.cnrs.fr/hal-01175840\">&#x27E8;hal-01175840&#x27E9;</a>"
    },
    "type" : "WORKING_PAPER",
    "publication-date" : {
    "year" : {
    "value" : "2018"
    },
    "month" : {
    "value" : "07"
    },
    "day" : {
    "value" : "12"
    },
    "media-type" : null
    },
    "external-ids" : {
    "external-id" : [ {
    "external-id-type" : "uri",
    "external-id-value" : "hal-01175840",
    "external-id-url" : {
    "value" : "http://halv3-local.ccsd.cnrs.fr/hal-01175840"
    },
    "external-id-relationship" : "SELF"
    } ]
    },
    "url" : {
    "value" : "http://halv3-local.ccsd.cnrs.fr/hal-01175840"
    },
    "contributors" : {
    "contributor" : [ {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Sarah Denoux"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    } ]
    },
    "language-code" : "fr",
    "country" : null,
    "visibility" : "PUBLIC"
    }
    }, {
    "work" : {
    "created-date" : {
    "value" : 1601653663381
    },
    "last-modified-date" : {
    "value" : 1601653663381
    },
    "source" : {
    "source-orcid" : null,
    "source-client-id" : {
    "uri" : "http://sandbox.orcid.org/client/APP-3TTGG2V0UU3CEEPL",
    "path" : "APP-3TTGG2V0UU3CEEPL",
    "host" : "sandbox.orcid.org"
    },
    "source-name" : {
    "value" : "HAL II"
    }
    },
    "put-code" : 1149830,
    "path" : null,
    "title" : {
    "title" : {
    "value" : "Influence of soil drying on leaf water potential, photosynthesis, stomatal conductance and growth in two black pine varieties Seedlings grown in the dry regime * Correspendence and reprints"
    },
    "subtitle" : null,
    "translated-title" : null
    },
    "journal-title" : null,
    "short-description" : null,
    "citation" : {
    "citation-type" : "FORMATTED_UNSPECIFIED",
    "citation-value" : "Sarah Denoux, Gérard Lévy, Gilbert Aussenac, François Lebourgeois, Bernard Clerc, et al.. Influence of soil drying on leaf water potential, photosynthesis, stomatal conductance and growth in two black pine varieties Seedlings grown in the dry regime * Correspendence and reprints. 2018. <a target=\"_blank\" href=\"http://halv3-local.ccsd.cnrs.fr/hal-01175871\">&#x27E8;hal-01175871&#x27E9;</a>"
    },
    "type" : "WORKING_PAPER",
    "publication-date" : {
    "year" : {
    "value" : "2018"
    },
    "month" : {
    "value" : "08"
    },
    "day" : {
    "value" : "27"
    },
    "media-type" : null
    },
    "external-ids" : {
    "external-id" : [ {
    "external-id-type" : "uri",
    "external-id-value" : "hal-01175871",
    "external-id-url" : {
    "value" : "http://halv3-local.ccsd.cnrs.fr/hal-01175871"
    },
    "external-id-relationship" : "SELF"
    } ]
    },
    "url" : {
    "value" : "http://halv3-local.ccsd.cnrs.fr/hal-01175871"
    },
    "contributors" : {
    "contributor" : [ {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Sarah Denoux"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Gérard Lévy"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Gilbert Aussenac"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "François Lebourgeois"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Bernard Clerc"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "François Willm"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    } ]
    },
    "language-code" : "fr",
    "country" : null,
    "visibility" : "PUBLIC"
    }
    }, {
    "work" : {
    "created-date" : {
    "value" : 1601653663384
    },
    "last-modified-date" : {
    "value" : 1601653663384
    },
    "source" : {
    "source-orcid" : null,
    "source-client-id" : {
    "uri" : "http://sandbox.orcid.org/client/APP-3TTGG2V0UU3CEEPL",
    "path" : "APP-3TTGG2V0UU3CEEPL",
    "host" : "sandbox.orcid.org"
    },
    "source-name" : {
    "value" : "HAL II"
    }
    },
    "put-code" : 1149831,
    "path" : null,
    "title" : {
    "title" : {
    "value" : "Influence of soil drying on leaf water potential, photosynthesis, stomatal conductance and growth in two black pine varieties Seedlings grown in the dry regime * Correspendence and reprints"
    },
    "subtitle" : null,
    "translated-title" : null
    },
    "journal-title" : null,
    "short-description" : null,
    "citation" : {
    "citation-type" : "FORMATTED_UNSPECIFIED",
    "citation-value" : "Sarah Denoux, François Lebourgeois, Gilbert Aussenac, Bernard Clerc, Gérard Lévy, et al.. Influence of soil drying on leaf water potential, photosynthesis, stomatal conductance and growth in two black pine varieties Seedlings grown in the dry regime * Correspendence and reprints. 2018. <a target=\"_blank\" href=\"http://halv3-local.ccsd.cnrs.fr/hal-01175872\">&#x27E8;hal-01175872&#x27E9;</a>"
    },
    "type" : "WORKING_PAPER",
    "publication-date" : {
    "year" : {
    "value" : "2018"
    },
    "month" : {
    "value" : "08"
    },
    "day" : {
    "value" : "27"
    },
    "media-type" : null
    },
    "external-ids" : {
    "external-id" : [ {
    "external-id-type" : "uri",
    "external-id-value" : "hal-01175872",
    "external-id-url" : {
    "value" : "http://halv3-local.ccsd.cnrs.fr/hal-01175872"
    },
    "external-id-relationship" : "SELF"
    } ]
    },
    "url" : {
    "value" : "http://halv3-local.ccsd.cnrs.fr/hal-01175872"
    },
    "contributors" : {
    "contributor" : [ {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Sarah Denoux"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "François Lebourgeois"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Gilbert Aussenac"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Bernard Clerc"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "Gérard Lévy"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    }, {
    "contributor-orcid" : null,
    "credit-name" : {
    "value" : "François Willm"
    },
    "contributor-email" : null,
    "contributor-attributes" : {
    "contributor-sequence" : null,
    "contributor-role" : "AUTHOR"
    }
    } ]
    },
    "language-code" : "fr",
    "country" : null,
    "visibility" : "PUBLIC"
    }
    } ]
    }
     */

}
