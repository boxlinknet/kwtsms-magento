<?php
/**
 * Phone Validation Rules.
 *
 * Contains phone number format rules for 87 countries, organized by region.
 * Each rule defines valid local lengths and mobile start digits for a given country code.
 * Also provides a mapping of country codes to country names.
 */
declare(strict_types=1);

namespace KwtSms\SmsIntegration\Model\Phone;

class Rules
{
    /**
     * Phone validation rules indexed by country calling code.
     *
     * Each entry contains:
     *   - localLengths: array of valid lengths for the local (national) part of the number
     *   - mobileStartDigits: array of valid first digits for mobile numbers, or null if unrestricted
     *
     * @var array<string, array{localLengths: int[], mobileStartDigits: string[]|null}>
     */
    public const PHONE_RULES = [
        // GCC
        '965' => ['localLengths' => [8], 'mobileStartDigits' => ['4', '5', '6', '9']],
        '966' => ['localLengths' => [9], 'mobileStartDigits' => ['5']],
        '971' => ['localLengths' => [9], 'mobileStartDigits' => ['5']],
        '973' => ['localLengths' => [8], 'mobileStartDigits' => ['3', '6']],
        '974' => ['localLengths' => [8], 'mobileStartDigits' => ['3', '5', '6', '7']],
        '968' => ['localLengths' => [8], 'mobileStartDigits' => ['7', '9']],

        // Levant
        '962' => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '961' => ['localLengths' => [7, 8], 'mobileStartDigits' => ['3', '7', '8']],
        '970' => ['localLengths' => [9], 'mobileStartDigits' => ['5']],
        '964' => ['localLengths' => [10], 'mobileStartDigits' => ['7']],
        '963' => ['localLengths' => [9], 'mobileStartDigits' => ['9']],

        // Other Arab
        '967' => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '20'  => ['localLengths' => [10], 'mobileStartDigits' => ['1']],
        '218' => ['localLengths' => [9], 'mobileStartDigits' => ['9']],
        '216' => ['localLengths' => [8], 'mobileStartDigits' => ['2', '4', '5', '9']],
        '212' => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7']],
        '213' => ['localLengths' => [9], 'mobileStartDigits' => ['5', '6', '7']],
        '249' => ['localLengths' => [9], 'mobileStartDigits' => ['9']],

        // Non-Arab Middle East
        '98'  => ['localLengths' => [10], 'mobileStartDigits' => ['9']],
        '90'  => ['localLengths' => [10], 'mobileStartDigits' => ['5']],
        '972' => ['localLengths' => [9], 'mobileStartDigits' => ['5']],

        // South Asia
        '91'  => ['localLengths' => [10], 'mobileStartDigits' => ['6', '7', '8', '9']],
        '92'  => ['localLengths' => [10], 'mobileStartDigits' => ['3']],
        '880' => ['localLengths' => [10], 'mobileStartDigits' => ['1']],
        '94'  => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '960' => ['localLengths' => [7], 'mobileStartDigits' => ['7', '9']],

        // East Asia
        '86'  => ['localLengths' => [11], 'mobileStartDigits' => ['1']],
        '81'  => ['localLengths' => [10], 'mobileStartDigits' => ['7', '8', '9']],
        '82'  => ['localLengths' => [10], 'mobileStartDigits' => ['1']],
        '886' => ['localLengths' => [9], 'mobileStartDigits' => ['9']],

        // Southeast Asia
        '65'  => ['localLengths' => [8], 'mobileStartDigits' => ['8', '9']],
        '60'  => ['localLengths' => [9, 10], 'mobileStartDigits' => ['1']],
        '62'  => ['localLengths' => [9, 10, 11, 12], 'mobileStartDigits' => ['8']],
        '63'  => ['localLengths' => [10], 'mobileStartDigits' => ['9']],
        '66'  => ['localLengths' => [9], 'mobileStartDigits' => ['6', '8', '9']],
        '84'  => ['localLengths' => [9], 'mobileStartDigits' => ['3', '5', '7', '8', '9']],
        '95'  => ['localLengths' => [9], 'mobileStartDigits' => ['9']],
        '855' => ['localLengths' => [8, 9], 'mobileStartDigits' => ['1', '6', '7', '8', '9']],
        '976' => ['localLengths' => [8], 'mobileStartDigits' => ['6', '8', '9']],

        // Europe
        '44'  => ['localLengths' => [10], 'mobileStartDigits' => ['7']],
        '33'  => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7']],
        '49'  => ['localLengths' => [10, 11], 'mobileStartDigits' => ['1']],
        '39'  => ['localLengths' => [10], 'mobileStartDigits' => ['3']],
        '34'  => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7']],
        '31'  => ['localLengths' => [9], 'mobileStartDigits' => ['6']],
        '32'  => ['localLengths' => [9], 'mobileStartDigits' => null],
        '41'  => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '43'  => ['localLengths' => [10], 'mobileStartDigits' => ['6']],
        '47'  => ['localLengths' => [8], 'mobileStartDigits' => ['4', '9']],
        '48'  => ['localLengths' => [9], 'mobileStartDigits' => null],
        '30'  => ['localLengths' => [10], 'mobileStartDigits' => ['6']],
        '420' => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7']],
        '46'  => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '45'  => ['localLengths' => [8], 'mobileStartDigits' => null],
        '40'  => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '36'  => ['localLengths' => [9], 'mobileStartDigits' => null],
        '380' => ['localLengths' => [9], 'mobileStartDigits' => null],

        // Americas
        '1'   => ['localLengths' => [10], 'mobileStartDigits' => null],
        '52'  => ['localLengths' => [10], 'mobileStartDigits' => null],
        '55'  => ['localLengths' => [11], 'mobileStartDigits' => null],
        '57'  => ['localLengths' => [10], 'mobileStartDigits' => ['3']],
        '54'  => ['localLengths' => [10], 'mobileStartDigits' => ['9']],
        '56'  => ['localLengths' => [9], 'mobileStartDigits' => ['9']],
        '58'  => ['localLengths' => [10], 'mobileStartDigits' => ['4']],
        '51'  => ['localLengths' => [9], 'mobileStartDigits' => ['9']],
        '593' => ['localLengths' => [9], 'mobileStartDigits' => ['9']],
        '53'  => ['localLengths' => [8], 'mobileStartDigits' => ['5', '6']],

        // Africa
        '27'  => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7', '8']],
        '234' => ['localLengths' => [10], 'mobileStartDigits' => ['7', '8', '9']],
        '254' => ['localLengths' => [9], 'mobileStartDigits' => ['1', '7']],
        '233' => ['localLengths' => [9], 'mobileStartDigits' => ['2', '5']],
        '251' => ['localLengths' => [9], 'mobileStartDigits' => ['7', '9']],
        '255' => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7']],
        '256' => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '237' => ['localLengths' => [9], 'mobileStartDigits' => ['6']],
        '225' => ['localLengths' => [10], 'mobileStartDigits' => null],
        '221' => ['localLengths' => [9], 'mobileStartDigits' => ['7']],
        '252' => ['localLengths' => [9], 'mobileStartDigits' => ['6', '7']],
        '250' => ['localLengths' => [9], 'mobileStartDigits' => ['7']],

        // Oceania
        '61'  => ['localLengths' => [9], 'mobileStartDigits' => ['4']],
        '64'  => ['localLengths' => [8, 9, 10], 'mobileStartDigits' => ['2']],
    ];

