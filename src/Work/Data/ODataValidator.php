<?php


namespace Orcid\Work\Data;

trait ODataValidator
{
    /**
     * @param $putCode
     * @return bool
     */
    public static function isValidPutCode($putCode)
    {
        return !empty($putCode) && is_numeric($putCode);
    }
    /**
     * @param $languageCode
     * @return bool
     */
    public static function isValidLanguageCode($languageCode)
    {
        return in_array($languageCode,Data::LANGUAGE_CODES);
    }

    /**
     * @param $country
     * @return bool
     */
    public static function isValidCountryCode($country)
    {
        return in_array($country,Data::COUNTRY_CODES);
    }

    /**
     * @param $type
     * @return bool
     */
    public static function isValidWorkType($type)
    {
        return in_array($type,Data::WORK_TYPES);
    }

    /**
     * @param $role
     * @return bool
     */
    public static function isValidContributorRole($role)
    {
        return in_array($role, Data::AUTHOR_ROLE_TYPE);
    }

    /**
     * @param $sequence
     * @return bool
     */
    public static function isValidContributorSequence($sequence){
        return in_array($sequence, Data::AUTHOR_SEQUENCE_TYPE);
    }

    /**
     * @param $orcidID
     * @return false|int
     */
    public static function isValidOrcid($orcidID){
        return preg_match("/(\d{4}-){3,}/",$orcidID);
    }

    /**
     * @param $citationType
     * @return bool
     */
    public static function isValidCitationType($citationType)
    {
        return in_array($citationType, Data::CITATION_FORMATS);
    }

    /**
     * @param string $citation
     * @return bool
     */
    public static function isValidCitation(string $citation){
        return mb_strlen($citation)<=Data::CITATION_MAX_LENGTH;
    }
    /**
     * @param $idType
     * @return bool
     */
    public static function isValidExternalIdType($idType){
        return in_array($idType, Data::EXTERNAL_ID_TYPE);
    }

    /**
     * @param $idType
     * @return bool
     */
    public static function isValidExternalIdRelationType($idType){
        return in_array($idType, Data::EXTERNAL_ID_RELATION_TYPE);
    }

    /**
     * @param string $shortDescription
     * @return bool
     */
    public static function isValidShortDescription(string $shortDescription){
        return mb_strlen($shortDescription)<=Data::SHORT_DESCRIPTION_AUTHORIZE_MAX_LENGTH;
    }

    /**
     * @param string $publicationYear
     * @return bool
     */
    public static function isValidPublicationYear(string $publicationYear){
        return is_numeric($publicationYear) && mb_strlen($publicationYear) <= 4
               && (int)$publicationYear >= Data::PUBLICATION_DATE_MIN_YEAR && (int)$publicationYear <= Data::PUBLICATION_DATE_MAX_YEAR;
    }

    /**
     * @param string $title
     * @return bool
     */
    public static function isValidTitle(string $title){
        return !empty($title) && mb_strlen($title) <=Data::TITLE_MAX_LENGTH;
    }

    /**
     * @param string $subTitle
     * @return bool
     */
    public static function isValidSubTitle(string $subTitle){
        return mb_strlen($subTitle)<=Data::SUBTITLE_MAX_LENGTH;
    }

    /**
     * @param string $translatedTile
     * @return bool
     */
    public static function isValidTranslatedTitle(string $translatedTile){
        return mb_strlen($translatedTile)<=Data::SUBTITLE_MAX_LENGTH;
    }
}