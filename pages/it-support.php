<?php
require_once __DIR__ . '/../includes/i18n.php';

// Opt this page (and its nav/footer chrome) into bilingual rendering.
define('IT_I18N_ACTIVE', true);

$page_title       = t('meta_title');
$page_description = t('meta_desc');
$seo_keywords    = t('meta_keywords');
$page_css         = ['contact', 'it-support'];

require_once __DIR__ . '/../includes/header.php';

if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION[CSRF_TOKEN_NAME];

// Contextual WhatsApp deep links for both languages
$wa_base = 'https://wa.me/' . WHATSAPP_NUMBER . '?text=';
$wa_links = [
    'en' => $wa_base . rawurlencode(t('wa_text', 'en')),
    'sw' => $wa_base . rawurlencode(t('wa_text', 'sw')),
];

$services = [
    ['key' => 'network'],
    ['key' => 'troubleshoot'],
    ['key' => 'software'],
    ['key' => 'business'],
];

$steps = [
    ['num' => '01', 'title' => 'del1_title', 'desc' => 'del1_desc'],
    ['num' => '02', 'title' => 'del2_title', 'desc' => 'del2_desc'],
    ['num' => '03', 'title' => 'del3_title', 'desc' => 'del3_desc'],
];
?>

<main id="main-content">

    <!-- ── Hero ── -->
    <section class="its-hero section" aria-labelledby="its-hero-title">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label"><?= htmlspecialchars(t('hero_label')) ?></span>
                <h1 id="its-hero-title"><?= htmlspecialchars(t('hero_title')) ?></h1>
                <p class="section-heading__sub"><?= htmlspecialchars(t('hero_sub')) ?></p>
            </header>

            <p class="its-lang-switch reveal">
                <span class="its-lang-switch__label"><?= htmlspecialchars(t('lang_switch')) ?>:</span>
                <a href="?lang=en" hreflang="en" class="its-lang-switch__link<?= $lang === 'en' ? ' is-active' : '' ?>"<?= $lang === 'en' ? ' aria-current="true"' : '' ?>>English</a>
                <span aria-hidden="true">·</span>
                <a href="?lang=sw" hreflang="sw" class="its-lang-switch__link<?= $lang === 'sw' ? ' is-active' : '' ?>"<?= $lang === 'sw' ? ' aria-current="true"' : '' ?>>Kiswahili</a>
            </p>

            <ul class="its-badges reveal" role="list">
                <li class="its-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <?= htmlspecialchars(t('badge_area')) ?>
                </li>
                <li class="its-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    <?= htmlspecialchars(t('badge_methods')) ?>
                </li>
                <li class="its-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?= htmlspecialchars(t('badge_exp')) ?>
                </li>
                <li class="its-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <?= htmlspecialchars(t('badge_quote')) ?>
                </li>
                <li class="its-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 5h16M4 12h16M4 19h10"/></svg>
                    <?= htmlspecialchars(t('badge_lang')) ?>
                </li>
            </ul>
        </div>
    </section>

    <!-- ── Services ── -->
    <section class="its-services section" id="services" aria-labelledby="its-services-title">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label"><?= htmlspecialchars(t('services_label')) ?></span>
                <h2 id="its-services-title"><?= htmlspecialchars(t('services_title')) ?></h2>
                <p class="section-heading__sub"><?= htmlspecialchars(t('services_sub')) ?></p>
            </header>

            <ol class="its-service-list">
                <?php foreach ($services as $i => $svc): ?>
                <li class="its-service reveal">
                    <span class="its-service__num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="its-service__icon" aria-hidden="true">
                        <?php if ($svc['key'] === 'network'): ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="2"/><path d="M16.24 7.76a6 6 0 0 1 0 8.49m-8.48-.01a6 6 0 0 1 0-8.49m11.31-2.82a10 10 0 0 1 0 14.14m-14.14 0a10 10 0 0 1 0-14.14"/></svg>
                        <?php elseif ($svc['key'] === 'troubleshoot'): ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        <?php elseif ($svc['key'] === 'software'): ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        <?php else: ?>
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="its-service__title"><?= htmlspecialchars(t('svc_' . $svc['key'] . '_title')) ?></h3>
                        <p class="its-service__desc"><?= htmlspecialchars(t('svc_' . $svc['key'] . '_desc')) ?></p>
                        <p class="its-service__items"><?= htmlspecialchars(t('svc_' . $svc['key'] . '_items')) ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- ── How it works ── -->
    <section class="its-how section" id="how-it-works" aria-labelledby="its-how-title">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label"><?= htmlspecialchars(t('delivery_label')) ?></span>
                <h2 id="its-how-title"><?= htmlspecialchars(t('delivery_title')) ?></h2>
                <p class="section-heading__sub"><?= htmlspecialchars(t('delivery_sub')) ?></p>
            </header>

            <ol class="its-steps">
                <?php foreach ($steps as $step): ?>
                <li class="its-step reveal">
                    <span class="its-step__num"><?= $step['num'] ?></span>
                    <h3 class="its-step__title"><?= htmlspecialchars(t($step['title'])) ?></h3>
                    <p class="its-step__desc"><?= htmlspecialchars(t($step['desc'])) ?></p>
                </li>
                <?php endforeach; ?>
            </ol>

            <div class="its-note reveal">
                <p><?= htmlspecialchars(t('del_note')) ?></p>
            </div>

            <dl class="its-facts reveal">
                <div class="its-fact">
                    <dt><?= htmlspecialchars(t('del_scope_title')) ?></dt>
                    <dd><?= htmlspecialchars(t('del_scope_area')) ?></dd>
                </div>
                <div class="its-fact">
                    <dt><?= htmlspecialchars(t('del_scope_price')) ?></dt>
                    <dd><?= htmlspecialchars(t('del_scope_quote')) ?></dd>
                </div>
                <div class="its-fact">
                    <dt><?= htmlspecialchars(t('del_scope_langs')) ?></dt>
                    <dd><?= htmlspecialchars(t('del_scope_langlist')) ?></dd>
                </div>
            </dl>
        </div>
    </section>

    <!-- ── Enquiry form + WhatsApp ── -->
    <section class="its-enquiry section" id="enquiry" aria-labelledby="its-form-title">
        <div class="container">
            <div class="contact-page__grid">

                <div class="contact-page__info reveal-stagger">
                    <span class="section-heading__label"><?= htmlspecialchars(t('form_title')) ?></span>
                    <h2 class="contact-page__title reveal" id="its-form-title"><?= htmlspecialchars(t('form_title')) ?></h2>
                    <p class="contact-page__desc reveal"><?= htmlspecialchars(t('form_sub')) ?></p>

                    <div class="its-wa reveal">
                        <span class="its-wa__label"><?= htmlspecialchars(t('wa_label')) ?></span>
                        <a href="<?= htmlspecialchars($wa_links[$lang]) ?>" target="_blank" rel="noopener noreferrer"
                           class="contact-channel its-wa__link"
                           id="its-wa-link"
                           data-wa-en="<?= htmlspecialchars($wa_links['en']) ?>"
                           data-wa-sw="<?= htmlspecialchars($wa_links['sw']) ?>">
                            <div class="contact-channel__icon" aria-hidden="true">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                            </div>
                            <div class="contact-channel__body">
                                <div class="contact-channel__label"><?= htmlspecialchars(t('wa_label')) ?></div>
                                <div class="contact-channel__value"><?= htmlspecialchars(PHONE_PRIMARY) ?></div>
                            </div>
                            <svg class="contact-channel__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>
                </div>

                <div class="contact-page__form reveal">
                    <h3 class="contact-page__form-title"><?= htmlspecialchars(t('form_title')) ?></h3>

                    <form
                        id="it-support-form"
                        action="<?= BASE_PATH ?>/backend/it_support_handler.php"
                        method="POST"
                        novalidate
                        aria-label="<?= htmlspecialchars(t('form_title')) ?>"
                    >
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="text" name="website" class="u-visually-hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

                        <div class="form-row">
                            <div class="form-group" id="group-name">
                                <label class="form-label" for="its-name"><?= htmlspecialchars(t('f_name')) ?> <span aria-hidden="true">*</span></label>
                                <input type="text" id="its-name" name="name" class="form-input" required minlength="2" maxlength="100" autocomplete="name" aria-required="true" aria-describedby="its-error-name">
                                <span class="form-error" role="alert" id="its-error-name" aria-live="polite"></span>
                            </div>
                            <div class="form-group" id="group-phone">
                                <label class="form-label" for="its-phone"><?= htmlspecialchars(t('f_phone')) ?> <span aria-hidden="true">*</span></label>
                                <input type="tel" id="its-phone" name="phone" class="form-input" required maxlength="30" autocomplete="tel" inputmode="tel" aria-required="true" aria-describedby="its-error-phone">
                                <span class="form-error" role="alert" id="its-error-phone" aria-live="polite"></span>
                            </div>
                        </div>

                        <div class="form-group" id="group-email">
                            <label class="form-label" for="its-email"><?= htmlspecialchars(t('f_email')) ?></label>
                            <input type="email" id="its-email" name="email" class="form-input" maxlength="254" autocomplete="email" aria-describedby="its-error-email">
                            <span class="form-error" role="alert" id="its-error-email" aria-live="polite"></span>
                        </div>

                        <div class="form-row">
                            <div class="form-group" id="group-category">
                                <label class="form-label" for="its-category"><?= htmlspecialchars(t('f_category')) ?> <span aria-hidden="true">*</span></label>
                                <select id="its-category" name="category" class="form-input" required aria-required="true" aria-describedby="its-error-category">
                                    <option value="" selected disabled><?= htmlspecialchars(t('f_category')) ?></option>
                                    <option value="network"><?= htmlspecialchars(t('cat_network')) ?></option>
                                    <option value="troubleshooting"><?= htmlspecialchars(t('cat_troubleshoot')) ?></option>
                                    <option value="software"><?= htmlspecialchars(t('cat_software')) ?></option>
                                    <option value="business"><?= htmlspecialchars(t('cat_business')) ?></option>
                                    <option value="other"><?= htmlspecialchars(t('cat_other')) ?></option>
                                </select>
                                <span class="form-error" role="alert" id="its-error-category" aria-live="polite"></span>
                            </div>
                            <div class="form-group" id="group-method">
                                <label class="form-label" for="its-method"><?= htmlspecialchars(t('f_method')) ?> <span aria-hidden="true">*</span></label>
                                <select id="its-method" name="method" class="form-input" required aria-required="true" aria-describedby="its-error-method">
                                    <option value="" selected disabled><?= htmlspecialchars(t('f_method')) ?></option>
                                    <option value="onsite"><?= htmlspecialchars(t('f_onsite')) ?></option>
                                    <option value="remote"><?= htmlspecialchars(t('f_remote')) ?></option>
                                </select>
                                <span class="form-error" role="alert" id="its-error-method" aria-live="polite"></span>
                            </div>
                        </div>

                        <div class="form-group" id="group-location">
                            <label class="form-label" for="its-location"><?= htmlspecialchars(t('f_location')) ?> <span aria-hidden="true" id="its-location-required" hidden>*</span></label>
                            <input type="text" id="its-location" name="location" class="form-input" maxlength="150" autocomplete="address-level2" aria-describedby="its-location-hint its-error-location">
                            <span class="form-hint" id="its-location-hint"><?= htmlspecialchars(t('f_location_hint')) ?></span>
                            <span class="form-error" role="alert" id="its-error-location" aria-live="polite"></span>
                        </div>

                        <div class="form-group" id="group-description">
                            <label class="form-label" for="its-description"><?= htmlspecialchars(t('f_description')) ?> <span aria-hidden="true">*</span></label>
                            <textarea id="its-description" name="description" class="form-input form-textarea" required minlength="20" maxlength="3000" aria-required="true" aria-describedby="its-error-description"></textarea>
                            <span class="form-error" role="alert" id="its-error-description" aria-live="polite"></span>
                        </div>

                        <div class="form-row">
                            <div class="form-group" id="group-date">
                                <label class="form-label" for="its-date"><?= htmlspecialchars(t('f_date')) ?></label>
                                <input type="date" id="its-date" name="preferred_date" class="form-input" aria-describedby="its-error-date">
                                <span class="form-error" role="alert" id="its-error-date" aria-live="polite"></span>
                            </div>
                            <div class="form-group" id="group-lang">
                                <label class="form-label" for="its-lang"><?= htmlspecialchars(t('f_lang')) ?></label>
                                <select id="its-lang" name="language" class="form-input">
                                    <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>English</option>
                                    <option value="sw" <?= $lang === 'sw' ? 'selected' : '' ?>>Kiswahili</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn--primary btn--lg contact-submit" id="its-submit">
                            <span id="its-submit-label"><?= htmlspecialchars(t('f_submit')) ?></span>
                        </button>

                        <p class="its-privacy"><?= htmlspecialchars(t('f_privacy')) ?></p>

                        <div class="form-feedback form-feedback--success" id="its-success" role="status" aria-live="polite" hidden>
                            <?= htmlspecialchars(t('f_success')) ?>
                        </div>
                        <div class="form-feedback form-feedback--error" id="its-error" role="alert" aria-live="assertive" hidden>
                            <span id="its-error-text"><?= htmlspecialchars(t('err_generic')) ?></span>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

