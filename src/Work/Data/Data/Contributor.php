<?php


namespace Orcid\Work\Data\Data;


use Exception;
use Orcid\Work\Work\OAbstractWork;

class Contributor
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
     * @var bool
     */
  protected $filterData;

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
       $this->setFilterData($filterData)->setCreditName($creditName)->setRole($role)->setOrcid($orcid)->setSequence($sequence)
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
            $role=OAbstractWork::filterContributorRole($role);
        }
        if(!OAbstractWork::isValidContributorRole($role)){
            throw new Exception('The author '.$this->creditName.' role '.$role.' is not valid here are author valid role: ['.
                implode(",",OAbstractWork::AUTHOR_ROLE_TYPE)."]");
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
        $orcid=OAbstractWork::filterOrcid($orcid);
      }
        if(!empty($orcid) && !OAbstractWork::isValidOrcid($orcid)){
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
            $sequence=OAbstractWork::filterContributorSequence($sequence);
        }
        if(!empty($sequence) && !OAbstractWork::isValidContributorSequence($sequence)){
            throw new Exception('The author '.$this->creditName.' sequence '.$sequence.' is not valid here are sequence valid values : ['
                .implode(",",OAbstractWork::AUTHOR_SEQUENCE_TYPE).']');
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
     * @param bool $filterData
     * @return Contributor
     */
    public function setFilterData(bool $filterData){
        $this->filterData=$filterData;
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
     * @return bool
     */
    public function hasFilter(){
        return $this->filterData;
      }
}