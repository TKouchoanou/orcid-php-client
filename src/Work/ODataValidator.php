<?php

namespace Orcid\Work;

trait ODataValidator
{
    //data validator
    public static function isValidLanguageCode($languageCode)
    {
        return in_array(self::tryToNormalizeLanguageCode($languageCode), OAwork::LANGAGE_CODES);
    }

    public static function isValidCountryCode($country)
    {
        return in_array(self::tryToNormalizeCountryCode($country), OAwork::COUNTRY_CODES);
    }

    public static function isValidWorkType($type)
    {
        return in_array(self::tryToNormalizeWorkType($type), OAwork::WORK_TYPES);
    }

    public static function isValidAuthorRole($role)
    {
        return in_array(self::tryToNormalizeAuthorRole($role), OAwork::AUTHOR_ROLE_TYPE);
    }

    public static function isValidCitationType($citationType)
    {
        return in_array(self::tryToNormalizeCitationType($citationType), OAwork::CITATION_FORMATS);
    }

    public static function isValidAuthorSequence($sequence)
    {
        return in_array(self::tryToNormalizeAuthorSequence($sequence), OAwork::AUTHOR_SEQUENCE_TYPE);
    }
    public static function isValidExternalIdType($idType)
    {
        return in_array(self::tryToNormalizeExternalIdType($idType), OAwork::EXTENAL_ID_TYPE);
    }

    public static function isValidExternalIdRelationType($idType)
    {
        return in_array(self::tryToNormalizeExternalIdRelationType($idType), OAwork::EXTERNAL_ID_RELATION_TYPE);
    }





    //data normalizer
    protected static function tryToNormalizeLanguageCode($languageCode)
    {
        $language_code=str_replace("-", "_", strtolower(trim($languageCode)));
        return array_key_exists($language_code, OAwork::SPECIAL_LANGAGE_CODES) ? OAwork::SPECIAL_LANGAGE_CODES[$language_code] : $language_code;
    }

    protected static function tryToNormalizeCountryCode($country)
    {
        return strtoupper(trim($country));
    }

    protected static function tryToNormalizeWorkType($type)
    {
        return str_replace("_", "-", strtolower(trim($type)));
    }

    protected static function tryToNormalizeAuthorRole($role)
    {
        return str_replace('_', '-', strtolower(trim($role)));
    }

    protected static function tryToNormalizeCitationType($citationType)
    {
        return str_replace('_', '-', strtolower(trim($citationType)));
    }

    protected static function tryToNormalizeAuthorSequence($sequence)
    {
        return strtolower(trim($sequence));
    }

    public static function tryToNormalizeExternalIdType($extIdType)
    {
        return str_replace('_', '-', strtolower(trim($extIdType)));
    }
    public static function tryToNormalizeExternalIdRelationType($type)
    {
        return str_replace('_', '-', strtolower(trim($type)));
    }
}
