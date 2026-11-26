/**
 * it-support.js — Enquiry form validation, conditional location field,
 * WhatsApp deep-link switching, and AJAX submission.
 *
 * Validation is field-level: each field is checked on blur/change and the
 * whole form only on submit. Errors clear as soon as a field becomes valid.
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

    function t(key) {
        return I18N[key] || key;
    }

    function el(id) {
        return document.getElementById(id);
    }

    // ── Error helpers ─────────────────────────────────────────
    function showError(fieldId, message) {
        var group = el('group-' + fieldId);
        if (!group) return;
        var errEl = group.querySelector('.form-error');
        var input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = message;
        if (input) input.setAttribute('aria-invalid', 'true');
        group.classList.add('has-error');
    }

    function clearError(fieldId) {
        var group = el('group-' + fieldId);
        if (!group) return;
        var errEl = group.querySelector('.form-error');
        var input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = '';
        if (input) input.removeAttribute('aria-invalid');
        group.classList.remove('has-error');
    }

    // ── Location is required only for on-site requests ────────
    function isOnsite() {
        return methodSel && methodSel.value === 'onsite';
    }

    function syncLocationRequirement() {
        var onsite = isOnsite();
        if (locationInp) locationInp.required = onsite;
        if (locationReq) locationReq.hidden = !onsite;
    }

    if (methodSel) {
        methodSel.addEventListener('change', function () {
            syncLocationRequirement();
            // Re-validate both fields when the method changes.
            validateField('method');
            validateField('location');
        });
        syncLocationRequirement();
    }

    // ── WhatsApp link follows the chosen enquiry language ─────
    if (langSel && waLink) {
        langSel.addEventListener('change', function () {
            var url = langSel.value === 'sw' ? waLink.dataset.waSw : waLink.dataset.waEn;
            if (url) waLink.href = url;
        });
    }

    // ── Per-field validators: return error message or '' ──────
    var FIELD_IDS = ['name', 'phone', 'email', 'category', 'method', 'location', 'description', 'date'];
    var inputId   = { name: 'its-name', phone: 'its-phone', email: 'its-email', category: 'its-category',
                      method: 'its-method', location: 'its-location', description: 'its-description', date: 'its-date' };

    var validators = {
        name: function () {
            var v = el('its-name');
            return !v || v.value.trim().length >= 2 ? '' : t('err_name');
        },
        phone: function () {
            var v = el('its-phone');
            if (!v) return '';
            var digits = v.value.replace(/[^0-9+]/g, '');
            return (digits.length >= 9 && digits.length <= 15 && /^\+?[0-9]+$/.test(digits)) ? '' : t('err_phone');
        },
        email: function () {
            var v = el('its-email');
            var val = v ? v.value.trim() : '';
            return val === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val) ? '' : t('err_email');
        },
        category: function () {
            var v = el('its-category');
            return v && v.value ? '' : t('err_category');
        },
        method: function () {
            var v = el('its-method');
            return v && v.value ? '' : t('err_method');
        },
        location: function () {
            if (!isOnsite()) return '';
            var v = el('its-location');
            return v && v.value.trim().length >= 3 ? '' : t('err_location');
        },
        description: function () {
            var v = el('its-description');
            return v && v.value.trim().length >= 20 ? '' : t('err_description');
        },
        date: function () {
            var v = el('its-date');
            if (!v || v.value === '') return '';
            var today = new Date();
            var isoToday = today.getFullYear() + '-'
                + String(today.getMonth() + 1).padStart(2, '0') + '-'
                + String(today.getDate()).padStart(2, '0');
            if (!/^\d{4}-\d{2}-\d{2}$/.test(v.value) || v.value < isoToday) {
                return t('err_date');
            }
            return '';
        }
    };

    // Validate one field; returns true when valid.
    function validateField(fieldId) {
        var message = validators[fieldId] ? validators[fieldId]() : '';
        if (message === '') {
            clearError(fieldId);
            return true;
        }
        showError(fieldId, message);
        return false;
    }

    function validateForm() {
        var valid = true;
        FIELD_IDS.forEach(function (f) {
            if (!validateField(f)) valid = false;
        });
        return valid;
    }

    // ── Live validation: blur validates, input clears when fixed ──
    FIELD_IDS.forEach(function (fieldId) {
        var input = el(inputId[fieldId]);
        if (!input) return;
        input.addEventListener('blur', function () { validateField(fieldId); });
        input.addEventListener('input', function () {
            if (el('group-' + fieldId).classList.contains('has-error')) {
                validateField(fieldId);
            }
        });
        input.addEventListener('change', function () { validateField(fieldId); });
    });

    // ── Submit ────────────────────────────────────────────────
    var submitting = false;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (submitting) return;
        if (!validateForm()) return;

        submitting = true;

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
                // Server rotates the CSRF token on success — adopt the new
                // one or the next submission without refresh gets a 403.
                if (json.csrf_token) {
                    var csrfInput = form.querySelector('input[name="csrf_token"]');
                    if (csrfInput) csrfInput.value = json.csrf_token;
                }
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
            submitting = false;
            if (submitBtn)   submitBtn.disabled = false;
            if (submitLabel) submitLabel.textContent = t('f_submit') || 'Send request';
        });
    });
})();
