<?php

namespace Orcid\Work\Data\Data;

use Exception;
use Orcid\Work\Data\Common;
use Orcid\Work\Data\Data;

class Citation extends Common
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $value;

    public function __construct($filterData=true)
    {
        $filterData ? $this->setFilter() : $this->removeFilter();
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * An empty string value will not be added like citation
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function setValue(string $value)
    {
        if (!empty($value)) {
            if (!Data::isValidCitation($value)) {
                throw new Exception("The citation value sent is not valid. The max length of a citation is : ".Data::CITATION_MAX_LENGTH);
            }
            $this->value = $value;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return (empty($this->type) &&!empty($this->value)) ? $this->setType("formatted-unspecified")->getType() : $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public static function loadInstanceFromOrcidArray($orcidCitationArray)
    {
        $citation=isset($orcidCitationArray['citation-value']) ? $orcidCitationArray['citation-value'] : '';
        $citationType=isset($orcidCitationArray['citation-type']) ? $orcidCitationArray['citation-type'] : '';
        return (new Citation(true))->setValue($citation)->setType($citationType);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->getValue());
    }
}
