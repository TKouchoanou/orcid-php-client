<?php


namespace Orcid\Work\Create;

use Exception;
use Orcid\Work\Contributor;
use Orcid\Work\OAbstractWork;
class CreateAbstractWork extends OAbstractWork
{
    /**
     * @var string
     */
    protected $journalTitle;
    /**
     * @var string
     */
    protected $shortDescription;
    /**
     * @var string
     */
    protected $citation;
    /**
     * @var string []
     */
    protected $authors;

    /**
     * @var string
     */
    protected $languageCode;
    /**
     * @var string
     */
    protected $citationType;

    /**
     * @var string
     */
    protected $country;
    /**
     * @var string
     */
    protected $workUrl;


    /**
     * An empty creditName (fullName) string value will not be added
     * to be sure to add an contributor check on your side that his full name is not empty.
     * if you added the contributor orcid ID and is from sandBox put false
     * for the last parameter $orcidIdOfProductionEnv (his default value is true)
     * this value will be use if only you add orcid ID
     * by default you can put empty string for $role and $sequence
     * but in this case we will add author for empty role
     * and we will not add sequence to the sent data
     * example : $work->('authorName','','0000-1111-2222-3333','',false)
     * Author is synonyme of contributor
     * @param string $creditName
     * @param string $role
     * @param string $orcidId
     * @param string $sequence
     * @param bool $orcidFromProductionEnv
     * @return $this
     * @throws Exception
     */
    public function addContributor(string $creditName, string $role='', string $orcidId='', string $sequence='', $orcidFromProductionEnv=true)
    {
        if(!empty($creditName)){
            if($this->isFilterData()){
             $role=self::filterContributorRole($role);
             $sequence=self::filterContributorSequence($sequence);
             $orcidId=self::filterOrcid($orcidId);
            }
            $this->authors []= new Contributor($creditName,$role,$orcidId,$sequence,'',$orcidFromProductionEnv);}
        return $this;
    }

    /**
     * contributors are Authors
     * if you added an orcid id, set the account environment
     * to be true for the production false for the sandbox,
     * by default the production is considered.
     * Email is not important and is not used in the data sent.
     * you can therefore put an empty string
     * @param Contributor $contributor
     * @throws Exception
     */
    public function addNewContributor(Contributor $contributor)
    {
        if($this->isFilterData()){
            $contributor->setRole(self::filterContributorRole($contributor->getRole()))
                        ->setSequence(self::filterContributorSequence($contributor->getSequence()))
                        ->setOrcid(self::filterOrcid($contributor->getOrcid()));
        }
        $this->authors []=$contributor;
    }

    /**
     * An empty string value will not be added
     * @param string $journalTitle
     * @return $this
     */
    public function setJournalTitle(string $journalTitle)
    {
        if(!empty($journalTitle)) {
            $this->journalTitle = $journalTitle;
        }
        return $this;
    }

    /**
     * An empty string value will not be added
     * @param string $shortDescription
     * @return $this
     * @throws Exception
     */
    public function setShortDescription(string $shortDescription)
    {
        if(!empty($shortDescription)) {
            if (!self::isValidShortDescription($shortDescription)) {
                throw new Exception('The short description length must not be than 5000 characters');
            }
            $this->shortDescription = $shortDescription;
        }
        return $this;
    }

    /**
     * an exception is thrown if you try to add invalid value
     * An empty string value will not be added
     * @param string $languageCode
     * @return $this
     * @throws Exception
     */
    public function setLanguageCode(string $languageCode)
    {
        if(!empty($languageCode)){
            if($this->isFilterData()){
                $languageCode=self::filterLanguageCode($languageCode);
            }
            if(!self::isValidLanguageCode($languageCode)){
                throw new Exception("Your language code is not valid. here are valid language code: [".implode(",",self::LANGUAGE_CODES)."] ".
                    "if you want to set it by force use the method setPropertyByForce('property','value')");
            }
            $this->languageCode = $languageCode;
        }
        return $this;
    }

    /**
     * An empty string value will not be added like citation
     * @param string $citation
     * @param string $citationType
     * @return $this
     * @throws Exception
     */
    public function setCitation(string $citation,$citationType='formatted-unspecified')
    {
        if(!empty($citation)){
            if(!self::isValidCitation($citation)){
                throw new Exception("The citation sent is not valid. The max length of a citation is : ".self::CITATION_MAX_LENGTH);
            }
            $this->citation = $citation;
            if(empty($this->citationType)){
                $this->setCitationType($citationType);
            }
        }
        return $this;
    }

    /**
     * 1- by default your citation type will be formatted-unspecified
     * if you add citation without citation-type.
     * 2-it makes no sense to add citation type without adding citation
     * @param string $citationType
     * @return $this
     * @throws Exception
     */
    public function setCitationType(string $citationType)
    {
        if (!empty($citationType)) {
            if ($this->isFilterData()){
                $citationType=self::filterCitationType($citationType);
            }
            if(!self::isValidCitationType($citationType)){
                throw new Exception("The citation format : ".$citationType."  is not valid here are the valid values : [".
                    implode(",",self::CITATION_FORMATS)."] if you want to set it by force use the method setPropertyByForce('property','value')");
            }
            $this->citationType = $citationType;
        }
        return $this;
    }


    /**
     * to be sure to add a country check on your side that it is not empty.
     * An empty string value will not be added
     * @param string $country
     * @return $this
     * @throws Exception
     */
    public function setCountry(string $country)
    {
        if(!empty($country)){
            if($this->isFilterData()){
                $country=self::filterCountryCode($country);
            }
            if(!self::isValidCountryCode($country)){
                throw new Exception("The country is not valid it must be a  code of  two characters and must respect ISO 3166 standard for country.".
                    " here are valid values : [" .implode(",",self::COUNTRY_CODES).
                    "] if you want to set it by force use the method setPropertyByForce('property','value')");
            }
            $this->country = $country;
        }
        return $this;
    }

    /**
     * to be sure to add a work url check on your side that it is not empty.
     * An empty string value will not be added
     * @param string $workUrl
     * @return $this
     */
    public function setWorkUrl(string $workUrl)
    {
        if(!empty($workUrl)){
            $this->workUrl = $workUrl;
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return string
     */
    public function getCitationType()
    {
        return $this->citationType;
    }

    /**
     * @return string
     */
    public function getWorkUrl()
    {
        return $this->workUrl;
    }

    /**
     * @return string
     */
    public function getCitation()
    {
        return $this->citation;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @return string
     */
    public function getJournalTitle()
    {
        return $this->journalTitle;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

}