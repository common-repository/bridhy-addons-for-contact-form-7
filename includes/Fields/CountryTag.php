<?php

/**
 * CountryTag Generator
 * @package cf7vb
 */

namespace CF7VB\Fields;

use WPCF7_TagGenerator;

class CountryTag
{
    public function __construct()
    {
        add_action('wpcf7_init', [$this, 'cf7vb_add_form_tag_countryname']);
        add_filter('wpcf7_validate_countryname', [$this, 'cf7vb_countryname_validation_filter'], 10, 2);
        add_filter('wpcf7_validate_countryname*', [$this, 'cf7vb_countryname_validation_filter'], 10, 2);
        add_action('wpcf7_admin_init', [$this, 'cf7vb_add_tag_countryname'], 20);
        add_action('wp_enqueue_scripts', [$this, 'cf7vb_wp_enqueue_script']);
    }

    /**
     * Undocumented function
     * wp_enqueue_script
     * @return void
     */
    public function cf7vb_wp_enqueue_script()
    {

        wp_enqueue_style('cf7vb-select-min', CF7VB_ASSETS . '/css/select2.min.css');
        wp_enqueue_style('cf7vb-country', CF7VB_ASSETS . '/css/country.css');

        wp_enqueue_script('cf7vb-select-min', CF7VB_ASSETS . '/js/select2.min.js', array('jquery'), '4.1.0', true);
        wp_enqueue_script('cf7vb-script', CF7VB_ASSETS . '/js/script.js', array('jquery'), null, true);
    }

    /* form_tag handler */
    public function cf7vb_add_form_tag_countryname()
    {
        wpcf7_add_form_tag(
            array('countryname', 'countryname*'),
            [$this, 'cf7vb_countryname_form_tag_handler'],
            array('name-attr' => true)
        );
    }

    public function cf7vb_countryname_form_tag_handler($tag)
    {
        if (empty($tag->name)) {
            return '';
        }
        $validation_error = wpcf7_get_validation_error($tag->name);
        $class = wpcf7_form_controls_class($tag->type, 'wpcf7-text');
        if (in_array($tag->basetype, array('countryname', 'countryname*'))) {
            $class .= ' wpcf7-validates-as-' . $tag->basetype;
        }
        if ($validation_error) {
            $class .= ' wpcf7-not-valid';
        }

        $atts['class'] = $tag->get_class_option($class);
        $atts['id'] = $tag->get_id_option();
        $atts['id'] = 'cf7vbcountry';
        $atts['tabindex'] = $tag->get_option('tabindex', 'signed_int', true);

        $atts['autocomplete'] = $tag->get_option('autocomplete', '[-0-9a-zA-Z]+', true);

        if ($tag->has_option('readonly')) {
            $atts['readonly'] = 'readonly';
        }

        if ($tag->is_required()) {
            $atts['aria-required'] = 'true';
        }

        if ($validation_error) {
            $atts['aria-invalid'] = 'true';
            $atts['aria-describedby'] = wpcf7_get_validation_error_reference($tag->name);
        } else {
            $atts['aria-invalid'] = 'false';
        }

        $attsname = $tag->name;

        $atts = wpcf7_format_atts($atts);

        $countrylist = $this->cf7vb_country_list($attsname, $atts);

        $html = sprintf(
            '<span class="wpcf7-form-control-wrap" data-name="%1$s">%2$s %3$s</span>',
            sanitize_html_class($tag->name),
            $countrylist,
            $validation_error,


        );
        return $html;
    }

    /**
     * show all country 
     */

