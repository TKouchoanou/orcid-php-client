<?php


namespace Orcid\Work\Data;


class Data
{
    use ODataValidator; use ODataFilter;
    const PUBLICATION_DATE_MIN_YEAR=1900;
    const PUBLICATION_DATE_MAX_YEAR=2100;
    const SHORT_DESCRIPTION_AUTHORIZE_MAX_LENGTH=5000;
    const CITATION_MAX_LENGTH=1000;
    const TITLE_MAX_LENGTH=1000;
    const SUBTITLE_MAX_LENGTH=1000;
    const TRANSLATED_MAX_LENGTH=1000;
    /**
     * https://members.orcid.org/api/resources/supported-work-identifiers
     * ,'version-of' is not currently accepted
     */
    const EXTERNAL_ID_RELATION_TYPE = ['self', 'part-of'];

    /**
     *
     */
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
    /**
     *
     */
    const SPECIAL_LANGUAGE_CODES = ['zh_cn' => 'zh_CN', 'ZH_CN' => 'zh_CN', 'zh_tw' => 'zh_TW', 'ZH_TW' => 'zh_TW'];

    /** orcid accepts countries that meet the standard iso-3166-country-or-empty http://documentation.abes.fr/sudoc/formats/CodesPays.htm */
    const COUNTRY_CODES = ['AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM', 'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ', 'BM', 'BT', 'BO', 'BQ', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI', 'CV', 'KH', 'CM', 'CA', 'KY',
        'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO', 'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CW', 'CY', 'CZ', 'DK', 'DJ', 'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'SZ', 'ET', 'FK', 'FO', 'FJ', 'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR', 'GL',
        'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA', 'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT', 'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA', 'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MG', 'MW',
        'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD', 'MC', 'MN', 'ME', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'NC', 'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MK', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS', 'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR',
        'QA', 'RE', 'RO', 'RU', 'RW', 'BL', 'SH', 'KN', 'LC', 'MF', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN', 'RS', 'SC', 'SL', 'SG', 'SX', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'SS', 'ES', 'LK', 'SD', 'SR', 'SJ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL', 'TG', 'TK', 'TO', 'TT',
        'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE', 'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH', 'YE', 'ZM', 'ZW'];
    /**
     * https://pub.orcid.org/v2.0/identifiers
     */
    const EXTERNAL_ID_TYPE = ['agr', 'ark', 'arxiv', 'asin', 'asin-tld', 'authenticusid', 'bibcode', 'cba', 'cienciaiul', 'cit', 'ctx', 'dnb', 'doi', 'eid', 'ethos',
        'grant_number', 'handle','hal', 'hir', 'isbn', 'issn', 'jfm', 'jstor', 'kuid', 'lccn', 'lensid', 'mr', 'oclc', 'ol', 'osti', 'other-id', 'pat', 'pdb', 'pmc', 'pmid',
        'proposal-id', 'rfc', 'rrid', 'source-work-id', 'ssrn', 'uri', 'urn', 'wosuid', 'zbl'];
    /**
     * https://pub.orcid.org/v2.0/identifiers
     */
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
    /**
     * https://members.orcid.org/api/resources/work-types
     */
    const WORK_TYPES = ['artistic-performance', 'book-chapter', 'book-review', 'book', 'conference-abstract', 'conference-paper', 'conference-poster', 'data-set',
        'dictionary-entry', 'disclosure', 'dissertation', 'edited-book', 'encyclopedia-entry', 'invention', 'journal-article', 'journal-issue', 'lecture-speech', 'license',
        'magazine-article', 'manual', 'newsletter-article', 'newspaper-article', 'online-resource', 'other', 'patent', 'registered-copyright', 'report', 'research-technique',
        'research-tool', 'spin-off-company', 'standards-and-policy', 'supervised-student-publication', 'technical-standard', 'test', 'translation', 'trademark', 'website', 'working-paper'];
    /**
     *
     */
    const AUTHOR_SEQUENCE_TYPE = ['first', 'additional'];
    /**
     *
     */
    const AUTHOR_ROLE_TYPE = ['author', 'assignee', 'editor', 'chair-or-translator', 'co-investigator', 'co-inventor', 'graduate-student', 'other-inventor', 'principal-investigator', 'postdoctoral-researcher', 'support-staff'];

}