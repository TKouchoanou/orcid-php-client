<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Orcid\Work\Create;



use ArrayIterator;
use Exception;

class Works extends ArrayIterator
{
    /**
     * @param Work $value
     * @throws Exception
     */
public function append($value)
{
    if(!is_null($value) && ($value instanceof Work)){
        parent::append($value);
    }else{
        throw new Exception("The value you can append must be instance of Work and not null");
    }
}

public  function getXMLData(){

        $dom= work::getNewOrcidCommonDomDocument();
        $bulk = $dom->appendChild( $dom->createElementNS(work::$namespacebulk,"bulk:bulk"));
        $dom->createAttributeNS(work::$namespaceWork, "work:work");
        $dom->createAttributeNS(work::$namespaceCommon, "common:common");
        $bulk->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:schemaLocation", work::$namespacebulk . " ../ bulk-3.0.xsd");
        foreach ($this as $work){
            /**
             * @var Work $work
             */
            $workNode=$bulk->appendChild($dom->createElementNS(work::$namespaceWork,"work:work"));
            try {
                $work->addMetaToWorkNode($dom, $workNode);
            } catch (Exception $e) {
                echo $e;
            }
        }

        return $dom->saveXML() ;
    }


}
