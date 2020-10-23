<?php


namespace Orcid\Work\Data\Data;


class Citation
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $value;

    public function __construct(string $citationType, string $citationValue, $filterData=true)
    {
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}