</main>

<!-- ── Page-specific structured data ── -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Service",
    "name": "IT Support & Networking Services",
    "description": <?= json_encode(t('meta_desc'), JSON_UNESCAPED_UNICODE) ?>,
    "provider": {
        "@type": "Person",
        "name": <?= json_encode(OWNER_NAME) ?>,
        "url": <?= json_encode(SITE_URL) ?>
    },
    "areaServed": {
        "@type": "City",
        "name": "Nyeri",
        "containedInPlace": { "@type": "Country", "name": "Kenya" }
    },
    "availableLanguage": ["English", "Kiswahili"],
    "serviceType": ["Computer repair", "Network installation", "IT support", "Data recovery", "Malware removal"]
}
</script>

<script>
window.ITS_I18N = <?= json_encode([
    'err_name'        => t('err_name'),
    'err_phone'       => t('err_phone'),
    'err_email'       => t('err_email'),
    'err_category'    => t('err_category'),
    'err_method'      => t('err_method'),
    'err_location'    => t('err_location'),
    'err_description' => t('err_description'),
    'err_date'        => t('err_date'),
    'err_generic'     => t('err_generic'),
    'err_server'      => t('err_server'),
    'f_submit'        => t('f_submit'),
    'f_sending'       => t('f_sending'),
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
</script>
<script src="<?= BASE_PATH ?>/assets/js/it-support.js" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
