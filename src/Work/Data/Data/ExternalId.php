<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Data\Data;


use Exception;
use Orcid\Work\Data\Common;
use Orcid\Work\Data\Data;

class ExternalId extends Common
{ 
    /**
     * @var string
     */
    protected $idType;
    /**
     * @var string
     */
    protected $idUrl;
    /**
     * @var string
     */
    protected $idValue;
    /**
     * @var string
     */
    protected $idRelationship;

    /**
     * ExternalId constructor.
     * @param string $idType
     * @param string |int $idValue
     * @param string $idUrl
     * @param string $idRelationship
     * @param bool $filterData
     * @throws Exception
     */
    public function __construct(string $idType, string $idValue, $idUrl='', $idRelationship='',$filterData=true)
    {
        $filterData?$this->setFilter():$this->removeFilter();
        $this->setIdRelationship($idRelationship);
        $this->setIdType($idType);
        $this->setIdValue($idValue);
        $this->setIdUrl($idUrl);
    }

    /**
     * @param $other
     * @return bool
     */
    public function isEqualTo($other){
        return ( $other instanceof ExternalId
                && $other->getIdType()===$this->idType
                && $other->getIdValue()===$this->idValue);
    }

    /**
     * @param string $idType
     * @param string $idValue
     * @return bool
     */
   public function isSame(string $idType,string $idValue){
       return $this->idType===(string)$idType && $this->idValue===(string)$idValue;
   }

    /**
     * @param string $idRelationship
     * @return ExternalId
     * @throws Exception
     */
    public function setIdRelationship(string $idRelationship="")
    {  $idRelationship=empty($idRelationship)?'self':$idRelationship;
        if($this->hasFilter()){
            $idRelationship=Data::filterExternalIdRelationType($idRelationship);
        }
        if(Data::isValidExternalIdRelationType($idRelationship)){
            $this->idRelationship = $idRelationship;
        }else{
            throw new Exception("the relationship value is not valid here are relationship valid value ["
                .implode(",",Data::EXTERNAL_ID_RELATION_TYPE)."].");
        }
        return $this;
    }

    /**
     * we don't control the idtType here because the list is evolving
     * @param string $idType
     * @return ExternalId
     * @throws Exception
     */
    public function setIdType(string $idType)
    {
        $this->checkIsNotEmptyValue($idType,'idType');
        $this->idType = $idType;
        return $this;
    }

    /**
     * @param string $idUrl
     * @return ExternalId
     */
    public function setIdUrl(string $idUrl)
    {
        $this->idUrl = $idUrl;
        return $this;
    }

    /**
     * @param string $idValue
     * @return ExternalId
     * @throws Exception
     */
    public function setIdValue(string $idValue)
    {
        $this->checkIsNotEmptyValue($idValue,'idValue');
        $this->idValue = $idValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdRelationship()
    {
        return $this->idRelationship;
    }

    /**
     * @return string
     */
    public function getIdType()
    {
        return $this->idType;
    }

    /**
     * @return string
     */
    public function getIdUrl()
    {
        return $this->idUrl;
    }

    /**
     * @return string
     */
    public function getIdValue()
    {
        return $this->idValue;
    }

    /**
     * @param $value
     * @param $name
     * @throws Exception
     */
    private function checkIsNotEmptyValue($value,$name)
    {
        if(empty($value)){
            throw new Exception('the value of '.$name.' can\'t be empty for valid external Id type') ;
        }
    }

    public static function loadInstanceFromOrcidArray($orcidExternalIdArray)
    {
        $relationType=isset($orcidExternalIdArray['external-id-relationship'])?$orcidExternalIdArray['external-id-relationship']:'';
        $url=isset($orcidExternalIdArray['external-id-url']['value'])?$orcidExternalIdArray['external-id-url']['value']:'';
        $type=$orcidExternalIdArray['external-id-type'];
        $value=$orcidExternalIdArray['external-id-value'];
        return new ExternalId($type,$value,$url,$relationType);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return true;
    }
}
