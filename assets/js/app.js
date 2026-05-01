function showToast(message, type, duration) {
    duration = duration || 4000;
    const icons = {
        success: 'fa-circle-check',
        danger:  'fa-circle-xmark',
        warning: 'fa-triangle-exclamation',
        info:    'fa-circle-info',
    };
    const icon = icons[type] || icons.info;
    const id   = 'toast_' + Date.now();

    const html = `
<div id="${id}" class="toast jj-toast ${type} show align-items-center" role="alert" aria-atomic="true">
    <div class="d-flex align-items-center gap-2 px-3 py-2">
        <i class="fa-solid ${icon} text-${type === 'danger' ? 'danger' : type === 'success' ? 'success' : type}"></i>
        <div class="toast-body flex-grow-1 p-0">${message}</div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>`;

    const container = document.getElementById('toastContainer');
    if (!container) return;
    container.insertAdjacentHTML('beforeend', html);
    const el = document.getElementById(id);
    const toast = new bootstrap.Toast(el, { delay: duration, autohide: true });
    toast.show();
    el.addEventListener('hidden.bs.toast', () => el.remove());
}

function updateUTCClock() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const el = document.getElementById('utcClock');
    if (el) el.textContent = `${pad(now.getUTCHours())}:${pad(now.getUTCMinutes())}:${pad(now.getUTCSeconds())} UTC`;
}

function updateFormTime() {
    const now = new Date();
    const pad = n => String(n).padStart(2, '0');
    const el  = document.getElementById('currentTime');
    if (el) {
        el.textContent = `${pad(now.getUTCDate())}. ${pad(now.getUTCMonth() + 1)}. ${now.getUTCFullYear()} `
                       + `${pad(now.getUTCHours())}:${pad(now.getUTCMinutes())}:${pad(now.getUTCSeconds())}`;
    }
}

setInterval(() => { updateUTCClock(); updateFormTime(); }, 1000);
updateUTCClock();
updateFormTime();

function validateForm() {
    const callsign  = document.getElementById('callsign');
    const band      = document.getElementById('band');
    const mode      = document.getElementById('mode');
    const submitBtn = document.querySelector('button[name="submit"]');
    if (!callsign || !band || !mode || !submitBtn) return;
    submitBtn.disabled = !(callsign.value.trim() && band.value && mode.value);
}

function fillForm(data) {
    const fields = ['callsign','band','mode','qth','locator','rst_tx','rst_rx','scout_name','note'];
    fields.forEach(f => {
        const el = document.getElementById(f);
        if (el) el.value = data[f] ?? '';
    });
    const rid = document.getElementById('record_id');
    if (rid) rid.value = data.id;

    const timeDisplay = document.getElementById('currentTime');
    const timeInput   = document.getElementById('qso_time');
    if (timeDisplay && timeInput) {
        const ts = (data.timestamp ?? '').replace(' ', 'T');
        timeInput.value = ts;
        timeDisplay.classList.add('d-none');
        timeInput.classList.remove('d-none');
    }

    const form = document.getElementById('entryForm');
    if (form) {
        form.classList.add('is-editing');
        setTimeout(() => form.classList.remove('is-editing'), 2000);
    }

    const submitBtn = document.querySelector('button[name="submit"]');
    if (submitBtn) {
        const label = submitBtn.closest('form')?.querySelector('[data-label-save]')?.dataset.labelSave
            || window.JJ_I18N?.action_save || 'Save changes';
        submitBtn.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i>' + label;
    }

    detectModeType();
    validateForm();
}

function clearForm() {
    const fields = ['callsign','band','mode','qth','locator','rst_tx','rst_rx','scout_name','note','record_id'];
    fields.forEach(f => {
        const el = document.getElementById(f);
        if (!el) return;
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else el.value = '';
    });

    const timeDisplay = document.getElementById('currentTime');
    const timeInput   = document.getElementById('qso_time');
    if (timeDisplay && timeInput) {
        timeInput.value = '';
        timeInput.classList.add('d-none');
        timeDisplay.classList.remove('d-none');
    }

    const submitBtn = document.querySelector('button[name="submit"]');
    if (submitBtn) {
        const label = window.JJ_I18N?.action_add_qso || 'Add QSO';
        submitBtn.innerHTML = '<i class="fa-solid fa-check me-1"></i>' + label;
    }

    const form = document.getElementById('entryForm');
    if (form) form.classList.remove('is-editing');

    detectModeType();
    validateForm();
}

function confirmDelete(recordId) {
    if (confirm('Opravdu chcete toto spojení smazat?\nTato akce je nevratná.')) {
        document.getElementById('delete_id').value = recordId;
        document.getElementById('delete_form').submit();
    }
}

function filterTable() {
    const searchEl = document.getElementById('searchInput');
    if (!searchEl) return;
    const q    = searchEl.value.toLowerCase();
    const rows = document.querySelectorAll('#logTable tbody tr.data-row');
    let visible = 0;
    rows.forEach(row => {
        const show = q === '' || row.textContent.toLowerCase().includes(q);
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    const counter = document.getElementById('visibleCount');
    if (counter) counter.textContent = visible;
}

const JOTI_MODES = new Set(['Zoom','Teams','Discord','IRC','EchoLink','WIRES-X','DMR','C4FM','Internet','ChatGPT','Online']);

function detectModeType() {
    const mode      = document.getElementById('mode');
    const bandGroup = document.getElementById('bandGroup');
    const rstGroup  = document.getElementById('rstGroup');
    if (!mode) return;

    const isJOTI = JOTI_MODES.has(mode.value);

    if (bandGroup) {
        bandGroup.style.opacity = isJOTI ? '.4' : '1';
        const lbl = bandGroup.querySelector('label');
        if (lbl) lbl.innerHTML = isJOTI
            ? '<i class="fa-solid fa-wifi me-1"></i>Platf.'
            : '<i class="fa-solid fa-wave-square me-1"></i>Pásmo';
    }
    if (rstGroup) {
        rstGroup.style.opacity = isJOTI ? '.4' : '1';
    }

    const badge = document.getElementById('modeTypeBadge');
    if (badge) {
        badge.className = isJOTI ? 'badge-joti ms-2' : 'badge-jota ms-2';
        badge.textContent = isJOTI ? 'JOTI' : 'JOTA';
        badge.style.display = mode.value ? '' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    validateForm();
    detectModeType();

    const csInput = document.getElementById('callsign');
    if (csInput) {
        csInput.addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
            validateForm();
        });
    }

    const modeEl = document.getElementById('mode');
    if (modeEl) modeEl.addEventListener('change', () => { detectModeType(); validateForm(); });

    const bandEl = document.getElementById('band');
    if (bandEl) bandEl.addEventListener('change', validateForm);

    const srch = document.getElementById('searchInput');
    if (srch) srch.addEventListener('input', filterTable);

    document.querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover' }));
});
