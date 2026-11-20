(function () {
    'use strict';

    /**
     * Validasi sisi klien. Tujuannya kenyamanan pengisian form saja;
     * validasi yang menentukan tetap dilakukan di sisi server.
     */

    var form = document.querySelector('form[data-validate="mahasiswa"]');

    /**
     * Konfirmasi penghapusan. Dialog browser dipakai agar tetap sederhana dan
     * tidak menambah ketergantungan. Tanpa JavaScript, halaman hapus.php tetap
     * menampilkan konfirmasi sendiri sebelum data benar-benar dihapus.
     */
    document.querySelectorAll('form[data-confirm]').forEach(function (target) {
        target.addEventListener('submit', function (event) {
            if (!window.confirm(target.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });

    if (!form) {
        return;
    }

    var rules = {
        nim: { required: true, max: 20, label: 'NIM' },
        nama: { required: true, max: 100, label: 'Nama lengkap' },
        alamat: { required: true, label: 'Alamat' },
        prodi: { required: true, max: 100, label: 'Program studi' }
    };

    function errorSlot(field) {
        return form.querySelector('[data-error-for="' + field + '"]');
    }

    function setError(field, message) {
        var input = form.elements[field];
        var slot = errorSlot(field);
        if (input) {
            input.classList.toggle('is-invalid', Boolean(message));
            input.setAttribute('aria-invalid', message ? 'true' : 'false');
        }
        if (slot) {
            slot.textContent = message || '';
        }
    }

    function validateField(field) {
        var rule = rules[field];
        var input = form.elements[field];
        if (!rule || !input) {
            return true;
        }

        var value = (input.value || '').trim();

        if (rule.required && value === '') {
            setError(field, rule.label + ' wajib diisi.');
            return false;
        }
        if (rule.max && value.length > rule.max) {
            setError(field, rule.label + ' maksimal ' + rule.max + ' karakter.');
            return false;
        }

        setError(field, '');
        return true;
    }

    Object.keys(rules).forEach(function (field) {
        var input = form.elements[field];
        if (!input) {
            return;
        }
        input.addEventListener('blur', function () {
            validateField(field);
        });
        input.addEventListener('input', function () {
            if (input.classList.contains('is-invalid')) {
                validateField(field);
            }
        });
    });

    form.addEventListener('submit', function (event) {
        var firstInvalid = null;

        Object.keys(rules).forEach(function (field) {
            var ok = validateField(field);
            if (!ok && firstInvalid === null) {
                firstInvalid = form.elements[field];
            }
        });

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
        }
    });
})();
