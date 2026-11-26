<?php
/**
 * includes/seo.php — Centralized schema.org JSON-LD builders.
 *
 * All structured data is generated here (or via these helpers) so identity
 * updates happen in one place. Output is encoded with JSON_HEX_* flags so
 * apostrophes, quotes, ampersands and "</script>" sequences in content can
 * never break out of the script tag.
 */

if (!defined('PROJECT_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * The single canonical Person entity for the whole site.
 * Referenced by @id from ProfilePage, Service.provider, etc.
 */
function seo_person_schema(): array {
    return [
        '@context'    => 'https://schema.org',
        '@type'       => 'Person',
        '@id'         => PERSON_ID,
        'name'        => OWNER_NAME,
        'url'         => SITE_URL . '/',
        'jobTitle'    => PRIMARY_TITLE,
        'description' => SITE_DESCRIPTION,
        'image'       => PERSON_IMAGE,
        'email'       => EMAIL_ADDRESS,
        'telephone'   => PHONE_PRIMARY,
        'address'     => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Nyeri',
            'addressCountry'  => 'KE',
        ],
        'sameAs'      => PERSON_SAME_AS,
        'knowsAbout'  => ['HTML5', 'CSS3', 'JavaScript', 'PHP', 'MySQL', 'MongoDB',
                          'Web Development', 'Full-Stack Development'],
    ];
}

/**
 * Print a <script type="application/ld+json"> block for a schema array.
 * HEX flags neutralize HTML-significant characters inside JSON strings.
 */
function seo_jsonld(array $schema): void {
    $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
           | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT;
    echo '<script type="application/ld+json">'
        . json_encode($schema, $flags) . "</script>\n";
}
