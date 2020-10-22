<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Orcid\Work\Read\Full;



use Exception;
use Orcid\Work\Read\AbstractRecordList;

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
     * @param array $orcidRecords
     * @return $this
     */
    public function buildWorkRecords(array $orcidRecords){
        $bulk=$orcidRecords['bulk'];
        foreach ($bulk as $work){
            $newRecord= new Record();
            $newRecord->buildRecord($work['work']);
            $this->append($newRecord);
        }
        return $this;
    }


}
