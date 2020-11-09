<?php


namespace Orcid\Work\Data\Data;


use Exception;
use Orcid\Work\Data\Common;
use Orcid\Work\Data\Data;

class Title extends Common
{
    /**
     * @var string
     */
    protected $value;
    /**
     * @var string
     */
    protected $subtitle;
    /**
     * @var string
     */
    protected $translatedTitle;
    /**
     * @var string
     */
    protected $translatedTitleLanguageCode;

    /**
     * Title constructor.
     * @param bool $filterData
     */
    public function __construct($filterData=true)
    {
        $filterData?$this->setFilter():$this->removeFilter();
    }

    /**
     * title value is required , empty value is not accepted
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function setValue(string $value)
    {
        if (!Data::isValidTitle($value)) {
            throw new Exception("The title value sent is not valid. The title value is required and its length must be between [ 1 -".Data::TITLE_MAX_LENGTH." ] your title length is : ".strlen($value));
        }
        $this->value = $value;
        return $this;
    }

    /**
     * if you add empty subtitle or translated title we just won't set it because
     * we consider that you don't want to add subtitle/translated title
     * empty subtitle is not useful
     * Then you don't need to check if your string is empty to set
     * @param string $subTitle
     * @return Title
     * @throws Exception
     */
    public function setSubtitle(string $subTitle)
    {
        if(!empty($subTitle)){
            if(!Data::isValidSubTitle($subTitle)){
                throw new Exception("The subtitle value sent is not valid. its length must be between [0 -".Data::SUBTITLE_MAX_LENGTH."] your subtitle (".$subTitle.") length is : ".strlen($subTitle));
            }
            $this->subtitle = $subTitle;
        }
        return $this;
    }

    /**
     *  if you add empty translated title we just won't set it because
     * we consider that you don't want to add translated title
     * empty translated title is not useful .
     * Then you don't need to check if your string is empty to set
     * if you add translated title is required to add the language code
     * otherwise your translated title won't be taken into account
     * @param string $translatedTitle
     * @return Title
     * @throws Exception
     */
    public function setTranslatedTitle(string $translatedTitle)
    {
        if(!empty($translatedTitle)){
            if(!Data::isValidTranslatedTitle($translatedTitle)){
                throw new Exception("The translatedTitle value sent is not valid. its length must be between [0 -".Data::TRANSLATED_MAX_LENGTH."] your translatedTitle (".$translatedTitle.") length is : ".strlen($translatedTitle));
            }
            $this->translatedTitle = $translatedTitle;
        }
        return $this;
    }

    /**
     * if you send empty string for translated title languageCode value
     * it won't be taken in account, then even if you add non empty
     * translated title it won't be possible to add theirs values to xml data because both must
     * not be empty to be valid
     * @param string $translatedTitleLanguageCode
     * @return Title
     * @throws Exception
     */
    public function setTranslatedTitleLanguageCode(string $translatedTitleLanguageCode)
    {
        if (!empty($translatedTitleLanguageCode)) {
            if($this->hasFilter()){
                $translatedTitleLanguageCode=Data::filterLanguageCode($translatedTitleLanguageCode);
            }
            if(!Data::isValidLanguageCode($translatedTitleLanguageCode)){
                throw new Exception("Your language code is not valid. here are valid language code: [".implode(",",Data::LANGUAGE_CODES)."] ".
                    "if you want to set it by force use the method setPropertyByForce('property','value')");
            }
            $this->translatedTitleLanguageCode = $translatedTitleLanguageCode;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @return string
     */
    public function getTranslatedTitle()
    {
        return $this->translatedTitle;
    }

    /**
     * @return string
     */
    public function getTranslatedTitleLanguageCode(): string
    {
        return $this->translatedTitleLanguageCode;
    }

    /**
     * @param $orcidTitleArray
     * @return Title
     * @throws Exception
     */
    public static function loadInstanceFromOrcidArray($orcidTitleArray)
    {
        $titles=new self();
        $title=$orcidTitleArray['title']['value'];
        $translatedTitle=isset($orcidTitleArray['translated-title']['value'])?$orcidTitleArray['translated-title']['value']:'';
        $translatedTitleLanguageCode=isset($orcidTitleArray['translated-title']['language-code'])?$orcidTitleArray['translated-title']['language-code']:'';
        $subTitle=isset($orcidTitleArray['subtitle']['value'])?$orcidTitleArray['subtitle']['value']:'';
       return $titles->setFilter()->setValue($title)->setTranslatedTitle($translatedTitle)->setTranslatedTitleLanguageCode($translatedTitleLanguageCode)->setSubtitle($subTitle);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->getValue());
    }
}