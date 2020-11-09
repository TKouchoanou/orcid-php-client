<?php


namespace Orcid\Work;


use Exception;


Abstract class Common
{
    /**
     * @var bool
     */
    protected $filter=false;

    /**
     * @return $this
     */
    public function setFilter()
    {
        $this->filter = true;
        return $this;
    }

    public function hasFilter(){
        return $this->filter;
    }

    public function removeFilter(){
        $this->filter = false;
        return $this;
    }

    /**
     * Consciously allows you to put any value in a property without checking the validity with orcid
     * @param string $property
     * @param $value
     * @return Common
     * @throws Exception
     */
    public function setPropertyByForce(string $property,$value){
        if(property_exists($this,$property)){
            $this->{$property}=$value;
        }else{
            throw new Exception("your property ".$property." don't exist this objet property are : ".
                implode(", ",array_keys(get_object_vars ($this))).".");
        }
        return $this;
    }
}