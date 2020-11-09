<?php


namespace Orcid\Work\Work\Read;


use Orcid\Work\Loader;
use Orcid\Work\Work\Read\Summary\Record;

interface SingleRecord extends Loader
{
    /**
     * @param  $source
     * @return $this
     */
    public function setSource($source);


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
     * @param  $visibility
     * @return $this
     */
    public function setVisibility($visibility);

    /**
     * @param  $path
     * @return Record
     */
    public function setPath($path);



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