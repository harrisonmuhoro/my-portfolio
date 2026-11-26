/**
 * it-support.js — Enquiry form validation, conditional location field,
 * WhatsApp deep-link switching, and AJAX submission.
 */
(function () {
    'use strict';

    var I18N = window.ITS_I18N || {};

    var form       = document.getElementById('it-support-form');
    if (!form) return;

    var submitBtn   = document.getElementById('its-submit');
    var submitLabel = document.getElementById('its-submit-label');
    var successEl   = document.getElementById('its-success');
    var errorEl     = document.getElementById('its-error');
    var errorText   = document.getElementById('its-error-text');
    var methodSel   = document.getElementById('its-method');
    var locationInp = document.getElementById('its-location');
    var locationReq = document.getElementById('its-location-required');
    var langSel     = document.getElementById('its-lang');
    var waLink      = document.getElementById('its-wa-link');

    // ── Error helpers ─────────────────────────────────────────
    function groupOf(fieldId) {
        return document.getElementById('group-' + fieldId);
    }

    function showError(fieldId, message) {
        var group = groupOf(fieldId);
        if (!group) return;
        var errEl = group.querySelector('.form-error');
        var input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = message;
        if (input) input.setAttribute('aria-invalid', 'true');
        group.classList.add('has-error');
    }

    function clearError(fieldId) {
        var group = groupOf(fieldId);
        if (!group) return;
        var errEl = group.querySelector('.form-error');
        var input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = '';
        if (input) input.removeAttribute('aria-invalid');
        group.classList.remove('has-error');
    }

    function t(key) {
        return I18N[key] || key;
    }

    // ── Location is required only for on-site requests ────────
    function syncLocationRequirement() {
        var onsite = methodSel && methodSel.value === 'onsite';
        if (locationInp) {
            locationInp.required = onsite;
        }
        if (locationReq) {
            locationReq.hidden = !onsite;
        }
    }

    if (methodSel) {
        methodSel.addEventListener('change', syncLocationRequirement);
        syncLocationRequirement();
    }

    // ── WhatsApp link follows the chosen enquiry language ─────
    if (langSel && waLink) {
        langSel.addEventListener('change', function () {
            var url = langSel.value === 'sw' ? waLink.dataset.waSw : waLink.dataset.waEn;
            if (url) waLink.href = url;
        });
    }

    // ── Validation ────────────────────────────────────────────
    function validateForm() {
        var valid = true;

        ['name', 'phone', 'email', 'category', 'method', 'location', 'description', 'date']
            .forEach(function (f) { clearError(f); });

        var name    = document.getElementById('its-name');
        var phone   = document.getElementById('its-phone');
        var email   = document.getElementById('its-email');
        var cat     = document.getElementById('its-category');
        var method  = document.getElementById('its-method');
        var loc     = document.getElementById('its-location');
        var desc    = document.getElementById('its-description');
        var date    = document.getElementById('its-date');

        if (!name || name.value.trim().length < 2) {
            showError('name', t('err_name'));
            valid = false;
        }

        var phoneDigits = phone ? phone.value.replace(/[^0-9+]/g, '') : '';
        if (!phone || phoneDigits.length < 9 || phoneDigits.length > 15 || !/^\+?[0-9]+$/.test(phoneDigits)) {
            showError('phone', t('err_phone'));
            valid = false;
        }

        var emailVal = email ? email.value.trim() : '';
        if (emailVal !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
            showError('email', t('err_email'));
            valid = false;
        }

        if (!cat || !cat.value) {
            showError('category', t('err_category'));
            valid = false;
        }

        if (!method || !method.value) {
            showError('method', t('err_method'));
            valid = false;
        }

        var onsite = method && method.value === 'onsite';
        if (onsite && (!loc || loc.value.trim().length < 3)) {
            showError('location', t('err_location'));
            valid = false;
        }

        if (!desc || desc.value.trim().length < 20) {
            showError('description', t('err_description'));
            valid = false;
        }

        if (date && date.value) {
            var today = new Date();
            var y = today.getFullYear();
            var m = String(today.getMonth() + 1).padStart(2, '0');
            var d = String(today.getDate()).padStart(2, '0');
            if (date.value < y + '-' + m + '-' + d) {
                date.value = '';
            }
        }

        return valid;
    }

    // ── Live validation on blur ───────────────────────────────
    ['its-name', 'its-phone', 'its-email', 'its-category', 'its-method', 'its-location', 'its-description']
        .forEach(function (id) {
            var el = document.getElementById(id);
            if (el) {
                el.addEventListener('blur', function () { validateForm(); });
                el.addEventListener('change', function () { validateForm(); });
            }
        });

    // ── Submit ────────────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) return;

        if (successEl) successEl.hidden = true;
        if (errorEl)   errorEl.hidden   = true;

        if (submitBtn)   submitBtn.disabled = true;
        if (submitLabel) submitLabel.textContent = t('f_sending') || 'Sending…';

        fetch(form.action, {
            method: 'POST',
            body:   new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(function (res) {
            return res.json().catch(function () {
                throw new Error(t('err_server') || 'Server error.');
            });
        })
        .then(function (json) {
            if (json.success) {
                if (successEl) successEl.hidden = false;
                form.reset();
                syncLocationRequirement();
            } else {
                throw new Error(json.message || t('err_generic'));
            }
        })
        .catch(function (err) {
            if (errorEl)  errorEl.hidden = false;
            if (errorText) errorText.textContent = err.message || t('err_generic');
        })
        .finally(function () {
            if (submitBtn)   submitBtn.disabled = false;
            if (submitLabel) submitLabel.textContent = t('f_submit') || 'Send request';
        });
    });
})();
