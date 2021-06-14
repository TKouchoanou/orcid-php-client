<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work;

use Exception;

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
     * @throws Exception
     */
    public function __construct(string $idType, string $idValue, $idUrl='', $idRelationship='')
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
    public function isEqualTo($other)
    {
        return ($other instanceof ExternalId
                && $other->getIdType()===$this->idType
                && $other->getIdValue()===$this->idValue);
    }

    /**
     * @param string $idType
     * @param string $idValue
     * @return bool
     */
    public function isSame(string $idType, string $idValue)
    {
        return $this->idType===(string)$idType && $this->idValue===(string)$idValue;
    }

    /**
     * @param string $idRelationship
     * @throws Exception
     */
    public function setIdRelationship(string $idRelationship="")
    {
        $idRelationship=empty($idRelationship) ? 'self' : $idRelationship;
        if (OAwork::isValidExternalIdRelationType($idRelationship)) {
            $this->idRelationship = OAwork::tryToNormalizeExternalIdRelationType($idRelationship);
        } else {
            throw new Exception("the relationship value is not valid here are relationship valid value ["
                .implode(",", OAwork::EXTERNAL_ID_RELATION_TYPE)."].");
        }
    }

    /**
     * we don't control the idtType here because the list is evolving
     * @param string $idType
     * @throws Exception
     */
    public function setIdType(string $idType)
    {
        $this->checkIsNotEmptyValue($idType, 'idType');
        $this->idType = OAwork::tryToNormalizeExternalIdType($idType);
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
     * @throws Exception
     */
    public function setIdValue(string $idValue)
    {
        $this->checkIsNotEmptyValue($idValue, 'idValue');
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

    /**
     * @param $value
     * @param $name
     * @throws Exception
     */
    private function checkIsNotEmptyValue($value, $name)
    {
        if (empty($value)) {
            throw new Exception('the value of '.$name.' can\'t be empty for valid external Id type') ;
        }
    }
}
