/**
 * contact.js — Form validation & AJAX submission
 */
(function () {
    'use strict';

    const form      = document.getElementById('contact-form');
    if (!form) return;

    const submitBtn  = document.getElementById('form-submit');
    const submitLabel= document.getElementById('submit-label');
    const successBox = document.getElementById('form-success');
    const errorBox   = document.getElementById('form-error');
    const errorText  = document.getElementById('form-error-text');

    // ── Validators ─────────────────────────────────────────
    const validators = {
        name:         { min: 2, max: 100, msg: 'Name must be 2–100 characters.' },
        email:        { regex: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, msg: 'Please enter a valid email address.' },
        project_type: { required: true, msg: 'Please select a project type.' },
        subject:      { min: 5, max: 150, msg: 'Subject must be 5–150 characters.' },
        message:      { min: 20, max: 3000, msg: 'Message must be 20–3000 characters.' },
    };

    function getGroup(fieldName) {
        return document.getElementById('group-' + fieldName) ||
               document.getElementById('group-type');
    }

    function getError(fieldName) {
        return document.getElementById('error-' + fieldName) ||
               document.getElementById('error-type');
    }

    function setError(fieldName, show) {
        const group = getGroup(fieldName);
        const errEl = getError(fieldName);
        if (group) group.classList.toggle('has-error', show);
        if (errEl) errEl.style.display = show ? 'block' : 'none';
    }

    function validateField(name, value) {
        const rule = validators[name];
        if (!rule) return true;

        if (rule.required && !value.trim()) { setError(name, true); return false; }
        if (rule.regex && !rule.regex.test(value.trim())) { setError(name, true); return false; }
        if (rule.min && value.trim().length < rule.min) { setError(name, true); return false; }
        if (rule.max && value.trim().length > rule.max) { setError(name, true); return false; }

        setError(name, false);
        return true;
    }

    function validateAll() {
        let valid = true;
        ['name', 'email', 'project_type', 'subject', 'message'].forEach(function (f) {
            const el = form.querySelector('[name="' + f + '"]');
            if (el && !validateField(f, el.value)) valid = false;
        });
        return valid;
    }

    // Live validation
    form.querySelectorAll('input, select, textarea').forEach(function (el) {
        ['blur', 'change'].forEach(function (ev) {
            el.addEventListener(ev, function () {
                validateField(el.name, el.value);
            });
        });
    });

    // ── Submit ─────────────────────────────────────────────
    function setLoading(loading) {
        submitBtn.disabled = loading;
        submitLabel.textContent = loading ? 'Sending...' : 'Send Message';
        submitBtn.style.opacity = loading ? '0.7' : '1';
    }

    function showFeedback(type) {
        successBox.style.display = 'none';
        errorBox.style.display   = 'none';
        if (type === 'success') successBox.style.display = 'flex';
        if (type === 'error')   errorBox.style.display   = 'flex';
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validateAll()) {
            // Scroll to first error
            const firstError = form.querySelector('.has-error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return;
        }

        setLoading(true);
        showFeedback(null);

        const formData = new FormData(form);

        fetch(form.action, {
            method:  'POST',
            body:    formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            setLoading(false);
            if (data.success) {
                form.reset();
                showFeedback('success');
                successBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            } else {
                if (errorText) errorText.textContent = data.message || 'Something went wrong.';
                showFeedback('error');
            }
        })
        .catch(function () {
            setLoading(false);
            if (errorText) {
                errorText.textContent = 'Network error. Please check your connection and try again.';
            }
            showFeedback('error');
        });
    });
})();
