<?php


namespace Orcid\Work\Read;


use Orcid\Work\Read\Summary\Record;

interface SingleRecord
{
    /**
     * @param string $source
     * @return $this
     */
    public function setSource(string $source);


    /**
     * @param $lastModifiedDate
     * @return $this
     */
    public function setLastModifiedDate($lastModifiedDate);


    /**
     * @param  $createdDate
     * @return $this
     */
    public function setCreatedDate($createdDate);

    /**
     * @param string $visibility
     * @return $this
     */
    public function setVisibility(string $visibility);

    /**
     * @param string $path
     * @return Record
     */
    public function setPath(string $path);



    /**
     * @return int|string
     */
    public function getPutCode();
    /**
     * @return string
     */
    public function getSource();

    /**
     * @return int
     */
    public function getLastModifiedDate();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getVisibility();
}