<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Orcid\Work\Read;



use ArrayIterator;
use Exception;

class Records extends ArrayIterator
{
  /**
     * @var string 
     */
    protected $lastModifiedDate;
    /**
     * @var array
     */
    protected $group;
    /**
     * @var array
     */
    protected $OrcidWorks;



    /**
     * @param mixed $lastModifiedDate
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;
    }

    /**
     * @param Record $value
     * @throws Exception
     */
    public function append($value)
    {
        if(!is_null($value) && ($value instanceof Record)){
            parent::append($value);
        }else{
            throw new Exception("The value you can append must be instance of Record and not null");
        }
    }



    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return array
     */
    public function getOrcidWorks()
    {
        return $this->OrcidWorks;
    }

    /**
     * @param array $OrcidWorks
     */
    public function setOrcidWorks(array $OrcidWorks)
    {
        $this->OrcidWorks = $OrcidWorks;
    }

    public function buildWorkRecords($orcidRecords){

        //orcid records in associative array
        $groups=$orcidRecords['group'];
        $lastModifiedDate=$orcidRecords['last-modified-date']['value'];
        $this->setLastModifiedDate($lastModifiedDate);
        $this->setOrcidWorks($orcidRecords);
        $this->setGroup($groups);
        foreach( $groups as $work) {
            $newRecord= new Record();
            $summary=$work['work-summary'][0];
            $putCode=$summary['put-code'];
            $source=$summary['source']['source-name']['value'];

            $title=$summary['title']['title']['value'];
            $translatedTitle=isset($summary['title']['translated-title']['value'])?$summary['title']['translated-title']['value']:null;
            $translatedTitleLanguageCode=isset($summary['title']['translated-title']['language-code'])?$summary['title']['translated-title']['language-code']:null;
            $subTitle=isset($summary['title']['subtitle']['value'])?$summary['title']['subtitle']['value']:null;

            $externalIdArray= $summary['external-ids']['external-id'];
            $lastUpdatedate=$summary['last-modified-date']['value'];
            $createdDate=$summary['created-date']['value'];
            $workType=$summary['type'];
            $visibility=$summary['visibility'];
            $workPath=$summary['path'];
            $pubYear=isset($summary['publication-date']['year']['value'])?$summary['publication-date']['year']['value']:'';
            $pubmonth=isset($summary['publication-date']['month']['value'])?$summary['publication-date']['month']['value']:'';
            $pubday=isset($summary['publication-date']['day']['value'])?$summary['publication-date']['day']['value']:'';
            try {
                $newRecord->setPutCode($putCode)
                    ->setTitle($title)
                    ->setSource($source)
                    ->setLastModifiedDate($lastUpdatedate)
                    ->setCreatedDate($createdDate)
                    ->setType($workType)
                    ->setPath($workPath)
                    ->setVisibility($visibility)
                    ->setPublicationDate($pubYear, $pubmonth, $pubday);
            } catch (Exception $e) {
                echo $e;
            }
            if(!empty($translatedTitle)){ $newRecord->setTranslatedTitle($translatedTitle); }
            if(!empty($translatedTitleLanguageCode)){
                try {
                    $newRecord->setTranslatedTitleLanguageCode($translatedTitleLanguageCode);
                } catch (Exception $e) {
                }
            }
            if(!empty($subTitle)){$newRecord->setSubTitle($subTitle); }
            foreach( $externalIdArray as $externalId) {
                $relationType=isset($externalId['external-id-relationship'])?$externalId['external-id-relationship']:'';
                $url=isset($externalId['external-id-url']['value'])?$externalId['external-id-url']['value']:'';
                $type=$externalId['external-id-type'];
                $value=$externalId['external-id-value'];
                try {
                    $newRecord->addExternalIdent($type, $value, $url, $relationType);
                } catch (Exception $e) {
                    echo $e;
                }
            }
            try {
                $this->append($newRecord);
            } catch (Exception $e) {
                echo $e;
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpity()
    {
        return $this->count()===0;
    }
}
