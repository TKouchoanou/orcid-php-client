<?php


namespace Orcid\Work\Data\Data;


use Exception;
use Orcid\Work\Data\Common;
use Orcid\Work\Data\Data;

class Contributor extends Common
{
    /**
     * @var string
     */
  protected $creditName;
    /**
     * @var string
     */
  protected $role;
    /**
     * @var string
     */
  protected $orcid;
    /**
     * @var string
     */
  protected $sequence;
    /**
     * @var string
     */
  protected $email;
    /**
     * @var string
     */
  protected $env;
    /**
     * @var bool
     */
  protected $countOfProdEnv;


    /**
     * Contributor constructor.
     * @param string $creditName
     * @param string $role
     * @param string $orcid
     * @param string $sequence
     * @param string $email
     * @param bool $countOfProdEnv
     * @param bool $filterData
     * @throws Exception
     */
    public function __construct(string $creditName, string $role, $orcid='',$sequence='', $email='',$countOfProdEnv=true,$filterData=true)
    {
           $filterData? $this->setFilter():$this->removeFilter();
           $this->setCreditName($creditName)->setRole($role)->setOrcid($orcid)->setSequence($sequence)
            ->setEmail($email)->setCountOfProdEnv($countOfProdEnv)->setEnv();
    }

    /**
     * @param string $creditName
     * @return Contributor
     */
    public function setCreditName(string $creditName)
    {
        $this->creditName = $creditName;
        return $this;
    }

    /**
     * @param string $role
     * @return Contributor
     * @throws Exception
     */
    public function setRole(string $role='')
    {
        $role=empty($role)?'author':$role;
        if($this->hasFilter()){
            $role=Data::filterContributorRole($role);
        }
        if(!Data::isValidContributorRole($role)){
            throw new Exception('The author '.$this->creditName.' role '.$role.' is not valid here are author valid role: ['.
                implode(",",Data::AUTHOR_ROLE_TYPE)."]");
        }
        $this->role = $role;
        return $this;
    }

    /**
     * @param string $orcid
     * @return Contributor
     * @throws Exception
     */
    public function setOrcid(string $orcid)
    {
        if($this->hasFilter()){
        $orcid=Data::filterOrcid($orcid);
      }
        if(!empty($orcid) && !Data::isValidOrcid($orcid)){
            throw new Exception('The author '.$this->creditName.' Orcid '.$orcid.' is not valid');
        }
        $this->orcid = $orcid;
        return $this;
    }

    /**
     * @param string $sequence
     * @return Contributor
     * @throws Exception
     */
    public function setSequence(string $sequence)
    {
        if($this->hasFilter()){
            $sequence=Data::filterContributorSequence($sequence);
        }
        if(!empty($sequence) && !Data::isValidContributorSequence($sequence)){
            throw new Exception('The author '.$this->creditName.' sequence '.$sequence.' is not valid here are sequence valid values : ['
                .implode(",",Data::AUTHOR_SEQUENCE_TYPE).']');
        }
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @param string $email
     * @return Contributor
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param bool $countOfProdEnv
     * @return Contributor
     */
    public function setCountOfProdEnv(bool $countOfProdEnv)
    {
        $this->countOfProdEnv = $countOfProdEnv;
        return $this;
    }



    /**
     * @return $this
     */
    protected function setEnv()
    {
        $this->env = $this->isCountOfProdEnv()? '' : 'sandbox.';
        return $this;
    }

    /**
     * @return string
     */
    public function getCreditName()
    {
        return $this->creditName;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }
    /**
     * @return string
     */
    public function getOrcid()
    {
        return $this->orcid;
    }

    /**
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }
    /**
     * @return bool
     */
    public function isCountOfProdEnv(): bool
    {
        return $this->countOfProdEnv;
    }

    /**
     * @param $orcidContributorArray
     * @return Contributor
     * @throws Exception
     */
    public static function loadInstanceFromOrcidArray($orcidContributorArray)
    {
        $orcidId=$orcidContributorArray['contributor-orcid']["path"];
        $creditName=$orcidContributorArray['credit-name']['value'];
        $email=isset($orcidContributorArray['contributor-email'])?$orcidContributorArray['contributor-email']:'';
        $sequence=$orcidContributorArray['contributor-attributes']['contributor-sequence'];
        $role=$orcidContributorArray['contributor-attributes']['contributor-role'];
        return new Contributor($creditName,$role,$orcidId,$sequence,$email);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return true;
    }
}