<?php


namespace Orcid\Work\Work\Read;


use ArrayIterator;
use Orcid\Work\Loader;

abstract class AbstractRecordList extends ArrayIterator implements Loader
{
    /**
     * @var string
     */
    protected $lastModifiedDate;
    /**
     * @var array
     */
    protected $group;
    /**
     * @var array
     */
    protected $OrcidWorks;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param mixed $lastModifiedDate
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return array
     */
    public function getOrcidWorks()
    {
        return $this->OrcidWorks;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * @param array $OrcidWorks
     */
    public function setOrcidWorks(array $OrcidWorks)
    {
        $this->OrcidWorks = $OrcidWorks;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->count()===0;
    }

}