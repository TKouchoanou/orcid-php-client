<?php


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

    public function __construct($idType,$idValue,$idUrl,$idRelationship='self')
    {

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
                && $other->getIdType()===$this->getIdType()
                && $other->getIdValue()===$this->getIdValue());
    }

    /**
     * @param string $idRelationship
     */
    public function setIdRelationship(string $idRelationship)
    {
        $this->idRelationship = $idRelationship;
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
        $this->checkisNotEmptyValue($idUrl,'idUrl');
        $this->idUrl = $idUrl;
    }

    /**
     * @param string $idValue
     */
    public function setIdValue($idValue)
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
            throw new \Exception('la valeur de la variable  '.$name.' ne peut pas être nulle') ;
        }
    }

}
