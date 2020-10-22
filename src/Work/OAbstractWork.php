<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
namespace Orcid\Work;


 use Exception;

 abstract  class OAbstractWork
 {
     use ODataValidator; use ODataFilter;
     const PUBLICATION_DATE_MIN_YEAR=1900;
     const PUBLICATION_DATE_MAX_YEAR=2100;
     const SHORT_DESCRIPTION_AUTHORIZE_MAX_LENGTH=5000;
     const CITATION_MAX_LENGTH=1000;
     const EXTERNAL_ID_RELATION_TYPE = ['self', 'part-of'];
     const CITATION_FORMATS = ['formatted-unspecified', 'bibtex', 'ris', 'formatted-apa', 'formatted-harvard', 'formatted-ieee', 'formatted-mla', 'formatted-vancouver', 'formatted-chicago'];
     /**
      *language code which is accepted by orcid  don't contains: zh Sino-Tibetan Chinese, he Afro-Asiatic Hebrew ,id Austronesian Indonesian,yi  Indo-European Yiddish
      */
     const LANGUAGE_CODES = ['en', 'ab', 'aa', 'af', 'ak', 'sq', 'am', 'ar', 'an', 'hy', 'as', 'av', 'ae', 'ay', 'az', 'bm', 'ba', 'eu', 'be', 'bn', 'bh', 'bi', 'bs', 'br', 'bg', 'my', 'ca', 'ch', 'ce', 'zh_CN', 'zh_TW', 'cu',
         'cv', 'kw', 'co', 'cr', 'hr', 'cs', 'da', 'dv', 'nl', 'dz', 'en', 'eo', 'et', 'ee', 'fo', 'fj', 'fi', 'fr', 'fy', 'ff', 'gl', 'lg', 'ka', 'de', 'el', 'kl', 'gn', 'gu', 'ht', 'ha', 'iw', 'hz', 'hi', 'ho', 'hu', 'is',
         'io', 'ig', 'in', 'ia', 'ie', 'iu', 'ik', 'ga', 'it', 'ja', 'jv', 'kn', 'kr', 'ks', 'kk', 'km', 'ki', 'rw', 'ky', 'kv', 'kg', 'ko', 'ku', 'kj', 'lo', 'la', 'lv', 'li', 'ln', 'lt', 'lu', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt'
         , 'gv', 'mi', 'mr', 'mh', 'mo', 'mn', 'na', 'nv', 'ng', 'ne', 'nd', 'se', 'no', 'nb', 'nn', 'ny', 'oc', 'oj', 'or', 'om', 'os', 'pi', 'pa', 'fa', 'pl', 'pt', 'ps', 'qu', 'rm', 'ro', 'rn', 'ru', 'sm', 'sg', 'sa', 'sc', 'gd'
         , 'sr', 'sn', 'ii', 'sd', 'si', 'sk', 'sl', 'so', 'nr', 'st', 'es', 'su', 'sw', 'ss', 'sv', 'tl', 'ty', 'tg', 'ta', 'tt', 'te', 'th', 'bo', 'ti', 'to', 'ts', 'tn', 'tr', 'tk', 'tw', 'ug', 'uk', 'ur', 'uz', 've', 'vi',
         'vo', 'wa', 'cy', 'wo', 'xh', 'ji', 'yo', 'za', 'zu'];

     const SPECIAL_LANGUAGE_CODES = ['zh_cn' => 'zh_CN', 'ZH_CN' => 'zh_CN', 'zh_tw' => 'zh_TW', 'ZH_TW' => 'zh_TW'];

     /** orcid accepts countries that meet the standard iso-3166-country-or-empty http://documentation.abes.fr/sudoc/formats/CodesPays.htm */
     const COUNTRY_CODES = ['AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'CV', 'KH', 'CM', 'CA', 'KY',
         'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'SZ', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL',
         'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT', 'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MG', 'MW',
         'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MK', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR',
         'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO', 'TT',
         'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW'];

     const EXTERNAL_ID_TYPE = ['agr', 'ark', 'arxiv', 'asin', 'asin-tld', 'authenticusid', 'bibcode', 'cba', 'cienciaiul', 'cit', 'ctx', 'dnb', 'doi', 'eid', 'ethos',
         'grant_number', 'handle','hal', 'hir', 'isbn', 'issn', 'jfm', 'jstor', 'kuid', 'lccn', 'lensid', 'mr', 'oclc', 'ol', 'osti', 'other-id', 'pat', 'pdb', 'pmc', 'pmid',
         'proposal-id', 'rfc', 'rrid', 'source-work-id', 'ssrn', 'uri', 'urn', 'wosuid', 'zbl'];
     const EXTERNAL_URL_BY_IDTYPE = ['arxiv' => 'https://arxiv.org/abs/', 'asin' => 'http://www.amazon.com/dp/',
         'authenticusid' => 'https://www.authenticus.pt/', 'bibcode' => 'http://adsabs.harvard.edu/abs/',
         'cienciaiul' => 'https://ciencia.iscte-iul.pt/id/', 'dnb' => 'https://d-nb.info/',
         'doi' => 'https://doi.org/', 'ethos' => 'http://ethos.bl.uk/OrderDetails.do?uin=', 'handle' => 'http://hdl.handle.net/',
         'isbn' => 'https://www.worldcat.org/isbn/', 'issn' => 'https://portal.issn.org/resource/ISSN/', 'jfm' => 'http://zbmath.org/?format=complete&q=an%3A',
         'jstor' => 'http://www.jstor.org/stable/', 'kuid' => 'https://koreamed.org/article/', 'lccn' => 'http://lccn.loc.gov/', 'lensid' => 'https://www.lens.org/',
         'mr' => 'http://www.ams.org/mathscinet-getitem?mr=', 'oclc' => 'http://www.worldcat.org/oclc/',
         'ol' => 'http://openlibrary.org/b/', 'osti' => 'http://www.osti.gov/energycitations/product.biblio.jsp?osti_id=',
         'pdb' => 'http://identifiers.org/pdb/', 'pmc' => 'https://europepmc.org/articles/', 'pmid' => 'https://www.ncbi.nlm.nih.gov/pubmed/',
         'rfc' => 'https://tools.ietf.org/html/', 'rrid' => 'https://identifiers.org/rrid/', 'ssrn' => 'http://papers.ssrn.com/abstract_id=',
         'zbl' => 'http://zbmath.org/?format=complete&q='];
     const WORK_TYPES = ['artistic-performance', 'book-chapter', 'book-review', 'book', 'conference-abstract', 'conference-paper', 'conference-poster', 'data-set',
         'dictionary-entry', 'disclosure', 'dissertation', 'edited-book', 'encyclopedia-entry', 'invention', 'journal-article', 'journal-issue', 'lecture-speech', 'license',
         'magazine-article', 'manual', 'newsletter-article', 'newspaper-article', 'online-resource', 'other', 'patent', 'registered-copyright', 'report', 'research-technique',
         'research-tool', 'spin-off-company', 'standards-and-policy', 'supervised-student-publication', 'technical-standard', 'test', 'translation', 'trademark', 'website', 'working-paper'];
     const AUTHOR_SEQUENCE_TYPE = ['first', 'additional'];
     const AUTHOR_ROLE_TYPE = ['author', 'assignee', 'editor', 'chair-or-translator', 'co-investigator', 'co-inventor', 'graduate-student', 'other-inventor', 'principal-investigator', 'postdoctoral-researcher', 'support-staff'];
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
      * @var PublicationDate
      */
     protected $publicationDate;
     /**
      * @var array
      */
     protected $externals = [];
     /**
      * @var string
      */
     protected $type;
     /**
      * @var bool
      */
     protected $filterData=true;


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
         if($this->isFilterData()){
             $externalIdType =self::filterExternalIdType($externalIdType);
             $externalIdRelationship=self::filterExternalIdRelationType($externalIdRelationship);
         }
         $this->externals[] = new ExternalId($externalIdType, $externalIdValue, $externalIdUrl, $externalIdRelationship);
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
     { if($this->isFilterData()){
         $externalId->setIdRelationship(self::filterExternalIdRelationType($externalId->getIdRelationship()));
         $externalId->setIdType(self::filterExternalIdType($externalId->getIdType()));
       }
         $this->externals[] = $externalId;
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
         if (!self::isValidPutCode($putCode)) {
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
         if ($this->isFilterData()){
             $workType = self::filterWorkType($workType);
         }
         if (!self::isValidWorkType($workType)) {
             throw new Exception("The type of work  '" . $workType . "'  you try to set is not valid for orcid work, here are the valid work-type: [".
             implode(",",self::WORK_TYPES)."].");
         }
         $this->type = $workType;
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
         if (empty($title)) {
             throw new Exception("The title of work must be string and not empty,you try to set the value which is empty");
         }
         $this->title = $title;
         $this->setTranslatedTitle($translatedTitle);
         $this->setTranslatedTitleLanguageCode($translatedTitleLanguageCode);
         return $this;
     }

     /**
      * if you add empty subtitle or translated title we just won't set it because
      * we consider that you don't want to add subtitle/translated title
      * empty subtitle is not useful
      * Then you don't need to check if your string is empty to set
      * @param string $subTitle
      * @return OAbstractWork
      */
     public function setSubTitle(string $subTitle)
     {
         if (!empty($subTitle)) {
             $this->subTitle = $subTitle;
         }
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
      */
     public function setTranslatedTitle(string $translatedTitle)
     {
         if (!empty($translatedTitle)) {
             $this->translatedTitle = $translatedTitle;
         }
         return $this;
     }

     /**
      * if you send empty string or translated title languageCode
      * it won't be taken in account, then even if you add non empty
      * translated title it won't be possible to send it because both must
      * not be empty
      * @param string $translatedTitleLanguageCode
      * @return $this
      * @throws Exception
      */
     public function setTranslatedTitleLanguageCode(string $translatedTitleLanguageCode)
     {

         if (!empty($translatedTitleLanguageCode)) {
             if($this->isFilterData()){
                 $translatedTitleLanguageCode=self::filterLanguageCode($translatedTitleLanguageCode);
             }
             if(!self::isValidLanguageCode($translatedTitleLanguageCode)){
                 throw new Exception("Your language code is not valid. here are valid language code: [".implode(",",self::LANGUAGE_CODES)."] ".
                     "if you want to set it by force use the method setPropertyByForce('property','value')");
             }
             $this->translatedTitleLanguageCode=$translatedTitleLanguageCode;
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
      * Consciously allows you to put any value in a property without checking the validity with orcid
      * @param string $property
      * @param $value
      * @return OAbstractWork
      * @throws Exception
      */
    public function setPropertyByForce(string $property,$value){
      if(property_exists($this,$property)){
          $this->{$property}=$value;
      }else{
          throw new Exception("your property ".$property." don't exist this objet property are : ".
              implode(", ",array_keys(get_object_vars ($this))).".");
      }
      return $this;
    }

     /**
      * @param bool $filterData
      * @return OAbstractWork
      */
     public function setFilterData(bool $filterData)
     {
         $this->filterData = $filterData;
         return $this;
     }

     /**
      * @return bool
      */
     public function isFilterData(): bool
     {
         return $this->filterData;
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
     public function getExternals()
     {
         return $this->externals;
     }
}
