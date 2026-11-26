<?php
/**
 * includes/i18n.php — Centralised bilingual (English / Kiswahili) dictionary
 * for the IT Support & Networking services page.
 *
 * Language preference is persisted in the session and is only ever set
 * from a validated allowlist value — arbitrary language codes are rejected.
 */

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__));
}

/** Supported languages: code => native label */
const IT_LANGUAGES = [
    'en' => 'English',
    'sw' => 'Kiswahili',
];

/**
 * Resolve the active language for this request.
 * Accepts a one-shot validated ?lang= switch, then falls back to the
 * session-stored preference, then English.
 */
function it_lang(): string {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_GET['lang'])) {
        $candidate = strtolower(trim((string) $_GET['lang']));
        if (array_key_exists($candidate, IT_LANGUAGES)) {
            $_SESSION['it_support_lang'] = $candidate;
        }
        // Invalid codes are silently ignored — never stored.
    }

    $lang = $_SESSION['it_support_lang'] ?? 'en';
    return array_key_exists($lang, IT_LANGUAGES) ? $lang : 'en';
}

/**
 * Translate a key for the active (or given) language.
 * Falls back to English, then to the key itself.
 */
/**
 * The full translation dictionary.
 * Exposed so tests (and tooling) can inspect entries directly instead of
 * relying on t()'s English fallback.
 */
