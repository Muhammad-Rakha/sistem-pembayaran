import './bootstrap';
import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';

window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Initialize Flatpickr across the app
document.addEventListener('DOMContentLoaded', () => {
    // Standard Datepicker
    flatpickr('.datepicker', {
        locale: Indonesian,
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d',
        allowInput: true,
        disableMobile: true,
    });

    // DateTime Picker (for Simulator & Timestamps)
    flatpickr('.datetimepicker', {
        locale: Indonesian,
        enableTime: true,
        time_24hr: true,
        altInput: true,
        altFormat: 'j F Y, H:i',
        dateFormat: 'Y-m-d H:i:s',
        allowInput: true,
        disableMobile: true,
    });
});

Alpine.start();
