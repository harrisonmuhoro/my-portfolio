<?php
$page_css = ['contact'];
require_once __DIR__ . '/../includes/header.php';

if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION[CSRF_TOKEN_NAME];
?>

<main id="main-content">

    <section class="contact-page section" id="contact">
        <div class="container">

            <div class="contact-page__grid">

                <div class="contact-page__info">
                    <span class="section-heading__label">Contact</span>
                    <h1 class="contact-page__title">A note is enough.</h1>
                    <p class="contact-page__desc">
                        I take full-time roles, freelance work, and consulting. On weekdays I usually reply within a day.
                    </p>

                    <ul class="contact-page__channels">
                        <li>
                            <span>Email</span>
                            <a href="<?= SOCIAL_LINKS['email'] ?>"><?= EMAIL_ADDRESS ?></a>
                        </li>
                        <li>
                            <span>WhatsApp</span>
                            <a href="<?= SOCIAL_LINKS['whatsapp'] ?>" target="_blank" rel="noopener noreferrer"><?= PHONE_PRIMARY ?></a>
                        </li>
                        <li>
                            <span>LinkedIn</span>
                            <a href="<?= SOCIAL_LINKS['linkedin'] ?>" target="_blank" rel="noopener noreferrer">zion-harrison</a>
                        </li>
                        <li>
                            <span>GitHub</span>
                            <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer">harrisonmuhoro</a>
                        </li>
                        <li>
                            <span>CV</span>
                            <a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer">Download PDF</a>
                        </li>
                    </ul>
                </div>

                <div class="contact-page__form">
                    <h2 class="contact-page__form-title">Or use this form</h2>

                    <form
                        id="contact-form"
                        action="<?= BASE_PATH ?>/backend/contact_handler.php"
                        method="POST"
                        novalidate
                        aria-label="Contact form"
                    >
                        <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="text" name="website" class="u-visually-hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

                        <div class="form-row">
                            <div class="form-group" id="group-name">
                                <label class="form-label" for="name">Name <span aria-hidden="true">*</span></label>
                                <input type="text" id="name" name="name" class="form-input" required minlength="2" maxlength="100" autocomplete="name" aria-required="true" aria-describedby="error-name">
                                <span class="form-error" role="alert" id="error-name" aria-live="polite"></span>
                            </div>
                            <div class="form-group" id="group-email">
                                <label class="form-label" for="email">Email <span aria-hidden="true">*</span></label>
                                <input type="email" id="email" name="email" class="form-input" required maxlength="254" autocomplete="email" aria-required="true" aria-describedby="error-email">
                                <span class="form-error" role="alert" id="error-email" aria-live="polite"></span>
                            </div>
                        </div>

                        <div class="form-group" id="group-subject">
                            <label class="form-label" for="subject">Subject <span aria-hidden="true">*</span></label>
                            <input type="text" id="subject" name="subject" class="form-input" required minlength="5" maxlength="150" aria-required="true" aria-describedby="error-subject">
                            <span class="form-error" role="alert" id="error-subject" aria-live="polite"></span>
                        </div>

                        <div class="form-group" id="group-message">
                            <label class="form-label" for="message">Message <span aria-hidden="true">*</span></label>
                            <textarea id="message" name="message" class="form-input form-textarea" required minlength="20" maxlength="3000" aria-required="true" aria-describedby="error-message"></textarea>
                            <span class="form-error" role="alert" id="error-message" aria-live="polite"></span>
                        </div>

                        <button type="submit" class="btn btn--primary btn--lg contact-submit" id="form-submit">
                            <span id="submit-label">Send</span>
                        </button>

                        <div class="form-feedback form-feedback--success" id="form-success" role="status" aria-live="polite" hidden>
                            Received — I will reply within a weekday.
                        </div>
                        <div class="form-feedback form-feedback--error" id="form-error" role="alert" aria-live="assertive" hidden>
                            <span id="form-error-text">Something went wrong. Email me directly instead.</span>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>

</main>

<script src="<?= BASE_PATH ?>/assets/js/contact.js" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
