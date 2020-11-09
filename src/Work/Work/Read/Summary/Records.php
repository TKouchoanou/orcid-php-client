<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Orcid\Work\Work\Read\Summary;



use Exception;
use Orcid\Work\Data\Data\ExternalId;
use Orcid\Work\Data\Data\PublicationDate;
use Orcid\Work\Data\Data\Title;
use Orcid\Work\Work\Read\AbstractRecordList;

/**
 * Class Records
 * @package Orcid\Work\Read
 */
class Records extends AbstractRecordList
{

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
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count()===0;
    }

    /**
     * @param $orcidRecords
     * @return Records
     * @throws Exception
     */
    public static function loadInstanceFromOrcidArray($orcidRecords)
    {
        $records=new self();
        $groups=$orcidRecords['group'];
        $lastModifiedDate=$orcidRecords['last-modified-date']['value'];
        $path=$orcidRecords['path'];
        $records->setLastModifiedDate($lastModifiedDate);
        $records->setOrcidWorks($orcidRecords);
        $records->setGroup($groups);
        $records->setPath($path);
        foreach( $groups as $work) {
            $newRecord = Record::loadInstanceFromOrcidArray($work);
            $records->append($newRecord);
        }
        return $records;
    }
}
