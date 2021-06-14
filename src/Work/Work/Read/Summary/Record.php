<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Work\Read\Summary;

use Exception;
use Orcid\Work\Data\Data\ExternalId;
use Orcid\Work\Data\Data\PublicationDate;
use Orcid\Work\Data\Data\Title;
use Orcid\Work\Work\OAbstractWork;
use Orcid\Work\Work\Read\SingleRecord;

/**
 * Class Record
 * @package Orcid\Work\Read
 */
class Record extends OAbstractWork implements SingleRecord
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
     * @param $source
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
     * @param $visibility
     * @return $this
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * @param $path
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
     * @param $work
     * @return Record
     */
    public static function loadInstanceFromOrcidArray($work)
    {
        try {
            $newRecord= new Record();
            $summary=$work['work-summary'][0];
            $putCode=$summary['put-code'];
            $source=$summary['source']['source-name']['value'];
            $externalIdArray= $summary['external-ids']['external-id'];
            $lastUpdateDate=$summary['last-modified-date']['value'];
            $createdDate=$summary['created-date']['value'];
            $workType=$summary['type'];
            $visibility=$summary['visibility'];
            $workPath=$summary['path'];
            $publicationDate=isset($summary['publication-date']) ? PublicationDate::loadInstanceFromOrcidArray($summary['publication-date']) : null;
            if (!empty($publicationDate)) {
                $newRecord->setPubDate($publicationDate);
            }
            $titles=Title::loadInstanceFromOrcidArray($summary['title']);
            $newRecord->setPutCode($putCode)
                ->setTitles($titles)
                ->setSource($source)
                ->setLastModifiedDate($lastUpdateDate)
                ->setCreatedDate($createdDate)
                ->setType($workType)
                ->setPath($workPath)
                ->setVisibility($visibility);
            foreach ($externalIdArray as $externalId) {
                $newExternalId=ExternalId::loadInstanceFromOrcidArray($externalId);
                $newRecord->addNewExternalIdent($newExternalId);
            }
        } catch (Exception $e) {
            error_log("Panic in ".get_class($newRecord)." : ".$e->getMessage());
        }
        return $newRecord;
    }
}
