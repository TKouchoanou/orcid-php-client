<?php
/**
 * @package   orcid-php-client
 * @author    Kouchoanou Théophane <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work\Read;

use Orcid\Work\OAwork;

class Record extends OAwork
{
    /**
     * @var int
     */
    protected $lastModifiedDate;
    /**
     * @var string
     */
    protected $source;

    /**
     * @var int
     */
    protected $createdDate;
    /**
     * @var string
     */
    protected $visibility;
    /**
     * @var string
     */
    protected $path;


    /**
     * @param string $source
     * @return $this
     */
    public function setSource(string $source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param $lastModifiedDate
     * @return $this
     */
    public function setLastModifiedDate($lastModifiedDate)
    {
        $this->lastModifiedDate = $lastModifiedDate;
        return $this;
    }

    /**
     * @param  $createdDate
     * @return $this
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @param string $visibility
     * @return $this
     */
    public function setVisibility(string $visibility)
    {
        $this->visibility = $visibility;
        return $this;
    }

    /**
     * @param string $path
     * @return Record
     */
    public function setPath(string $path)
    {
        $this->path = $path;
        return $this;
    }



    /**
     * @return int|string
     */
    public function getPutCode()
    {
        return $this->putCode;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return int
     */
    public function getLastModifiedDate()
    {
        return $this->lastModifiedDate;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
    * @return string
    */
    public function getVisibility()
    {
        return $this->visibility;
    }
}
