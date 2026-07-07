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
    function showError(groupId, message) {
        const group = document.getElementById(groupId);
        if (!group) return;
        const errEl = group.querySelector('.form-error');
        const input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = message;
        if (input) input.setAttribute('aria-invalid', 'true');
        group.classList.add('has-error');
    }

    function clearError(groupId) {
        const group = document.getElementById(groupId);
        if (!group) return;
        const errEl = group.querySelector('.form-error');
        const input = group.querySelector('.form-input');
        if (errEl) errEl.textContent = '';
        if (input) input.removeAttribute('aria-invalid');
        group.classList.remove('has-error');
    }

    function validateForm() {
        let valid = true;

        const name    = document.getElementById('name');
        const email   = document.getElementById('email');
        const subject = document.getElementById('subject');
        const message = document.getElementById('message');

        clearError('group-name');
        clearError('group-email');
        clearError('group-subject');
        clearError('group-message');

        if (!name || name.value.trim().length < 2) {
            showError('group-name', 'Please enter your name.');
            valid = false;
        }

        const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email || !emailRe.test(email.value.trim())) {
            showError('group-email', 'Please enter a valid email address.');
            valid = false;
        }

        if (!subject || subject.value.trim().length < 5) {
            showError('group-subject', 'Please enter a subject (min 5 characters).');
            valid = false;
        }

        if (!message || message.value.trim().length < 20) {
            showError('group-message', 'Please write a message (min 20 characters).');
            valid = false;
        }

        return valid;
    }

    // ── Live validation on blur ─────────────────
    ['name', 'email', 'subject', 'message'].forEach(function (fieldId) {
        const el = document.getElementById(fieldId);
        if (el) {
            el.addEventListener('blur', function () { validateForm(); });
        }
    });

    // ── Submit ──────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validateForm()) return;

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
            if (!res.ok) throw new Error('Server error: ' + res.status);
            return res.json();
        })
        .then(function (json) {
            if (json.success) {
                if (successEl) successEl.hidden = false;
                form.reset();
            } else {
                throw new Error(json.message || 'Unexpected error.');
            }
        })
        .catch(function (err) {
            if (errorEl)  errorEl.hidden = false;
            if (errorText) errorText.textContent = err.message || 'Something went wrong. Please contact me directly.';
        })
        .finally(function () {
            if (submitBtn)  submitBtn.disabled = false;
            if (submitLabel) submitLabel.textContent = 'Send Message';
        });
    });

})();
