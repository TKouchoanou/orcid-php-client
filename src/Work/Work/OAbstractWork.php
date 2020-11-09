<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Orcid\Work\Work;


 use Exception;
 use Orcid\Work\Common;
 use Orcid\Work\Data\Data\ExternalId;
 use Orcid\Work\Data\Data;
 use Orcid\Work\Data\Data\PublicationDate;

 abstract  class OAbstractWork extends Common
 {
     /**
      * @var string
      */
     protected $putCode;
     /**
      * @var Data\Title
      */
     protected $titles;
     /**
      * @var PublicationDate
      */
     protected $publicationDate;
     /**
      * @var ExternalId[]
      */
     protected $externalIds = [];
     /**
      * @var string
      */
     protected $type;



     public function __construct()
     {
     }

     /**
      * possible to add several external id of the same type
      * But you are responsible for what you send
      * @param string $externalIdType
      * @param string $externalIdValue
      * @param string $externalIdUrl
      * @param string $externalIdRelationship
      * @return $this
      * @throws Exception
      */
     public function addExternalIdent(string $externalIdType, string $externalIdValue, $externalIdUrl = '', $externalIdRelationship = '')
     {
         if($this->hasFilter()){
             $externalIdType =Data::filterExternalIdType($externalIdType);
             $externalIdRelationship=Data::filterExternalIdRelationType($externalIdRelationship);
         }
         $this->externalIds[] = new ExternalId($externalIdType, $externalIdValue, $externalIdUrl, $externalIdRelationship);
         return $this;
     }

     /**
      * possible to add several external id of the same type
      * But you are responsible for what you send
      * @param ExternalId $externalId
      * @return $this
      * @throws Exception
      */
     public function addNewExternalIdent(ExternalId $externalId)
     { if($this->hasFilter()){
         $externalId->setIdRelationship(Data::filterExternalIdRelationType($externalId->getIdRelationship()));
         $externalId->setIdType(Data::filterExternalIdType($externalId->getIdType()));
       }
         $this->externalIds[] = $externalId;
         return $this;
     }

     /**
      * you have not to set put-code for sending
      * put-code is required to update but not to send
      * if you have decided to set put code check if its not empty
      * empty value is not accepted
      * @param string $putCode
      * @return $this
      * @throws Exception
      */
     public function setPutCode(string $putCode)
     {
         if (!Data::isValidPutCode($putCode)) {
             throw new Exception("The put-code of work must be numeric and not empty,you try to set a value which is not numeric or is empty");
         }
         $this->putCode = $putCode;
         return $this;
     }

     /**
      * type is required , empty value is not accepted
      * @param string $workType
      * @return $this
      * @throws Exception
      */
     public function setType(string $workType)
     {
         if (empty($workType)) {
             throw new Exception("The type of work must be string and not empty,you try to set empty value");
         }
         if ($this->hasFilter()){
             $workType = Data::filterWorkType($workType);
         }
         if (!Data::isValidWorkType($workType)) {
             throw new Exception("The type of work  '" . $workType . "'  you try to set is not valid for orcid work, here are the valid work-type: [".
             implode(",",Data::WORK_TYPES)."].");
         }
         $this->type = $workType;
         return $this;
     }

     /**
      * @param Data\Title $titles
      * @return $this
      */
     public function setTitles(Data\Title $titles){
         $this->titles=$titles;
         return $this;
      }

     /**
      * title is required , empty value is not accepted
      * @param string $title
      * @param string $translatedTitle
      * @param string $translatedTitleLanguageCode
      * @return $this
      * @throws Exception
      */
     public function setTitle(string $title, $translatedTitle = '', $translatedTitleLanguageCode = '')
     {
         $this->getTitles()->setValue($title)->setTranslatedTitle($translatedTitle)->setTranslatedTitleLanguageCode($translatedTitleLanguageCode);
         return $this;
     }

     /**
      * if you add empty subtitle or translated title we just won't set it because
      * we consider that you don't want to add subtitle/translated title
      * empty subtitle is not useful
      * Then you don't need to check if your string is empty to set
      * @param string $subTitle
      * @return OAbstractWork
      * @throws Exception
      */
     public function setSubTitle(string $subTitle)
     {
             $this->getTitles()->setSubtitle($subTitle);
             return $this;
     }

     /**
      * if you add empty translated title we just won't set it because
      * we consider that you don't want to add translated title
      * empty translated title is not useful .
      * Then you don't need to check if your string is empty to set
      * if you add translated title is required to add the language code
      * otherwise your translated title won't be taken into account
      * @param string $translatedTitle
      * @return $this
      * @throws Exception
      */
     public function setTranslatedTitle(string $translatedTitle)
     {
         if(!empty($translatedTitle)){
             $this->getTitles()->setTranslatedTitle($translatedTitle);
         }
         return $this;
     }

     /**
      * if you send empty string for translated title languageCode
      * it won't be taken in account, then even if you add non empty
      * translated title it won't be possible to send it because both must
      * not be empty
      * @param string $translatedTitleLanguageCode
      * @return $this
      * @throws Exception
      */
     public function setTranslatedTitleLanguageCode(string $translatedTitleLanguageCode)
     {
         if(!empty($translatedTitleLanguageCode)){
             $this->getTitles()->setTranslatedTitleLanguageCode($translatedTitleLanguageCode);
         }
         return $this;
     }


     /**
      * the publication date is not required but the year must not to be empty if you decided to send publication
      * with empty year it won't be added. Check your side if the year  is not empty before to add it. if you set non empty day
      * but empty month , your day won't be send to orcid , just the year will be send like publication date
      * @param string $year
      * @param string $month
      * @param string $day
      * @return $this
      * @throws Exception
      */
     public function setPublicationDate(string $year, $month = '', $day = '')
     {
         if (!empty($year)) {
             $this->publicationDate = new PublicationDate($year,$month,$day);
         }
         return $this;
     }

     /**
      * @param PublicationDate $publicationDate
      * @return $this
      */
     public function setPubDate(PublicationDate $publicationDate)
     {
         if ($publicationDate->isValid()) {
             $this->publicationDate = $publicationDate;
         }
         return $this;
     }


     /**
      * @return Data\Title
      */
     public function getTitles() : Data\Title
     {
         return isset($this->titles)?$this->titles: $this->setTitles(new Data\Title($this->filter))->getTitles();
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
     public function getPutCode()
     {
         return $this->putCode;
     }


     /**
      * @return PublicationDate
      */
     public function getPublicationDate()
     {
         return $this->publicationDate;
     }

     /**
      * @return string
      */
     public function getSubTitle()
     {
         return $this->getTitles()->getSubtitle();
     }

     /**
      * @return string
      */
     public function getTranslatedTitle()
     {
         return $this->getTitles()->getTranslatedTitle();
     }
     /**
      * @return string
      */
     public function getTranslatedTitleLanguageCode()
     {
         return $this->getTitles()->getTranslatedTitleLanguageCode();
     }

     /**
      * @return string
      */
     public function getTitle()
     {
         return $this->getTitles()->getValue();
     }


     /**
      * @return array
      */
     public function getExternalIds()
     {
         return $this->externalIds;
     }
}