    public function cf7vb_country_list($attsname, $atts)
    {
        // write a code here
        $countries = array(
            'AF' => 'Afghanistan',
            'AX' => '&Aring;land Islands',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AS' => 'American Samoa',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas (the)',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia (Plurinational State of)',
            'BA' => 'Bosnia and Herzegovina',
            'BW' => 'Botswana',
            'BV' => 'Bouvet Island',
            'BR' => 'Brazil',
            'IO' => 'British Indian Ocean Territory (the)',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CV' => 'Cabo Verde',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CT' => 'Catalonia',
            'KY' => 'Cayman Islands (the)',
            'CF' => 'Central African Republic (the)',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CX' => 'Christmas Island',
            'CC' => 'Cocos (Keeling) Islands (the)',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CD' => 'Congo (the Democratic Republic of the)',
            'CG' => 'Congo (the)',
            'CK' => 'Cook Islands (the)',
            'CR' => 'Costa Rica',
            'CI' => 'C&ocirc;te d\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic (the)',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic (the)',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'EN' => 'England',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'EU' => 'European Union',
            'FK' => 'Falkland Islands (the) [Malvinas]',
            'FO' => 'Faroe Islands (the)',
            'FJ' => 'Fiji',
            'FI' => 'Finland',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'TF' => 'French Southern Territories (the)',
            'GA' => 'Gabon',
            'GM' => 'Gambia (the)',
            'GE' => 'Georgia',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HM' => 'Heard Island and McDonald Islands',
            'VA' => 'Holy See (the)',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran (Islamic Republic of)',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KP' => 'Korea (the Democratic People\'s Republic of)',
            'KR' => 'Korea (the Republic of)',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Lao People\'s Democratic Republic (the)',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia (the former Yugoslav Republic of)',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands (the)',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'YT' => 'Mayotte',
            'MX' => 'Mexico',
            'FM' => 'Micronesia (Federated States of)',
            'MD' => 'Moldova (the Republic of)',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands (the)',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NZ' => 'New Zealand',
            'NI' => 'Nicaragua',
            'NE' => 'Niger (the)',
            'NG' => 'Nigeria',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'MP' => 'Northern Mariana Islands (the)',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestine, State of',
            'PA' => 'Panama',
            'PG' => 'Papua New Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines (the)',
            'PN' => 'Pitcairn',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'R&eacute;union',
            'RO' => 'Romania',
            'RU' => 'Russian Federation (the)',
            'RW' => 'Rwanda',
            'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
            'KN' => 'Saint Kitts and Nevis',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'AB' => 'Scotland',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'CS' => 'Serbia and Montenegro',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovakia',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'GS' => 'South Georgia and the South Sandwich Islands',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan (the)',
            'SR' => 'Suriname',
            'SJ' => 'Svalbard and Jan Mayen',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan (Province of China)',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania, United Republic of',
            'TH' => 'Thailand',
            'TL' => 'Timor-Leste',
            'TG' => 'Togo',
            'TK' => 'Tokelau',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands (the)',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'AE' => 'United Arab Emirates (the)',
            'GB' => 'United Kingdom of Great Britain and Northern Ireland (the)',
            'UM' => 'United States Minor Outlying Islands (the)',
            'US' => 'United States of America (the)',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VE' => 'Venezuela (Bolivarian Republic of)',
            'VN' => 'Viet Nam',
            'VG' => 'Virgin Islands (British)',
            'VI' => 'Virgin Islands (U.S.)',
            'WA' => 'Wales',
            'WF' => 'Wallis and Futuna',
            'EH' => 'Western Sahara',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            // Add more countries as needed
        );
        $html = '<select name="' . $attsname . '"' . $atts . '>';
        $html .= '<option value="" selected disabled>Select a Country</option>';

        foreach ($countries as $code => $name) {
            $imagePath = plugins_url('', __FILE__) . '/flagsicon/' . strtolower($code) . '.svg';
            $html .= '<option value="' . $name . '" data-image="' . $imagePath . '">';
            $html .= $name;
            $html .= '</option>';
            // echo $imagePath; // Add this line for debugging
        }

        $html .= '</select>';
        return $html;
    }

    /* Validation filter */

    public function cf7vb_countryname_validation_filter($result, $tag)
    {
        $type = $tag->type;
        $name = $tag->name;

        $value = isset($_POST[$name]) ? (string) wp_unslash($_POST[$name]) : '';

        if ($tag->is_required() && '' == $value) {
            $result->invalidate($tag, wpcf7_get_message('invalid_required'));
        }

        return $result;
    }
    // cf7vb_add_tag_countryname
    public function cf7vb_add_tag_countryname()
    {
        // write a code here
        $tag_generator = WPCF7_TagGenerator::get_instance();
        $tag_generator->add('countryname', __('country drop-down', 'cf7vb'), [$this, 'cf7vb_tag_generator_countryname']);
    }

    // cf7vb_tag_generator_countryname

    public function cf7vb_tag_generator_countryname($contact_form, $args = '')
    {
        $args = wp_parse_args($args, array());
        $type = 'countryname';
        $description = __("Generate a form-tag for a country dorp list with flags icon text input field.", 'cf7vb');
        $desc_link = '';
?>
        <div class="control-box">
            <fieldset>
                <legend><?php echo esc_html($description); ?></legend>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo esc_html(__('Field type', 'cf7vb')); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html(__('Field type', 'cf7vb')); ?></legend>
                                    <label><input type="checkbox" name="required" /> <?php echo esc_html(__('Required field', 'cf7vb')); ?></label>
                                </fieldset>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-name'); ?>"><?php echo esc_html(__('Name', 'cf7vb')); ?></label></th>
                            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr($args['content'] . '-name'); ?>" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-id'); ?>"><?php echo esc_html(__('Id attribute', 'cf7vb')); ?></label></th>
                            <td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr($args['content'] . '-id'); ?>" /></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr($args['content'] . '-class'); ?>"><?php echo esc_html(__('Class attribute', 'cf7vb')); ?></label></th>
                            <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr($args['content'] . '-class'); ?>" /></td>
                        </tr>
                    </tbody>
                </table>
            </fieldset>
        </div>
        <div class="insert-box">
            <input type="text" name="<?php echo esc_attr($type); ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr(__('Insert Tag', 'cf7vb')); ?>" />
            </div>

            <br class="clear" />

            <p class="description mail-tag"><label for="<?php echo esc_attr($args['content'] . '-mailtag'); ?>"><?php echo sprintf("To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", '<strong><span class="mail-tag"></span></strong>'); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr($args['content'] . '-mailtag'); ?>" /></label></p>
        </div>

<?php
    }
}
