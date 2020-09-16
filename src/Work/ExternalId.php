<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work;


class ExternalId
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
     * @throws \Exception
     */
    public function __construct($idType,$idValue,$idUrl='',$idRelationship='')
    {
        $idRelationship=empty($idRelationship)?'self':$idRelationship;
        if(!in_array(str_replace('_','-',strtolower($idRelationship)),OAwork::EXTERNAL_ID_RELATION_TYPE)){
            throw new \Exception(" externalType : ".$idType." , external value : ".$idValue." , external relationship : ".$idRelationship." . The External Ident type of relationship is not valid");
        }
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
     */
    public function setIdRelationship(string $idRelationship)
    {
        $this->idRelationship = $idRelationship=str_replace('_','-',strtolower($idRelationship));
    }

    /**
     * @param string $idType
     */
    public function setIdType(string $idType)
    {
        $this->checkisNotEmptyValue($idType,'idType');
        $this->idType = $idType;
    }

    /**
     * @param string $idUrl
     */
    public function setIdUrl(string $idUrl)
    {
        $this->idUrl = $idUrl;
    }

    /**
     * @param string $idValue
     */
    public function setIdValue(string $idValue)
    {
        $this->checkisNotEmptyValue($idValue,'idValue');
        $this->idValue = $idValue;
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

    private function checkisNotEmptyValue($value,$name)
    {
        if(empty($value)){
            throw new \Exception('the value of '.$name.' can\'t be empity for valid external Id type') ;
        }
    }

}
