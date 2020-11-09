<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Work\Read\Full;


use Exception;
use Orcid\Work\Data\Data\Citation;
use Orcid\Work\Data\Data\Contributor;
use Orcid\Work\Data\Data\ExternalId;
use Orcid\Work\Data\Data\PublicationDate;
use Orcid\Work\Data\Data\Title;
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
     * @var bool
     */
    protected $filter=true;


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
     * @param  $path
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
     * @param $orcidFullRecord
     */
    public static function loadInstanceFromOrcidArray($orcidFullRecord)
    {

        try {
            $fullRecord=new self();
            $fullRecord->setFilter();
            $createdDate=$orcidFullRecord['created-date']['value'];
            $lastModifiedDate=$orcidFullRecord['last-modified-date']['value'];
            $source=$orcidFullRecord['source']['source-name']['value'];
            $visibility=$orcidFullRecord['visibility'];
            $workPath=$orcidFullRecord['path'];
            $putCode=$orcidFullRecord['put-code'];
            $languageCode=isset($orcidFullRecord['language-code'])?$orcidFullRecord['language-code']:'';
            $country = isset($orcidFullRecord['country']['value'])?$orcidFullRecord['country']['value']:'';
            $journalTitle=isset($orcidFullRecord['journal-title']['value']) ? $orcidFullRecord['journal-title']['value']:'';
            $shortDescription=isset($orcidFullRecord['short-description'])?$orcidFullRecord['short-description']:'';
            $workUrl=isset($orcidFullRecord['url']['value'])?$orcidFullRecord['url']['value']:'';
            $type=$orcidFullRecord['type'];
            $titleArray=$orcidFullRecord['title'];
            $titles=Title::loadInstanceFromOrcidArray($titleArray);
            $citationArray =$orcidFullRecord['citation'];
            $citations=Citation::loadInstanceFromOrcidArray($citationArray);
            $publicationDate=isset($orcidFullRecord['publication-date'])?PublicationDate::loadInstanceFromOrcidArray($orcidFullRecord['publication-date']):null;
            $fullRecord->setCreatedDate($createdDate)->setLastModifiedDate($lastModifiedDate)
                ->setSource($source)->setVisibility($visibility)->setPath($workPath)
                ->setPutCode($putCode)->setTitles($titles)->setCitations($citations)
                ->setCountry($country)->setJournalTitle($journalTitle)->setShortDescription($shortDescription)
                ->setType($type)->setLanguageCode($languageCode)->setWorkUrl($workUrl);

            if(!empty($publicationDate)){
                $fullRecord->setPubDate($publicationDate);
            }

            $externalIds= $orcidFullRecord['external-ids']['external-id'];
            foreach( $externalIds as $externalId) {
                $newExternalId=ExternalId::loadInstanceFromOrcidArray($externalId);
                $fullRecord->addNewExternalIdent($newExternalId);
            }

            $contributorArray=isset($orcidFullRecord['contributors']['contributor'])?$orcidFullRecord['contributors']['contributor']:[];
            foreach( $contributorArray as $contributor) {
                $newContributor=Contributor::loadInstanceFromOrcidArray($contributor);
                $fullRecord->addNewContributor($newContributor);
            }

        } catch (Exception $e) {
            error_log("Panic in ".get_class($fullRecord)." : ".$e->getMessage());
        }
       return $fullRecord;
    }
}
