<?php


namespace Orcid\Work\Data;


use Orcid\Work\Work\OAbstractWork;

trait ODataFilter
{
    /**
     * @param string $languageCode
     * @return string
     */
    public static function filterLanguageCode(string $languageCode)
    {
        $language_code=str_replace("-", "_", strtolower(trim($languageCode)));
        return array_key_exists($language_code, OAbstractWork::SPECIAL_LANGUAGE_CODES)?OAbstractWork::SPECIAL_LANGUAGE_CODES[$language_code]:$language_code;
    }

    /**
     * @param string $country
     * @return string
     */
    public static function filterCountryCode(string $country)
    {
        return strtoupper(trim($country));
    }

    /**
     * @param $type
     * @return string
     */
    public static function filterWorkType(string $type)
    {
        return str_replace("_", "-", strtolower(trim($type)));
    }

    /**
     * @param string $role
     * @return string
     */
    public static function filterContributorRole(string $role)
    {
        return str_replace('_', '-', strtolower(trim($role)));
    }

    /**
     * @param $sequence
     * @return string
     */
    public static function filterContributorSequence($sequence){
        return strtolower(trim($sequence));
    }

    /**
     * @param string $orcidId
     * @return string
     */
    public static function filterOrcid(string $orcidId){
        return str_replace('_', '-', strtolower(trim($orcidId)));
    }

    /**
     * @param string $citationType
     * @return string
     */
    public static function filterCitationType(string $citationType)
    {
        return str_replace('_', '-', strtolower(trim($citationType)));
    }

    /**
     * @param string $extIdType
     * @return string
     */
    public static function filterExternalIdType(string $extIdType){
        return str_replace('_', '-', strtolower(trim($extIdType)));
    }

    /**
     * @param string $type
     * @return string
     */
    public static function filterExternalIdRelationType(string $type){
        return str_replace('_', '-', strtolower(trim($type)));
    }
}