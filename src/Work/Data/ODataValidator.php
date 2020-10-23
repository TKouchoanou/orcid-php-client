<?php


namespace Orcid\Work\Data;


use Orcid\Work\Work\OAbstractWork;

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
        return in_array($languageCode,OAbstractWork::LANGUAGE_CODES);
    }

    /**
     * @param $country
     * @return bool
     */
    public static function isValidCountryCode($country)
    {
        return in_array($country,OAbstractWork::COUNTRY_CODES);
    }

    /**
     * @param $type
     * @return bool
     */
    public static function isValidWorkType($type)
    {
        return in_array($type,OAbstractWork::WORK_TYPES);
    }

    /**
     * @param $role
     * @return bool
     */
    public static function isValidContributorRole($role)
    {
        return in_array($role, OAbstractWork::AUTHOR_ROLE_TYPE);
    }

    /**
     * @param $sequence
     * @return bool
     */
    public static function isValidContributorSequence($sequence){
        return in_array($sequence, OAbstractWork::AUTHOR_SEQUENCE_TYPE);
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
        return in_array($citationType, OAbstractWork::CITATION_FORMATS);
    }

    /**
     * @param $idType
     * @return bool
     */
    public static function isValidExternalIdType($idType){
        return in_array($idType, OAbstractWork::EXTERNAL_ID_TYPE);
    }

    /**
     * @param $idType
     * @return bool
     */
    public static function isValidExternalIdRelationType($idType){
        return in_array($idType, OAbstractWork::EXTERNAL_ID_RELATION_TYPE);
    }

    /**
     * @param string $shortDescription
     * @return bool
     */
    public static function isValidShortDescription(string $shortDescription){
        return mb_strlen($shortDescription)<=OAbstractWork::SHORT_DESCRIPTION_AUTHORIZE_MAX_LENGTH;
    }

    /**
     * @param string $publicationYear
     * @return bool
     */
    public static function isValidPublicationYear(string $publicationYear){
        return is_numeric($publicationYear) && mb_strlen($publicationYear) <= 4
               && (int)$publicationYear >= OAbstractWork::PUBLICATION_DATE_MIN_YEAR && (int)$publicationYear <= OAbstractWork::PUBLICATION_DATE_MAX_YEAR;
    }

    /**
     * @param string $citation
     * @return bool
     */
    public static function isValidCitation(string $citation){
        return mb_strlen($citation)<=OAbstractWork::CITATION_MAX_LENGTH;
    }
}