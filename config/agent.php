<?php

/*
|--------------------------------------------------------------------------
| Agent / Site Details
|--------------------------------------------------------------------------
|
| Everything specific to the business lives here. Change these values and the
| whole site updates - header, footer, contact page, click-to-call buttons,
| WhatsApp links, SEO tags and the JSON-LD structured data.
|
| NOTE: the three figures under "Headline numbers" and the `bio` and `license`
| entries are placeholders. Put your real numbers and words in before this site
| goes anywhere public - they are claims made in your name.
|
*/

return [
    'name'      => 'Zohaib Hassan',
    'title'     => 'Property Dealer',
    'agency'    => 'ZH Estates',
    'tagline'   => 'Straight dealing on Lahore property - DHA, Bahria Town and Gulberg.',
    'photo'     => 'images/agent.jpg',
    'avatar'    => 'images/agent-avatar.jpg',
    'logo'      => 'images/logo.svg',
    'logo_mark' => 'images/logo-mark.svg',
    'logo_light' => 'images/logo-light.svg',

    // Leave blank until you have a real registration to show.
    'license'   => '',

    /* Headline numbers - PLACEHOLDERS, replace with your own */
    'experience_years'   => 6,
    'properties_sold'    => 120,
    'avg_days_on_market' => 34,

    /* PLACEHOLDER copy - rewrite in your own words */
    'bio' => [
        "I deal in residential property across Lahore - mostly DHA, Bahria Town and Gulberg, with regular work in Model Town, Johar Town and Askari. Plots, houses and the occasional commercial unit.",
        "Most of the trouble in a Lahore property transaction is paperwork, not price. Transfer letters, NOCs, society dues, possession status, whether the file is clean - I check all of it before you commit, and I tell you plainly when something does not add up.",
        "I work on a short list of properties at a time rather than a long one, which means when you call about a listing you get somebody who has actually walked it. If a deal is wrong for you, I will say so - there will be another one.",
    ],

    'phone'         => '+92 322 728 9296',
    'phone_dial'    => '+923227289296',
    'office_phone'  => '+92 322 728 9296',
    'email'         => 'zohaibhassan1107@gmail.com',
    'whatsapp'      => '923227289296',

    'office' => [
        'street'   => 'Y Block Commercial, DHA Phase 3',
        'suburb'   => 'Lahore',
        'state'    => 'Punjab',
        'postcode' => '54792',
        'country'  => 'PK',
        'lat'      => 31.4752,
        'lng'      => 74.3998,
    ],

    'hours' => [
        'Monday - Thursday' => '10:00am - 8:00pm',
        'Friday'            => '10:00am - 12:30pm, 2:30pm - 8:00pm',
        'Saturday'          => '10:00am - 8:00pm',
        'Sunday'            => 'By appointment',
    ],

    'service_areas' => [
        'DHA Lahore', 'Bahria Town', 'Gulberg', 'Model Town', 'Johar Town',
        'Askari', 'Cantt', 'Wapda Town', 'Valencia Town', 'Iqbal Town',
    ],

    'social' => [
        'facebook'  => 'https://facebook.com/',
        'instagram' => 'https://instagram.com/',
        'linkedin'  => 'https://linkedin.com/in/',
        'youtube'   => 'https://youtube.com/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Local formatting
    |--------------------------------------------------------------------------
    |
    | `price.style` - 'subcontinent' renders 42500000 as "PKR 4.25 Crore".
    |                 'western' renders it as "PKR 42,500,000".
    | `area.unit`   - 'marla' stores land size in Marla and rolls 20 Marla up
    |                 into Kanal. 'sqm' stores plain square metres.
    |
    */
    'format' => [
        'price' => ['currency' => 'PKR', 'style' => 'subcontinent'],
        'area'  => ['unit' => 'marla'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Demo mode
    |--------------------------------------------------------------------------
    |
    | This site is published as a portfolio piece, so the admin login is
    | pre-filled and the credentials are shown on screen. Set DEMO_LOGIN=false
    | (or drop the line) the moment it holds anything real.
    |
    */
    'demo' => [
        'enabled'  => env('DEMO_LOGIN', false),
        'email'    => env('DEMO_LOGIN_EMAIL', 'zohaibhassan1107@gmail.com'),
        'password' => env('DEMO_LOGIN_PASSWORD', 'password'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Local vocabulary
    |--------------------------------------------------------------------------
    |
    | The database stores the generic keys; these are what visitors read. Change
    | the right-hand side for another market without touching a migration.
    |
    */
    'property_types' => [
        'house'     => 'House',
        'apartment' => 'Flat / Apartment',
        'townhouse' => 'Upper / Lower Portion',
        'land'      => 'Plot',
        'acreage'   => 'Farmhouse',
    ],

    /* Price bands offered in the listing filter, in rupees. */
    'price_bands' => [
        ''         => 'Any',
        '10000000' => '1 Crore',
        '20000000' => '2 Crore',
        '30000000' => '3 Crore',
        '50000000' => '5 Crore',
        '80000000' => '8 Crore',
        '120000000' => '12 Crore',
    ],

    'seo' => [
        'site_name'   => 'Zohaib Hassan | ZH Estates',
        'description' => 'Zohaib Hassan is a property dealer in Lahore handling houses and plots across DHA, Bahria Town, Gulberg and Model Town. Browse listings, recent sales and request a free valuation.',
        'keywords'    => 'property dealer Lahore, DHA Lahore property, Bahria Town Lahore house for sale, Gulberg property, plot for sale Lahore, free property valuation Lahore',
        'locale'      => 'en_PK',
        'twitter'     => '@zhestates',

        /* Paste the content value from Search Console / Bing Webmaster Tools. */
        'google_verification' => env('GOOGLE_SITE_VERIFICATION', ''),
        'bing_verification'   => env('BING_SITE_VERIFICATION', ''),

        /* GA4 measurement id, e.g. G-XXXXXXX. Blank = no analytics loaded. */
        'analytics_id' => env('GA_MEASUREMENT_ID', ''),
    ],
];
