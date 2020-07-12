<?php


namespace Orcid\Work;


 abstract  class OAwork
{
     const ID_TYPE  = 'idType';
     const ID_VALUE = 'idValue';
     const ID_URL   = 'idUrl';
     const YEAR     ='year';
     const MONTH    = 'month';
     const DAY      ='day';

     const EXTERNAL_ID_RELATION_TYPE=['self', 'part-of'];
     const CITATION_FORMATS=['formatted-unspecified', 'bibtex', 'ris', 'formatted-apa', 'formatted-harvard', 'formatted-ieee', 'formatted-mla', 'formatted-vancouver', 'formatted-chicago'];
     const LANGAGE_CODES = ['en', 'ab', 'aa', 'af', 'ak', 'sq', 'am', 'ar', 'an', 'hy', 'as', 'av', 'ae', 'ay', 'az', 'bm', 'ba', 'eu', 'be', 'bn', 'bh', 'bi', 'bs', 'br', 'bg', 'my', 'ca', 'ch', 'ce', 'zh_CN', 'zh_TW', 'cu',
         'cv', 'kw', 'co', 'cr', 'hr', 'cs', 'da', 'dv', 'nl', 'dz', 'en', 'eo', 'et', 'ee', 'fo', 'fj', 'fi', 'fr', 'fy', 'ff', 'gl', 'lg', 'ka', 'de', 'el', 'kl', 'gn', 'gu', 'ht', 'ha', 'iw', 'hz', 'hi', 'ho', 'hu', 'is',
         'io', 'ig', 'in', 'ia', 'ie', 'iu', 'ik', 'ga', 'it', 'ja', 'jv', 'kn', 'kr', 'ks', 'kk', 'km', 'ki', 'rw', 'ky', 'kv', 'kg', 'ko', 'ku', 'kj', 'lo', 'la', 'lv', 'li', 'ln', 'lt', 'lu', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt'
         , 'gv', 'mi', 'mr', 'mh', 'mo', 'mn', 'na', 'nv', 'ng', 'ne', 'nd', 'se', 'no', 'nb', 'nn', 'ny', 'oc', 'oj', 'or', 'om', 'os', 'pi', 'pa', 'fa', 'pl', 'pt', 'ps', 'qu', 'rm', 'ro', 'rn', 'ru', 'sm', 'sg', 'sa', 'sc', 'gd'
         , 'sr', 'sn', 'ii', 'sd', 'si', 'sk', 'sl', 'so', 'nr', 'st', 'es', 'su', 'sw', 'ss', 'sv', 'tl', 'ty', 'tg', 'ta', 'tt', 'te', 'th', 'bo', 'ti', 'to', 'ts', 'tn', 'tr', 'tk', 'tw', 'ug', 'uk', 'ur', 'uz', 've', 'vi',
         'vo', 'wa', 'cy', 'wo', 'xh', 'ji', 'yo', 'za', 'zu'];
     const EXTENAL_ID_TYPE=['agr','ark','arxiv','asin','asin-tld','authenticusid','bibcode','cba','cienciaiul','cit','ctx','dnb','doi','eid','ethos',
         'grant_number','handle','hir','isbn','issn','jfm','jstor','kuid','lccn','lensid','mr','oclc','ol','osti','other-id','pat','pdb','pmc','pmid',
         'proposal-id','rfc','rrid','source-work-id','ssrn','uri','urn','wosuid','zbl'];
     const EXTERNAL_URL_BY_IDTYPE=['arxiv'=>'https://arxiv.org/abs/','asin'=>'http://www.amazon.com/dp/',
         'authenticusid'=>'https://www.authenticus.pt/','bibcode'=>'http://adsabs.harvard.edu/abs/',
         'cienciaiul'=>'https://ciencia.iscte-iul.pt/id/','dnb'=>'https://d-nb.info/',
         'doi'=>'https://doi.org/','ethos'=>'http://ethos.bl.uk/OrderDetails.do?uin=','handle'=>'http://hdl.handle.net/',
         'isbn'=>'https://www.worldcat.org/isbn/', 'issn'=>'https://portal.issn.org/resource/ISSN/','jfm'=>'http://zbmath.org/?format=complete&q=an%3A',
         'jstor'=>'http://www.jstor.org/stable/','kuid'=>'https://koreamed.org/article/','lccn'=>'http://lccn.loc.gov/','lensid'=>'https://www.lens.org/',
         'mr'=>'http://www.ams.org/mathscinet-getitem?mr=','oclc'=>'http://www.worldcat.org/oclc/',
         'ol'=>'http://openlibrary.org/b/', 'osti'=>'http://www.osti.gov/energycitations/product.biblio.jsp?osti_id=',
          'pdb'=>'http://identifiers.org/pdb/','pmc'=>'https://europepmc.org/articles/','pmid'=>'https://www.ncbi.nlm.nih.gov/pubmed/',
         'rfc'=>'https://tools.ietf.org/html/','rrid'=>'https://identifiers.org/rrid/','ssrn'=>'http://papers.ssrn.com/abstract_id=',
          'zbl'=>'http://zbmath.org/?format=complete&q='];
     const WORK_TYPES=['artistic-performance','book-chapter','book-review','book','conference-abstract','conference-paper','conference-poster', 'data-set',
         'dictionary-entry','disclosure','dissertation','edited-book','encyclopedia-entry','invention','journal-article','journal-issue','lecture-speech', 'license',
         'magazine-article','manual','newsletter-article','newspaper-article','online-resource','other','patent','registered-copyright','report','research-technique',
         'research-tool', 'spin-off-company','standards-and-policy','supervised-student-publication','technical-standard','test','translation','trademark','website','working-paper'];

