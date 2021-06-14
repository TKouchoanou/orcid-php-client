<?php

/**
 * @package   orcid-php-client
 * @author    Kouchoanou Enagnon Théophane Malo <theophane.kouchoanou@ccsd.cnrs.fr>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */

namespace Orcid\Work;

 use Exception;

 abstract class OAwork
 {
     use ODataValidator;
     public const YEAR = 'year';
     public const MONTH = 'month';
     public const DAY = 'day';

     public const EXTERNAL_ID_RELATION_TYPE = ['self', 'part-of'];
     public const CITATION_FORMATS = ['formatted-unspecified', 'bibtex', 'ris', 'formatted-apa', 'formatted-harvard', 'formatted-ieee', 'formatted-mla', 'formatted-vancouver', 'formatted-chicago'];
     /**
      *language code which is accepted by orcid  don't contains: zh Sino-Tibetan Chinese, he Afro-Asiatic Hebrew ,id Austronesian Indonesian,yi  Indo-European Yiddish
      */
     public const LANGAGE_CODES = ['en', 'ab', 'aa', 'af', 'ak', 'sq', 'am', 'ar', 'an', 'hy', 'as', 'av', 'ae', 'ay', 'az', 'bm', 'ba', 'eu', 'be', 'bn', 'bh', 'bi', 'bs', 'br', 'bg', 'my', 'ca', 'ch', 'ce', 'zh_CN', 'zh_TW', 'cu',
         'cv', 'kw', 'co', 'cr', 'hr', 'cs', 'da', 'dv', 'nl', 'dz', 'en', 'eo', 'et', 'ee', 'fo', 'fj', 'fi', 'fr', 'fy', 'ff', 'gl', 'lg', 'ka', 'de', 'el', 'kl', 'gn', 'gu', 'ht', 'ha', 'iw', 'hz', 'hi', 'ho', 'hu', 'is',
         'io', 'ig', 'in', 'ia', 'ie', 'iu', 'ik', 'ga', 'it', 'ja', 'jv', 'kn', 'kr', 'ks', 'kk', 'km', 'ki', 'rw', 'ky', 'kv', 'kg', 'ko', 'ku', 'kj', 'lo', 'la', 'lv', 'li', 'ln', 'lt', 'lu', 'lb', 'mk', 'mg', 'ms', 'ml', 'mt'
         , 'gv', 'mi', 'mr', 'mh', 'mo', 'mn', 'na', 'nv', 'ng', 'ne', 'nd', 'se', 'no', 'nb', 'nn', 'ny', 'oc', 'oj', 'or', 'om', 'os', 'pi', 'pa', 'fa', 'pl', 'pt', 'ps', 'qu', 'rm', 'ro', 'rn', 'ru', 'sm', 'sg', 'sa', 'sc', 'gd'
         , 'sr', 'sn', 'ii', 'sd', 'si', 'sk', 'sl', 'so', 'nr', 'st', 'es', 'su', 'sw', 'ss', 'sv', 'tl', 'ty', 'tg', 'ta', 'tt', 'te', 'th', 'bo', 'ti', 'to', 'ts', 'tn', 'tr', 'tk', 'tw', 'ug', 'uk', 'ur', 'uz', 've', 'vi',
         'vo', 'wa', 'cy', 'wo', 'xh', 'ji', 'yo', 'za', 'zu'];

     public const SPECIAL_LANGAGE_CODES = ['zh_cn' => 'zh_CN', 'ZH_CN' => 'zh_CN', 'zh_tw' => 'zh_TW', 'ZH_TW' => 'zh_TW'];

     /** orcid accepts countries that meet the standard iso-3166-country-or-empty http://documentation.abes.fr/sudoc/formats/CodesPays.htm */
     public const COUNTRY_CODES = ['AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'CV', 'KH', 'CM', 'CA', 'KY',
         'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'SZ', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL',
         'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT', 'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MG', 'MW',
         'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MK', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR',
         'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO', 'TT',
         'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW'];

     public const EXTENAL_ID_TYPE = ['agr', 'ark', 'arxiv', 'asin', 'asin-tld', 'authenticusid', 'bibcode', 'cba', 'cienciaiul', 'cit', 'ctx', 'dnb', 'doi', 'eid', 'ethos',
         'grant_number', 'handle','hal', 'hir', 'isbn', 'issn', 'jfm', 'jstor', 'kuid', 'lccn', 'lensid', 'mr', 'oclc', 'ol', 'osti', 'other-id', 'pat', 'pdb', 'pmc', 'pmid',
         'proposal-id', 'rfc', 'rrid', 'source-work-id', 'ssrn', 'uri', 'urn', 'wosuid', 'zbl'];
     public const EXTERNAL_URL_BY_IDTYPE = ['arxiv' => 'https://arxiv.org/abs/', 'asin' => 'http://www.amazon.com/dp/',
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
     public const WORK_TYPES = ['artistic-performance', 'book-chapter', 'book-review', 'book', 'conference-abstract', 'conference-paper', 'conference-poster', 'data-set',
         'dictionary-entry', 'disclosure', 'dissertation', 'edited-book', 'encyclopedia-entry', 'invention', 'journal-article', 'journal-issue', 'lecture-speech', 'license',
         'magazine-article', 'manual', 'newsletter-article', 'newspaper-article', 'online-resource', 'other', 'patent', 'registered-copyright', 'report', 'research-technique',
         'research-tool', 'spin-off-company', 'standards-and-policy', 'supervised-student-publication', 'technical-standard', 'test', 'translation', 'trademark', 'website', 'working-paper'];
     public const AUTHOR_SEQUENCE_TYPE = ['first', 'additional'];
     public const AUTHOR_ROLE_TYPE = ['author', 'assignee', 'editor', 'chair-or-translator', 'co-investigator', 'co-inventor', 'graduate-student', 'other-inventor', 'principal-investigator', 'postdoctoral-researcher', 'support-staff'];
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
     protected $externals = [];
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
         $this->externals[] = new ExternalId($externalIdType, $externalIdValue, $externalIdUrl, $externalIdRelationship);
         return $this;
     }

     /**
      * possible to add several external id of the same type
      * But you are responsible for what you send
      * @param ExternalId $externalId
      * @return $this
      */
     public function addNewExternalIdent(ExternalId $externalId)
     {
         $this->externals[] = $externalId;
         return $this;
     }

     /**
      * you have not to set putcode for sending
      * putcode is required to update but not to send
      * if you have decided to set put code check if its not empity
      * empity value is not accepted
      * @param string $putCode
      * @return $this
      * @throws Exception
      */
     public function setPutCode(string $putCode)
     {
         if (empty($putCode) || !is_numeric($putCode)) {
             throw new Exception("The putcode of work must be numéric and not empity,you try to set a value which is not numercic or is empity");
         }
         $this->putCode = $putCode;
         return $this;
     }

     /**
      * type is required , empity value is not accepted
      * @param string $type
      * @return $this
      * @throws Exception
      */
     public function setType(string $type)
     {
         $workType = self::tryToNormalizeWorkType($type);
         if (empty($type)) {
             throw new Exception("The type of work must be string and not empity,you try to set empity value");
         }
         if (!in_array($workType, self::WORK_TYPES)) {
             throw new Exception("The type of work  '" . $type . "'  you try to set is not valid for orcid work, here are the valid worktype: [".
             implode(",", self::WORK_TYPES)."].");
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
      * @throws Exception
      */
     public function setTitle(string $title, $translatedTitle = '', $translatedTitleLanguageCode = '')
     {
         if (empty($title)) {
             throw new Exception("The title of work must be string and not empity,you try to set the value which is empity");
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
      * @return OAwork
      */
     public function setSubTitle(string $subTitle)
     {
         if (!empty($subTitle)) {
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
         if (!empty($translatedTitle)) {
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
      * @throws Exception
      */
     public function setTranslatedTitleLanguageCode(string $translatedTitleLanguageCode)
     {
         if (!empty($translatedTitleLanguageCode)) {
             if (self::isValidLanguageCode($translatedTitleLanguageCode)) {
                 $this->translatedTitleLanguageCode = self::tryToNormalizeLanguageCode($translatedTitleLanguageCode);
             } else {
                 throw new Exception("Your language code is not valid. here are valid langage code: [".implode(",", self::LANGAGE_CODES)."] ".
                     "if you want to set it by force use the method setPropertyByForce('property','value')");
             }
         }
         return $this;
     }


     /**
      * the publication date is not required but the year must not to be empity if you decided to send publication
      * with empity year it won't be added. Check your side if the year  is not empty before to add it
      * @param string $year
      * @param string $month
      * @param string $day
      * @return $this
      * @throws Exception
      */
     public function setPublicationDate(string $year, $month = '', $day = '')
     {
         if (empty($year)) {
             return $this;
         }

         if (!is_numeric($year) || mb_strlen($year) > 4) {
             $message = " \n The year must be a string made up of four numeric characters or be a number of four digits. You have send Year=" . $year;
         }

         if ((int)$year < 1900 || (int)$year > 2100) {
             $message = " The minimum value for orcid work year is 1900 and the maximun value  is 2100. You have send Year=" . $year;
         }

         if ((!empty($month) && (!is_numeric($month) || mb_strlen((string)$month) > 2 || (int)$month > 12 || (int)$month < 1))) {
             $message .= " \n The month must be a numeric string or a integer whose value is between 1 and 12. You have send Month=" . $month;
         }

         if (!empty($day) && (!is_numeric($day) || strlen((string)$day) > 2 || (int)$day > 31 || (int)$day < 1)) {
             $message .= " \n The day must be a numeric string or a number whose value is between 1 and 31. You have send Day=" . $day;
         }

         if (isset($message)) {
             throw new Exception($message);
         }
         $this->publicationDate = [self::YEAR => $year, self::MONTH => $month, self::DAY => $day];
         return $this;
     }

     /**
      * Consciously allows you to put any value in a property without checking the validity with orcid
      * @param string $property
      * @param $value
      * @return OAwork
      * @throws Exception
      */
     public function setPropertyByForce(string $property, $value)
     {
         if (property_exists($this, $property)) {
             $this->{$property}=$value;
         } else {
             throw new Exception("your property ".$property." don't exist this objet property are : ".
              implode(", ", array_keys(get_object_vars($this))).".");
         }
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
     public function getExternals()
     {
         return $this->externals;
     }
 }
