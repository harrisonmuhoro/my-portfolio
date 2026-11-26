(function () {
    'use strict';

    const form      = document.getElementById('contact-form');
    if (!form) return;

    const submitBtn   = document.getElementById('form-submit');
    const submitLabel = document.getElementById('submit-label');
    const successEl   = document.getElementById('form-success');
    const errorEl     = document.getElementById('form-error');
    const errorText   = document.getElementById('form-error-text');

    // ── Validation helpers ──────────────────────
    // Errors are shown per-field: a field is validated on blur/change and
    // re-checked on input once it has an error, so untouched fields never
    // show errors prematurely.
    function showError(fieldId, message) {
        const group = document.getElementById('group-' + fieldId);
        if (!group) return;
        const errEl = group.querySelector('.form-error');
        const input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = message;
        if (input) input.setAttribute('aria-invalid', 'true');
        group.classList.add('has-error');
    }

    function clearError(fieldId) {
        const group = document.getElementById('group-' + fieldId);
        if (!group) return;
        const errEl = group.querySelector('.form-error');
        const input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = '';
        if (input) input.removeAttribute('aria-invalid');
        group.classList.remove('has-error');
    }

    function el(id) {
        return document.getElementById(id);
    }

    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Per-field validators: return an error message or '' when valid.
    const FIELD_IDS = ['name', 'email', 'subject', 'message'];
    const validators = {
        name: function () {
            const v = el('name');
            return !v || v.value.trim().length >= 2 ? '' : 'Please enter your name.';
        },
        email: function () {
            const v = el('email');
            return v && emailRe.test(v.value.trim()) ? '' : 'Please enter a valid email address.';
        },
        subject: function () {
            const v = el('subject');
            return v && v.value.trim().length >= 5 ? '' : 'Please enter a subject (min 5 characters).';
        },
        message: function () {
            const v = el('message');
            return v && v.value.trim().length >= 20 ? '' : 'Please write a message (min 20 characters).';
        }
    };

    // Validate one field; returns true when valid.
    function validateField(fieldId) {
        const message = validators[fieldId] ? validators[fieldId]() : '';
        if (message === '') {
            clearError(fieldId);
            return true;
        }
        showError(fieldId, message);
        return false;
    }

    function validateForm() {
        let valid = true;
        FIELD_IDS.forEach(function (fieldId) {
            if (!validateField(fieldId)) valid = false;
        });
        return valid;
    }

    // ── Live validation: blur validates, input re-checks errors ──
    FIELD_IDS.forEach(function (fieldId) {
        const input = el(fieldId);
        if (!input) return;
        input.addEventListener('blur', function () { validateField(fieldId); });
        input.addEventListener('input', function () {
            const group = el('group-' + fieldId);
            if (group && group.classList.contains('has-error')) {
                validateField(fieldId);
            }
        });
        input.addEventListener('change', function () { validateField(fieldId); });
    });

    // ── Submit ──────────────────────────────────
    let submitting = false;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (submitting) return;
        if (!validateForm()) return;

        submitting = true;

        // Hide previous feedback
        if (successEl) successEl.hidden = true;
        if (errorEl)   errorEl.hidden   = true;

        // Loading state
        if (submitBtn) submitBtn.disabled  = true;
        if (submitLabel) submitLabel.textContent = 'Sending…';

        const data = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body:   data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(function (res) {
            // Parse the JSON body regardless of status — the API returns
            // structured errors (422/429/503) the user should see.
            return res.json().catch(function () {
                throw new Error('Server error. Please try again.');
            });
        })
        .then(function (json) {
            if (json.success) {
                if (successEl) successEl.hidden = false;
                form.reset();
                // Server rotates the CSRF token on success — adopt the new
                // one or the next submission without refresh gets a 403.
                if (json.csrf_token) {
                    const csrfInput = form.querySelector('input[name="csrf_token"]');
                    if (csrfInput) csrfInput.value = json.csrf_token;
                }
            } else {
                throw new Error(json.message || 'Unexpected error.');
            }
        })
        .catch(function (err) {
            if (errorEl)  errorEl.hidden = false;
            if (errorText) errorText.textContent = err.message || 'Something went wrong. Please contact me directly.';
        })
        .finally(function () {
            submitting = false;
            if (submitBtn)  submitBtn.disabled = false;
            if (submitLabel) submitLabel.textContent = 'Send Message';
        });
    });

})();