     /**
      * @var string|
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
      * @var string
      */
     protected $subTitle;
     /**
      * @var array
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

     /**
      * possible to add several external id of the same type
      * But you are responsible for what you send
      * @param string $externalIdType
      * @param string $externalIdValue
      * @param string $externalIdUrl
      * @param string $externalIdRelationship
      * @return $this
      * @throws \Exception
      */
     public function addExternalIdent($externalIdType,$externalIdValue,$externalIdUrl,$externalIdRelationship='self'){
         $this->externals[]= new ExternalId($externalIdType,$externalIdValue,$externalIdUrl,$externalIdRelationship);
         return $this;
     }

     /**
      * possible to add several external id of the same type
      * But you are responsible for what you send
      * @param ExternalId $externalId
      * @return $this
      */
     public function addNewExternalIdent(ExternalId $externalId){
         $this->externals[]= $externalId;
         return $this;
     }

     /**
      * you have not to set putcode for sending
      * putcode is required to update but not to send
      * if you have decided to set put code check if its not empity
      * empity value is not accepted
      * @param string $putCode
      * @return $this
      * @throws \Exception
      */
     public function setPutCode(string $putCode)
     {
         if(empty($putCode)||!is_numeric($putCode)){
             throw new \Exception("The putcode of work must be numéric and not empity,you try to set a value which is not numercic or is empity");
         }
         $this->putCode = $putCode;
         return $this;
     }

     /**
      * type is required , empity value is not accepted
      * @param string $type
      * @return $this
      */
     public function setType(string $type)
     {
         $workType= strtolower(str_replace("_", "-", $type));
         if(empty($type)){
             throw new \Exception("The type of work must be string and not empity,you try to set empity value");
         }
         if(!in_array($workType,self::WORK_TYPES)){
             throw new \Exception("The type of work  '".$type."'  you try to set is not valid for orcid work");
         }
         $this->type = $workType;
         return $this;
     }

