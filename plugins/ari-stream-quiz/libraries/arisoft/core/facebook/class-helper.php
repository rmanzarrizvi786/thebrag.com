<?php
namespace Ari\Facebook;

class Helper {
    const DEFAULT_LOCALE = 'en_US';

    private static $locales = array(
        'af_ZA', // Afrikaans
        'ar_AR', // Arabic
        'as_IN', //
        'az_AZ', // Azerbaijani
        'be_BY', // Belarusian
        'bg_BG', // Bulgarian
        'bn_IN', // Bengali
        'br_FR', //
        'bs_BA', // Bosnian
        'ca_ES', // Catalan
        'cb_IQ', //
        'co_FR', //
        'cs_CZ', // Czech
        'cx_PH', //
        'cy_GB', // Welsh
        'da_DK', // Danish
        'de_DE', // German
        'el_GR', // Greek
        'en_UD', //
        'en_US', // English (US)
        'en_GB', // English (UK)
        'eo_EO', // Esperanto
        'es_LA', // Spanish
        'es_ES', // Spanish (Spain)
        'et_EE', // Estonian
        'eu_ES', // Basque
        'fa_IR', // Persian
        'fi_FI', // Finnish
        'ff_NG', //
        'fo_FO', // Faroese
        'fr_FR', // French (France)
        'fr_CA', // French (Canada)
        'fy_NL', // Frisian
        'ga_IE', // Irish
        'gl_ES', // Galician
        'gn_PY', //
        'gu_IN', //
        'ha_NG', //
        'he_IL', // Hebrew
        'hi_IN', // Hindi
        'hr_HR', // Croatian
        'hu_HU', // Hungarian
        'hy_AM', // Armenian
        'id_ID', // Indonesian
        'is_IS', // Icelandic
        'it_IT', // Italian
        'ja_KS', //
        'ja_JP', // Japanese
        'jv_ID', //
        'ka_GE', // Georgian
        'kk_KZ', //
        'km_KH', // Khmer
        'kn_IN', //
        'ko_KR', // Korean
        'ku_TR', // Kurdish
        'la_VA', // Latin
        'lt_LT', // Lithuanian
        'lv_LV', // Latvian
        'mg_MG', //
        'mk_MK', // Macedonian
        'mn_MN', //
        'ml_IN', // Malayalam
        'mr_IN', //
        'ms_MY', // Malay
        'mt_MT', //
        'my_MM', //
        'nb_NO', // Norwegian (bokmal)
        'ne_NP', // Nepali
        'nl_BE', //
        'nl_NL', // Dutch
        'nn_NO', // Norwegian (nynorsk)
        'or_IN', //
        'pa_IN', // Punjabi
        'pl_PL', // Polish
        'ps_AF', // Pashto
        'pt_BR', // Portuguese (Brazil)
        'pt_PT', // Portuguese (Portugal)
        'qz_MM', //
        'ro_RO', // Romanian
        'ru_RU', // Russian
        'rw_RW', //
        'sc_IT', //
        'si_LK', //
        'sk_SK', // Slovak
        'sl_SI', // Slovenian
        'sq_AL', // Albanian
        'so_SO', //
        'sr_RS', // Serbian
        'sv_SE', // Swedish
        'sw_KE', // Swahili
        'sz_PL', //
        'ta_IN', // Tamil
        'te_IN', // Telugu
        'tg_TJ', //
        'th_TH', // Thai
        'tl_PH', // Filipino
        'tr_TR', // Turkish
        'tz_MA', //
        'uk_UA', // Ukrainian
        'ur_PK', //
        'uz_UZ', //
        'vi_VN', // Vietnamese
        'zh_CN', // Simplified Chinese (China)
        'zh_HK', // Traditional Chinese (Hong Kong)
        'zh_TW', // Traditional Chinese (Taiwan)
    );

    public static function is_supported_locale( $locale ) {
        return in_array( $locale, self::$locales );
    }

    public static function convert_locale( $locale, $default_locale = self::DEFAULT_LOCALE ) {
        if ( ! is_string( $locale ) )
            return $default_locale;

        $locale_length = strlen( $locale );
        if ( ! ( $locale_length === 2 || $locale_length === 5 ) )
            return $default_locale;

        if ( $locale_length === 2 ) {
            if ( ! ctype_alpha( $locale ) )
                return $default_locale;

            $locale = strtolower( $locale );
            $possible_locale = $locale . '_' . strtoupper( $locale );
            if ( self::is_supported_locale( $possible_locale ) )
                return $possible_locale;

            foreach( self::$locales as $facebook_locale  ) {
                if ( substr_compare( $facebook_locale, $locale, 0, 2 ) === 0 )
                    return $facebook_locale;
            }

            return $default_locale;
        }

        $lang = substr( $locale, 0, 2 );
        $localization = substr( $locale, 3, 2 );

        $possible_locale = strtolower( $lang ) . '_' . strtoupper( $localization );

        return self::is_supported_locale( $possible_locale ) ? $possible_locale : $default_locale;
    }
}