function it_dictionary(): array {
    static $dict = null;
    if ($dict === null) {
        $dict = [
            'en' => [
                // ── Meta / SEO ──
                'meta_title'       => 'IT Support & Networking Services in Nyeri, Kenya | Harrison Muhoro',
                'meta_desc'        => 'Professional IT support and computer networking services in Nyeri and surrounds. LAN & Wi-Fi installation, Windows troubleshooting, malware removal, backups, and small business IT support. On-site or remote. Request a quotation.',
                'meta_keywords'    => 'IT support Nyeri, computer repair Nyeri, network installation Kenya, WiFi setup Nyeri, computer troubleshooting Kenya, business IT support Nyeri, laptop repair Nyeri, tech support Kenya',

                // ── Hero ──
                'hero_label'       => 'IT Support & Networking',
                'hero_title'       => 'IT support that comes to you.',
                'hero_sub'         => 'Fast, honest help with computers, networks, and business IT — on-site around Nyeri or remotely where there is an internet connection. Two years of hands-on experience, priced by quotation.',
                'badge_area'       => 'Nyeri & surrounding areas',
                'badge_methods'    => 'On-site & remote support',
                'badge_exp'        => '2 years experience',
                'badge_quote'      => 'Quotation-based pricing',
                'badge_lang'       => 'English & Kiswahili',
                'cta_quote'        => 'Request a quotation',
                'cta_whatsapp'     => 'Chat on WhatsApp',

                // ── Services section ──
                'services_label'   => 'What I cover',
                'services_title'   => 'Everyday IT, sorted properly.',
                'services_sub'     => 'Practical support for homes and small businesses — the kind of work that keeps your computers and network running without drama.',

                'svc_network_title'  => 'Network installation & configuration',
                'svc_network_desc'   => 'LAN and WAN setup, wired and wireless networking, Wi-Fi and Ethernet cabling, network sharing, and connectivity troubleshooting for homes and offices.',
                'svc_network_items'  => 'LAN / WAN setup · Wi-Fi & Ethernet cabling · Home & office networking · Connectivity troubleshooting',

                'svc_troubleshoot_title'  => 'Computer troubleshooting',
                'svc_troubleshoot_desc'   => 'Windows installation, hardware diagnosis, RAM and HDD/SSD issues, boot problems, drivers, and performance optimisation for desktop and laptop computers.',
                'svc_troubleshoot_items'  => 'Windows install & repair · RAM / HDD / SSD diagnostics · Boot & driver issues · Speed & performance tuning',

                'svc_software_title' => 'Software & IT support',
                'svc_software_desc'  => 'Software installation, operating system errors, malware removal, data backup and recovery, user account setup, and routine maintenance.',
                'svc_software_items' => 'OS support & fixes · Malware removal · Backup & recovery · User accounts & maintenance',

                'svc_business_title' => 'Business IT support',
                'svc_business_desc'  => 'Office computer setup, printer sharing, small office networking, and ongoing basic technical support for businesses that need reliable day-to-day IT.',
                'svc_business_items' => 'Office PC setup · Printer sharing · Small office networks · Ongoing basic support',

                // ── Delivery / coverage section ──
                'delivery_label'   => 'How it works',
                'delivery_title'   => 'Simple, honest support.',
                'delivery_sub'     => 'Tell me what is broken or what you need set up. I will quote first — you approve before any work begins.',
                'del1_title'       => 'Request a quotation',
                'del1_desc'        => 'Use the form or WhatsApp. Describe the problem, where you are, and whether you need on-site or remote help.',
                'del2_title'       => 'Get a clear quote',
                'del2_desc'        => 'I assess the job and send a straightforward quotation in KES. No hidden costs, no surprises.',
                'del3_title'       => 'We fix it',
                'del3_desc'        => 'On-site visits around Nyeri and surrounds, or remote support wherever an internet connection reaches.',
                'del_note'         => 'Support is for individuals and businesses. I handle everyday IT — if your issue needs specialist equipment or advanced infrastructure work, I will tell you honestly rather than guess.',
                'del_scope_title'  => 'Coverage',
                'del_scope_area'   => 'Nyeri town and surrounding areas (on-site). Remote support available where there is an internet connection.',
                'del_scope_price'  => 'Pricing',
                'del_scope_quote'  => 'Every job is quoted individually after assessment — request a quotation using the form below.',
                'del_scope_langs'  => 'Languages',
                'del_scope_langlist' => 'English and Kiswahili',

                // ── Form ──
                'form_title'       => 'Request your quotation',
                'form_sub'         => 'Fill this in and send your request.',
                'f_name'           => 'Full name',
                'f_phone'          => 'Phone / WhatsApp',
                'f_email'          => 'Email (optional)',
                'f_category'       => 'What do you need help with?',
                'f_method'         => 'Preferred support method',
                'f_onsite'         => 'On-site visit',
                'f_remote'         => 'Remote support',
                'f_location'       => 'Location / area (for on-site)',
                'f_location_hint'  => 'e.g. Nyeri town, Naro Moru, Othaya…',
                'f_description'    => 'Describe the problem or what you need set up',
                'f_date'           => 'Preferred appointment date (optional)',
                'f_lang'           => 'Preferred language',
                'f_submit'         => 'Send request',
                'f_sending'        => 'Sending…',
                'f_success'        => 'Request received — thank you.',
                'f_privacy'        => 'Your details are only used to respond to this request.',

                'cat_network'      => 'Network installation & configuration',
                'cat_troubleshoot' => 'Computer troubleshooting',
                'cat_software'     => 'Software & IT support',
                'cat_business'     => 'Business IT support',
                'cat_other'        => 'Something else / not sure',

                'err_name'         => 'Please enter your name.',
                'err_phone'        => 'Please enter a valid phone number.',
                'err_email'        => 'Please enter a valid email address, or leave it blank.',
                'err_category'     => 'Please choose a service category.',
                'err_method'       => 'Please choose a support method.',
                'err_location'     => 'Please tell me the location for your on-site visit.',
                'err_description'  => 'Please describe the problem (at least 20 characters).',
                'err_date'         => 'Please enter a valid appointment date that is today or later.',
                'err_generic'      => 'Something went wrong. Please try again or contact me directly.',
                'err_server'       => 'Server error. Please try again.',

                // ── WhatsApp ──
                'wa_label'         => 'Prefer to chat?',
                'wa_text'          => 'Hi Harrison, I found your IT support page and I need help with computers / networking. I would like a quotation. Please get back to me.',

                // ── Lang switch ──
                'lang_switch'      => 'Lugha / Language',

                // ── Shared chrome (nav / footer) ──
                'nav_work'         => 'Work',
                'nav_about'        => 'About',
                'nav_projects'     => 'Projects',
                'nav_it_support'   => 'IT Support',
                'nav_services'     => 'Services',
                'nav_contact'      => 'Contact',
                'nav_cv'           => 'CV',
                'footer_tagline'   => 'Software engineer building dependable systems in Nyeri, Kenya.',
                'footer_location'  => 'Nyeri, Kenya',
            ],

            'sw' => [
                'meta_title'       => 'Huduma za Msaada wa IT na Mtandao Nyeri, Kenya | Harrison Muhoro',
                'meta_desc'        => 'Huduma za kitaalamu za msaada wa IT na kompyuta Nyeri na maeneo ya karibu. Ufunguzi wa LAN na Wi-Fi, utatuzi wa matatizo ya Windows, kuondoa malware, nakala za data, na msaada wa IT kwa biashara. Kwenye eneo au mbali. Omba nukuu ya bei.',
                'meta_keywords'    => 'msaada wa IT Nyeri, ukarabati wa kompyuta Nyeri, ufunguzi wa mtandao Kenya, usanidi wa WiFi Nyeri, utatuzi wa matatizo ya kompyuta Kenya, msaada wa IT kwa biashara Nyeri, ukarabati wa laptop Nyeri, msaada wa kiufundi Kenya',

                'hero_label'       => 'Msaada wa IT na Mtandao',
                'hero_title'       => 'Msaada wa IT unaokuja kwako.',
                'hero_sub'         => 'Msaada wa haraka na wa kuaminika kwa kompyuta, mitandao na IT ya biashara — kwenye eneo la Nyeri na mabonde au mbali popote penye muunganisho wa intaneti. Uzoefu wa miaka miwili, bei kwa nukuu.',
                'badge_area'       => 'Nyeri na maeneo ya karibu',
                'badge_methods'    => 'Huduma ya eneo na mbali',
                'badge_exp'        => 'Uzoefu wa miaka miwili',
                'badge_quote'      => 'Bei kwa nukuu',
                'badge_lang'       => 'Kiingereza na Kiswahili',
                'cta_quote'        => 'Omba nukuu ya bei',
                'cta_whatsapp'     => 'Ongea WhatsApp',

                'services_label'   => 'Ninachofanya',
                'services_title'   => 'Matatizo ya kila siku ya IT, yamepanguliwa vyema.',
                'services_sub'     => 'Msaada wa vitendo kwa kaya na biashara ndogo — unaohakikisha kompyuta na mtandao wako vinaendelea kufanya kazi bila shida.',

                'svc_network_title'  => 'Ufunguzi na usanidi wa mtandao',
                'svc_network_desc'   => 'Usanidi wa LAN na WAN, mitandao ya waya na isiyo ya waya, waya za Wi-Fi na Ethernet, kushiriki mtandao, na utatuzi wa matatizo ya muunganisho kwa kaya na ofisi.',
                'svc_network_items'  => 'Usanidi wa LAN / WAN · Waya za Wi-Fi na Ethernet · Mitandao ya nyumbani na ofisini · Utatuzi wa muunganisho',

                'svc_troubleshoot_title'  => 'Utatuzi wa matatizo ya kompyuta',
                'svc_troubleshoot_desc'   => 'Usanidi wa Windows, utambuzi wa hitilafu za vifaa, matatizo ya RAM na HDD/SSD, matatizo ya kuanza (boot), madereva, na kuboresha utendaji wa kompyuta za mezani na laptop.',
                'svc_troubleshoot_items'  => 'Usanidi na ukarabati wa Windows · Utambuzi wa RAM / HDD / SSD · Matatizo ya boot na madereva · Kuongeza kasi na utendaji',

                'svc_software_title' => 'Msaada wa programu na IT',
                'svc_software_desc'  => 'Usanidi wa programu, makosa ya mfumo wa uendeshaji, kuondoa malware, nakala na urejesho wa data, usanidi wa akaunti za watumiaji, na matengenezo ya kawaida.',
                'svc_software_items' => 'Msaada wa OS na fixes · Kuondoa malware · Nakala na urejesho wa data · Akaunti za watumiaji na matengenezo',

                'svc_business_title' => 'Msaada wa IT kwa biashara',
                'svc_business_desc'  => 'Usanidi wa kompyuta za ofisini, kushiriki printa, mitandao midogo ya ofisi, na msaada wa kiufundi wa kila siku kwa biashara zinazohitaji IT imara.',
                'svc_business_items' => 'Usanidi wa kompyuta za ofisini · Kushiriki printa · Mitandao midogo ya ofisi · Msaada wa kila siku',

                'delivery_label'   => 'Jinsi inavyofanya kazi',
                'delivery_title'   => 'Msaada rahisi na wa kuaminika.',
                'delivery_sub'     => 'Niambie tatizo lako au unachohitaji kusanidiwa. Nitakupa nukuu kwanza — unakubali kabla ya kazi yoyote kuanza.',
                'del1_title'       => 'Omba nukuu ya bei',
                'del1_desc'        => 'Tumia fomu au WhatsApp. Eleza tatizo, ulipo, na kama unahitaji msaada wa eneo au wa mbali.',
                'del2_title'       => 'Pokea nukuu iliyo wazi',
                'del2_desc'        => 'Hutathmini kazi na nitakutumia nukuu ya bei kwa KES. Hakuna gharama zilizofichwa.',
                'del3_title'       => 'Tunakurekebisha',
                'del3_desc'        => 'Ziara za eneo la Nyeri na mabonde yake, au msaada wa mbali popote penye muunganisho wa intaneti.',
                'del_note'         => 'Msaada ni kwa watu binafsi na biashara. Naushughulikia IT ya kila siku — kama tatizo lako linahitaji vifaa maalum au kazi ya miundombinu ya hali ya juu, nitakuambia kwa uaminifu badala ya kubahatisha.',
                'del_scope_title'  => 'Eneo na masharti',
                'del_scope_area'   => 'Mji wa Nyeri na maeneo ya karibu (kwenye eneo). Msaada wa mbali upatikana popote penye muunganisho wa intaneti.',
                'del_scope_price'  => 'Bei',
                'del_scope_quote'  => 'Kila kazi hupimwa na kupewa nukuu yake — omba nukuu kwa kutumia fomu hapa chini.',
                'del_scope_langs'  => 'Lugha',
                'del_scope_langlist' => 'Kiingereza na Kiswahili',

                'form_title'       => 'Omba nukuu ya bei',
                'form_sub'         => 'Jaza fomu hii kutuma ombi lako.',
                'f_name'           => 'Jina kamili',
                'f_phone'          => 'Simu / WhatsApp',
                'f_email'          => 'Barua pepe (hiari)',
                'f_category'       => 'Unahitaji msaada wa aina gani?',
                'f_method'         => 'Njia unayopendelea ya msaada',
                'f_onsite'         => 'Ziara ya eneo',
                'f_remote'         => 'Msaada wa mbali',
                'f_location'       => 'Mahali / eneo (kwa ziara)',
                'f_location_hint'  => 'mfano: Mji wa Nyeri, Naro Moru, Othaya…',
                'f_description'    => 'Eleza tatizo au unachohitaji kusanidiwa',
                'f_date'           => 'Tarehe ya miadi unayopendelea (hiari)',
                'f_lang'           => 'Lugha unayopendelea',
                'f_submit'         => 'Tuma ombi',
                'f_sending'        => 'Inatuma…',
                'f_success'        => 'Ombi limepokelewa — asante.',
                'f_privacy'        => 'Taarifa zako hutumika tu kujibu ombi hili.',

                'cat_network'      => 'Ufunguzi na usanidi wa mtandao',
                'cat_troubleshoot' => 'Utatuzi wa matatizo ya kompyuta',
                'cat_software'     => 'Msaada wa programu na IT',
                'cat_business'     => 'Msaada wa IT kwa biashara',
                'cat_other'        => 'Jambo lingine / sijui',

                'err_name'         => 'Tafadhali weka jina lako.',
                'err_phone'        => 'Tafadhali weka namba sahihi ya simu.',
                'err_email'        => 'Tafadhali weka barua pepe sahihi, au uache wazi.',
                'err_category'     => 'Tafadhali chagua aina ya huduma.',
                'err_method'       => 'Tafadhali chagua njia ya msaada.',
                'err_location'     => 'Tafadhali niambie eneo la ziara yako.',
                'err_description'  => 'Tafadhali eleza tatizo (angalau herufi 20).',
                'err_date'         => 'Tafadhali weka tarehe sahihi ya miadi — leo au baadaye.',
                'err_generic'      => 'Hitilafu imetokea. Tafadhali jaribu tena au niwasiliane nami moja kwa moja.',
                'err_server'       => 'Hitilafu ya seva. Tafadhali jaribu tena.',

                'wa_label'         => 'Unapendelea kuongea?',
                'wa_text'          => 'Habari Harrison, nimekutafuta ukurasa wako wa msaada wa IT na ninahitaji msaada wa kompyuta / mitandao. Ningependa nukuu ya bei. Tafadhali niambie. Asante.',

                'lang_switch'      => 'Lugha / Language',

                // ── Shared chrome (nav / footer) ──
                'nav_work'         => 'Kazi',
                'nav_about'        => 'Kuhusu',
                'nav_projects'     => 'Miradi',
                'nav_it_support'   => 'Msaada wa IT',
                'nav_services'     => 'Huduma za Programu',
                'nav_contact'      => 'Mawasiliano',
                'nav_cv'           => 'CV',
                'footer_tagline'   => 'Mhandisi wa programu anayejenga mifumo thabiti Nyeri, Kenya.',
                'footer_location'  => 'Nyeri, Kenya',
            ],
        ];
    }
    return $dict;
}

/**
 * Translate a key for the active (or given) language.
 * Falls back to English, then to the key itself.
 */
function t(string $key, ?string $lang = null): string {
    $lang = $lang ?? it_lang();
    $dict = it_dictionary();
    return $dict[$lang][$key] ?? $dict['en'][$key] ?? $key;
}

/** Current language code ('en' | 'sw') — short alias used across pages. */
$lang = it_lang();