    /**
     * Country names indexed by country calling code.
     *
     * @var array<string, string>
     */
    public const COUNTRY_NAMES = [
        // GCC
        '965' => 'Kuwait',
        '966' => 'Saudi Arabia',
        '971' => 'UAE',
        '973' => 'Bahrain',
        '974' => 'Qatar',
        '968' => 'Oman',

        // Levant
        '962' => 'Jordan',
        '961' => 'Lebanon',
        '970' => 'Palestine',
        '964' => 'Iraq',
        '963' => 'Syria',

        // Other Arab
        '967' => 'Yemen',
        '20'  => 'Egypt',
        '218' => 'Libya',
        '216' => 'Tunisia',
        '212' => 'Morocco',
        '213' => 'Algeria',
        '249' => 'Sudan',
        '211' => 'South Sudan',

        // Non-Arab Middle East
        '98'  => 'Iran',
        '90'  => 'Turkey',
        '972' => 'Israel',
        '93'  => 'Afghanistan',

        // South Asia
        '91'  => 'India',
        '92'  => 'Pakistan',
        '880' => 'Bangladesh',
        '94'  => 'Sri Lanka',
        '960' => 'Maldives',
        '977' => 'Nepal',
        '975' => 'Bhutan',

        // East Asia
        '86'  => 'China',
        '81'  => 'Japan',
        '82'  => 'South Korea',
        '886' => 'Taiwan',
        '852' => 'Hong Kong',
        '853' => 'Macau',
        '850' => 'North Korea',

        // Southeast Asia
        '65'  => 'Singapore',
        '60'  => 'Malaysia',
        '62'  => 'Indonesia',
        '63'  => 'Philippines',
        '66'  => 'Thailand',
        '84'  => 'Vietnam',
        '95'  => 'Myanmar',
        '855' => 'Cambodia',
        '976' => 'Mongolia',
        '856' => 'Laos',
        '670' => 'Timor-Leste',
        '673' => 'Brunei',

        // Europe
        '44'  => 'United Kingdom',
        '33'  => 'France',
        '49'  => 'Germany',
        '39'  => 'Italy',
        '34'  => 'Spain',
        '31'  => 'Netherlands',
        '32'  => 'Belgium',
        '41'  => 'Switzerland',
        '43'  => 'Austria',
        '47'  => 'Norway',
        '48'  => 'Poland',
        '30'  => 'Greece',
        '420' => 'Czech Republic',
        '46'  => 'Sweden',
        '45'  => 'Denmark',
        '40'  => 'Romania',
        '36'  => 'Hungary',
        '380' => 'Ukraine',
        '351' => 'Portugal',
        '353' => 'Ireland',
        '358' => 'Finland',
        '359' => 'Bulgaria',
        '385' => 'Croatia',
        '381' => 'Serbia',
        '386' => 'Slovenia',
        '421' => 'Slovakia',
        '370' => 'Lithuania',
        '371' => 'Latvia',
        '372' => 'Estonia',
        '373' => 'Moldova',
        '375' => 'Belarus',
        '382' => 'Montenegro',
        '383' => 'Kosovo',
        '355' => 'Albania',
        '389' => 'North Macedonia',
        '387' => 'Bosnia and Herzegovina',
        '354' => 'Iceland',
        '356' => 'Malta',
        '357' => 'Cyprus',
        '352' => 'Luxembourg',
        '7'   => 'Russia',
        '374' => 'Armenia',
        '995' => 'Georgia',
        '994' => 'Azerbaijan',

        // Americas
        '1'   => 'USA/Canada',
        '52'  => 'Mexico',
        '55'  => 'Brazil',
        '57'  => 'Colombia',
        '54'  => 'Argentina',
        '56'  => 'Chile',
        '58'  => 'Venezuela',
        '51'  => 'Peru',
        '593' => 'Ecuador',
        '53'  => 'Cuba',
        '591' => 'Bolivia',
        '595' => 'Paraguay',
        '598' => 'Uruguay',
        '507' => 'Panama',
        '506' => 'Costa Rica',
        '502' => 'Guatemala',
        '503' => 'El Salvador',
        '504' => 'Honduras',
        '505' => 'Nicaragua',
        '509' => 'Haiti',
        '809' => 'Dominican Republic',
        '592' => 'Guyana',
        '597' => 'Suriname',

        // Africa
        '27'  => 'South Africa',
        '234' => 'Nigeria',
        '254' => 'Kenya',
        '233' => 'Ghana',
        '251' => 'Ethiopia',
        '255' => 'Tanzania',
        '256' => 'Uganda',
        '237' => 'Cameroon',
        '225' => 'Ivory Coast',
        '221' => 'Senegal',
        '252' => 'Somalia',
        '250' => 'Rwanda',
        '253' => 'Djibouti',
        '257' => 'Burundi',
        '258' => 'Mozambique',
        '260' => 'Zambia',
        '261' => 'Madagascar',
        '263' => 'Zimbabwe',
        '264' => 'Namibia',
        '265' => 'Malawi',
        '266' => 'Lesotho',
        '267' => 'Botswana',
        '268' => 'Eswatini',
        '269' => 'Comoros',
        '230' => 'Mauritius',
        '231' => 'Liberia',
        '232' => 'Sierra Leone',
        '235' => 'Chad',
        '236' => 'Central African Republic',
        '238' => 'Cape Verde',
        '239' => 'Sao Tome and Principe',
        '240' => 'Equatorial Guinea',
        '241' => 'Gabon',
        '242' => 'Republic of the Congo',
        '243' => 'DR Congo',
        '244' => 'Angola',
        '245' => 'Guinea-Bissau',
        '246' => 'Diego Garcia',
        '247' => 'Ascension Island',
        '248' => 'Seychelles',
        '220' => 'Gambia',
        '222' => 'Mauritania',
        '223' => 'Mali',
        '224' => 'Guinea',
        '226' => 'Burkina Faso',
        '227' => 'Niger',
        '228' => 'Togo',
        '229' => 'Benin',

        // Oceania
        '61'  => 'Australia',
        '64'  => 'New Zealand',
        '679' => 'Fiji',
        '675' => 'Papua New Guinea',
        '676' => 'Tonga',
        '677' => 'Solomon Islands',
        '678' => 'Vanuatu',
        '685' => 'Samoa',
    ];
}
