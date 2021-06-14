<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Work\Read\Full;

use Exception;
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
        if (!is_null($value) && ($value instanceof Record)) {
            parent::append($value);
        } else {
            throw new Exception("The value you can append must be instance of Record and not null");
        }
    }


    /**
     * @param $orcidRecords
     * @return Records
     * @throws Exception
     */
    public static function loadInstanceFromOrcidArray($orcidRecords)
    {
        $records=new Records();
        $bulk=$orcidRecords['bulk'];
        foreach ($bulk as $work) {
            $newRecord= Record::loadInstanceFromOrcidArray(($work['work']));
            $records->append($newRecord);
        }
        return $records;
    }
}
