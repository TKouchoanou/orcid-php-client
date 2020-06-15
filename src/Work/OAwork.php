<?php


namespace Orcid\Work;


 abstract  class OAwork
{

     const ID_TYPE = 'idType';
     const ID_VALUE = 'idValue';
     const ID_URL = 'idUrl';

     /**
      * @var string|int|
      */
     protected $putCode;
     /**
      * @var string
      */
     protected $title;
     /**
      * @var string
      */
     protected $translatedTitle;
     /**
      * @var string
      */
     protected $translatedTitleLanguageCode;
     /**
      * @var array
      */
     protected $subTitles=[];
     /**
      * @var string
      */
     protected $journalTitle;
     /**
      * @var string
      */

     protected $publicationDate;
     /**
      * @var array
      */
     protected $externals=[];

     /**
      * @var string
      */
     protected $type;


     public function __construct()
     {
     }

     public function addExternalIdent($externalIdType,$externalIdValue,$externalIdUrl,$externalIdRelationship='self'){
         $this->externals[]= new ExternalId($externalIdType,$externalIdValue,$externalIdUrl,$externalIdRelationship);
     }
     public function addNewExternalIdent(ExternalId $externalId){
         $this->externals[]= $externalId;
     }
     /**
      * @param string $type
      * @return $this
      */
     public function setType($type)
     {
       if(empty($type)||!is_string($type)){
             throw new \Exception("The type of work must be string and not empity,you try to set the value which is not string or empity");
         }
         $this->type = $type;
         return $this;

     }

     /**
      * @param string $title
      * @param string $translatedTitle
      * @param string $translatedTitleLanguageCode
      * @return $this
      */
     public function setTitle($title, $translatedTitle='',$translatedTitleLanguageCode='')
     {
        if(empty($title)||!is_string($title)){
                throw new \Exception("The title of work must be string and not empity,you try to set the value which is not string or empity");
         }
      
         $this->title = $title;
         $this->setTranslatedTitle($translatedTitle);
         $this->setTranslatedTitleLanguageCode($translatedTitleLanguageCode);
         return $this;
     }

     /**
      * @param string $translatedTitle
      */
     public function setTranslatedTitle($translatedTitle)
     {
         if(!empty($translatedTitle) && is_string($translatedTitle)){
             $this->translatedTitle = $translatedTitle;
         }
         return $this;
     }


     /**
      * @param string $subTitles
      */
     public function addSubTitle(string $subTitle)
     {
         if(!empty($subTitle)){
             $this->subTitles[] = $subTitle;
         }
         return $this;
     }


     /**
      * @param $putCode
      * @return $this
      */
     public function setPutCode($putCode)
     {
         if(empty($putCode)||!is_numeric($putCode)){
             throw new \Exception("The putcode of work must be numéric and not empity,you try to set a value which is not numercic or empity");
         }

         $this->putCode = $putCode;

         return $this;
     }

     /**
      * @param string $translatedTitleLanguageCode
      */
     public function setTranslatedTitleLanguageCode($translatedTitleLanguageCode)
     {
         if(!empty($translatedTitleLanguageCode) && is_string($translatedTitleLanguageCode)) {
             $this->translatedTitleLanguageCode = $translatedTitleLanguageCode;
         }
         return $this;
     }


     /**
      * @param $date
      * @return $this
      */
     public function setPublicationDate($year,$month='',$day='')
     {

         if(!empty($month)&&(!is_numeric($year)||strlen((string)$year)>4)){
             $message=" \n The year must be a string made up of four numeric characters or be a number of four digits. You have send Year=".$year;
         }

         if( (!empty($month)&&(!is_numeric($month)||strlen((string)$month)>2||(int)$month>12||(int)$month<1))) {
             $message.=" \n The month must be a numeric string or a integer whose value is between 1 and 12. You have send Month=".$month;
         }

         if(!empty($day)&&(!is_numeric($day)||strlen((string)$day)>2||(int)$day>31||(int)$day<1))  {
             $message.=" \n The day must be a numeric string or a number whose value is between 1 and 31. You have send Day=".$day;
         }

         if(isset($message)){
             throw new Exception($message);
         }
         $this->publicationDate = ['year'=>$year,'month'=>$month,'day'=>$day] ;
         return $this;
     }
  
     /**
      * @return string
      */
     public function getType(): string
     {
         return $this->type;
     }

     /**
      * @return string
      */
     public function getTitle(): string
     {
         return $this->title;
     }
  
  
     /**
     * @return array
     */
    public function getExternals(): array
    {
        return $this->externals;
    }

}
