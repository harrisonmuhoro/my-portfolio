<?php
/**
 * sections/contact.php
 */
require_once __DIR__ . '/../config/config.php';

// Session is already started in pages/index.php (before any HTML output)
// Just generate CSRF token if not set
if (empty($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION[CSRF_TOKEN_NAME];
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/contact.css">

<section class="contact section" id="contact" aria-labelledby="contact-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">Get In Touch</p>
            <h2 class="section-header__title" id="contact-title" style="display:none;">Contact</h2>
        </header>

        <div class="contact__grid">

            <!-- Left: Info -->
            <div class="contact__info reveal">
                <h2 class="contact__info-title">
                    Let's Build<br>Something <em>Together</em>
                </h2>
                <p class="contact__info-desc">
                    Whether you're a business owner, recruiter, government body, or institution — I'm ready to discuss your project and deliver a solution that fits your needs.
                </p>

                <div class="contact__methods">
                    <a href="<?= SOCIAL_LINKS['whatsapp'] ?>" target="_blank" rel="noopener noreferrer" class="contact__method">
                        <div class="contact__method-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        </div>
                        <div>
                            <div class="contact__method-label">WhatsApp</div>
                            <div class="contact__method-value"><?= PHONE_PRIMARY ?></div>
                        </div>
                    </a>

                    <a href="<?= SOCIAL_LINKS['email'] ?>" class="contact__method">
                        <div class="contact__method-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div>
                            <div class="contact__method-label">Email</div>
                            <div class="contact__method-value"><?= EMAIL_ADDRESS ?></div>
                        </div>
                    </a>

                    <div class="contact__method" style="cursor:default;">
                        <div class="contact__method-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.7 19.79 19.79 0 0 1 1.65 5.11 2 2 0 0 1 3.62 3h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 10a16 16 0 0 0 6 6l.71-.71a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        </div>
                        <div>
                            <div class="contact__method-label">Phone</div>
                            <div class="contact__method-value"><?= PHONE_PRIMARY ?></div>
                        </div>
                    </div>
                </div>

                <div class="contact__social">
                    <span class="contact__social-label">Social</span>
                    <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer" class="contact__social-link" aria-label="GitHub">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                    </a>
                    <a href="<?= SOCIAL_LINKS['linkedin'] ?>" target="_blank" rel="noopener noreferrer" class="contact__social-link" aria-label="LinkedIn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    </a>
                    <a href="<?= SOCIAL_LINKS['facebook'] ?>" target="_blank" rel="noopener noreferrer" class="contact__social-link" aria-label="Facebook">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                </div>
            </div>

            <!-- Right: Form -->
            <div class="contact__form-wrap reveal reveal-delay-2">
                <h3 class="form__title">Send a Message</h3>

                <form
                    id="contact-form"
                    action="<?= BASE_PATH ?>/backend/contact_handler.php"
                    method="POST"
                    novalidate
                    aria-label="Contact form"
                >
                    <input type="hidden" name="<?= CSRF_TOKEN_NAME ?>" value="<?= htmlspecialchars($csrf_token) ?>">
                    <!-- Honeypot: hidden from real users, bots fill it -->
                    <input type="text" name="website" class="form__honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

                    <div class="form__row">
                        <div class="form__group" id="group-name">
                            <label class="form__label" for="name">Full Name <span aria-hidden="true">*</span></label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form__input"
                                placeholder="John Doe"
                                required
                                minlength="2"
                                maxlength="100"
                                autocomplete="name"
                                aria-required="true"
                            >
                            <span class="form__error" role="alert" id="error-name">Please enter your name.</span>
                        </div>

                        <div class="form__group" id="group-email">
                            <label class="form__label" for="email">Email Address <span aria-hidden="true">*</span></label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form__input"
                                placeholder="you@company.com"
                                required
                                maxlength="254"
                                autocomplete="email"
                                aria-required="true"
                            >
                            <span class="form__error" role="alert" id="error-email">Please enter a valid email.</span>
                        </div>
                    </div>

                    <div class="form__row">
                        <div class="form__group" id="group-phone">
                            <label class="form__label" for="phone">Phone Number</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                class="form__input"
                                placeholder="+254 700 000 000"
                                maxlength="20"
                                autocomplete="tel"
                            >
                        </div>

                        <div class="form__group" id="group-type">
                            <label class="form__label" for="project_type">Project Type <span aria-hidden="true">*</span></label>
                            <select id="project_type" name="project_type" class="form__select form__input" required aria-required="true">
                                <option value="" disabled selected>Select a type...</option>
                                <option value="website">Website Development</option>
                                <option value="system">Business / Org System</option>
                                <option value="backend">Backend Integration</option>
                                <option value="maintenance">Maintenance & Support</option>
                                <option value="other">Other / Not Sure</option>
                            </select>
                            <span class="form__error" role="alert" id="error-type">Please select a project type.</span>
                        </div>
                    </div>

                    <div class="form__group" id="group-subject">
                        <label class="form__label" for="subject">Subject <span aria-hidden="true">*</span></label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            class="form__input"
                            placeholder="Brief description of your project"
                            required
                            minlength="5"
                            maxlength="150"
                            aria-required="true"
                        >
                        <span class="form__error" role="alert" id="error-subject">Please enter a subject.</span>
                    </div>

                    <div class="form__group" id="group-message">
                        <label class="form__label" for="message">Message <span aria-hidden="true">*</span></label>
                        <textarea
                            id="message"
                            name="message"
                            class="form__textarea"
                            placeholder="Tell me about your project, goals, timeline, and any specific requirements..."
                            required
                            minlength="20"
                            maxlength="3000"
                            aria-required="true"
                        ></textarea>
                        <span class="form__error" role="alert" id="error-message">Please enter your message (minimum 20 characters).</span>
                    </div>

                    <button type="submit" class="btn btn--primary btn--lg form__submit" id="form-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        <span id="submit-label">Send Message</span>
                    </button>

                    <div class="form__feedback" id="form-success" role="status" aria-live="polite">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                        Message sent successfully! I'll get back to you within 24 hours.
                    </div>
                    <div class="form__feedback" id="form-error" role="alert" aria-live="assertive">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span id="form-error-text">Something went wrong. Please try again or contact me directly.</span>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/contact.js" defer></script>