     /**
      * title is required , empity value is not accepted
      * @param string $title
      * @param string $translatedTitle
      * @param string $translatedTitleLanguageCode
      * @return $this
      */
     public function setTitle(string $title, $translatedTitle='',$translatedTitleLanguageCode='')
     {
         if(empty($title)){
             throw new \Exception("The title of work must be string and not empity,you try to set the value which is empity");
         }
         $this->title = $title;
         $this->setTranslatedTitle($translatedTitle);
         $this->setTranslatedTitleLanguageCode($translatedTitleLanguageCode);
         return $this;
     }

     /**
      * if you add empity subtitle or translatedtitle we just won't set it because
      * we consider that you don't want to add subtitle/translated title
      * empity subtitle is not useful
      * Then you don't neead to check if your string is empity to set
      * @param string $subTitle
      */
     public function setSubTitle(string $subTitle)
     {
         if(!empty($subTitle)){
             $this->subTitle = $subTitle;
         }
         return $this;
     }

     /**
      * if you add empity translated title we just won't set it because
      * we consider that you don't want to add translated title
      * empity translated title is not useful .
      * Then you don't neead to check if your string is empity to set
      * if you add transleted title is required to add the langage code
      * otherwise your transleted title won't be taken into account
      * @param string $translatedTitle
      * @return $this
      */
     public function setTranslatedTitle(string $translatedTitle)
     {
         if(!empty($translatedTitle)){
             $this->translatedTitle = $translatedTitle;
         }
         return $this;
     }

     /**
      * if you send empity string or transleted title languagecode
      * it won't be taken in account, then even if you add non empity
      * transleted title it won't be possible to send it because both must
      * not be empity
      * @param string $translatedTitleLanguageCode
      * @return $this
      * @throws \Exception
      */
     public function setTranslatedTitleLanguageCode(string $translatedTitleLanguageCode)
     {
         if(!empty($translatedTitleLanguageCode) && in_array($translatedTitleLanguageCode,self::LANGAGE_CODES)) {
             $this->translatedTitleLanguageCode = $translatedTitleLanguageCode;
         }elseif (!empty($translatedTitleLanguageCode)&&!in_array($translatedTitleLanguageCode,self::LANGAGE_CODES)){
             throw new \Exception("The transleted langage code must be a string of two or three character and must respect ISO 3166 rules for country ");
         }
         return $this;
     }


     /**
      * the publication date is not required but the year must not to be empity if you decided to send publication
      * date. Check your side if it is not empty before to add it
      * @param string $year
      * @param string $month
      * @param string $day
      * @return $this
      * @throws \Exception
      */
     public function setPublicationDate(string $year,string $month='',string $day='')
     {
         if(empty($year)) {
             $message=" \n The year must not be empity you try to set publication date with empity year ";
         }

         if(!is_numeric($year)||mb_strlen($year)>4){
             $message=" \n The year must be a string made up of four numeric characters or be a number of four digits. You have send Year=".$year;
         }

         if( (!empty($month)&&(!is_numeric($month)||mb_strlen((string)$month)>2||(int)$month>12||(int)$month<1))) {
             $message.=" \n The month must be a numeric string or a integer whose value is between 1 and 12. You have send Month=".$month;
         }

         if(!empty($day)&&(!is_numeric($day)||strlen((string)$day)>2||(int)$day>31||(int)$day<1))  {
             $message.=" \n The day must be a numeric string or a number whose value is between 1 and 31. You have send Day=".$day;
         }

         if(isset($message)){
             throw new \Exception($message);
         }
         $this->publicationDate = [self::YEAR=>$year,self::MONTH=>$month,self::DAY=>$day] ;
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
      * @return string
      */
     public function getPutCode()
     {
         return $this->putCode;
     }


     /**
      * @return array
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
         return $this->subTitle;
     }

     /**
      * @return string
      */
     public function getTranslatedTitle()
     {
         return $this->translatedTitle;
     }
     /**
      * @return array
      */
     public function getExternals(): array
     {
         return $this->externals;
     }

}
