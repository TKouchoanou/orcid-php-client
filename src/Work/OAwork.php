<?php


namespace Orcid\Work;


 abstract  class OAwork
{
     const ID_TYPE = 'idType';
     const ID_VALUE = 'idValue';
     const ID_URL = 'idUrl';
     const EXTERNAL_ID_RELATION_TYPE=['self', 'part-of'];
     const CITATION_FORMATS=['formatted-unspecified', 'bibtex', 'ris', 'formatted-apa', 'formatted-harvard', 'formatted-ieee', 'formatted-mla', 'formatted-vancouver', 'formatted-chicago'];
     const LANGAGE_CODES = ['en', 'ab', 'aa', 'af', 'ak', 'sq', 'am', 'ar', 'an', 'hy', 'as', 'av', 'ae', 'ay', 'az', 'bm', 'ba', 'eu', 'be', 'bn', 'bh', 'bi', 'bs', 'br', 'bg', 'my', 'ca', 'ch', 'ce', 'zh_CN', 'zh_TW', 'cu',
         'cv', 'kw', 'co', 'cr', 'hr', 'cs', 'da', 'dv', 'nl', 'dz', 'en', 'eo', 'et', 'ee', 'fo', 'fj', 'fi', 'fr', 'fy', 'ff', 'gl', 'lg', 'ka', 'de', 'el', 'kl', 'gn', 'gu', 'ht', 'ha', 'iw', 'hz', 'hi', 'ho', 'hu', 'is',
         'io', 'ig', 'in', 'ia', 'ie', 'iu', 'ik', 'ga', 'it', 'ja', 'jv', 'kn', 'kr', 'ks', 'kk', 'km', 'ki', 'rw', 'ky', 'kv', 'kg', 'ko', 'ku', 'kj', 'lo', 'la', 'lv', 'li', 'ln', 'lt', 'lu', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt'
         , 'gv', 'mi', 'mr', 'mh', 'mo', 'mn', 'na', 'nv', 'ng', 'ne', 'nd', 'se', 'no', 'nb', 'nn', 'ny', 'oc', 'oj', 'or', 'om', 'os', 'pi', 'pa', 'fa', 'pl', 'pt', 'ps', 'qu', 'rm', 'ro', 'rn', 'ru', 'sm', 'sg', 'sa', 'sc', 'gd'
         , 'sr', 'sn', 'ii', 'sd', 'si', 'sk', 'sl', 'so', 'nr', 'st', 'es', 'su', 'sw', 'ss', 'sv', 'tl', 'ty', 'tg', 'ta', 'tt', 'te', 'th', 'bo', 'ti', 'to', 'ts', 'tn', 'tr', 'tk', 'tw', 'ug', 'uk', 'ur', 'uz', 've', 'vi',
         'vo', 'wa', 'cy', 'wo', 'xh', 'ji', 'yo', 'za', 'zu'];

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
         if(!in_array(strtolower($externalIdRelationship),self::EXTERNAL_ID_RELATION_TYPE)){
             throw new \Exception(" externalType : ".$externalIdType." , external value : ".$externalIdValue." , external relationship : ".$externalIdRelationship." . The External Ident type of relationship is not valid");
         }
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
     public function getTitle()
     {
         return $this->title;
     }

     /**
      * @return string
      */
     public function getType()
     {
         return $this->type;
     }

     /**
      * @return array
      */
     public function getExternals(): array
     {
         return $this->externals;
     }

}
