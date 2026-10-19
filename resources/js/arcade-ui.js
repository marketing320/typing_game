// Presentation only: these controls never start, submit, or score an attempt.
const cabinet = document.getElementById('arcade-cabinet');
const fullscreenButton = document.querySelector('[data-fullscreen-toggle]');

if (cabinet && fullscreenButton && document.fullscreenEnabled) {
    fullscreenButton.hidden = false;

    document.addEventListener('fullscreenchange', () => {
        const active = document.fullscreenElement === cabinet;
        const label = active ? 'Exit fullscreen' : 'Enter fullscreen';
        fullscreenButton.setAttribute('aria-pressed', String(active));
        fullscreenButton.setAttribute('aria-label', label);
        fullscreenButton.title = label;
        fullscreenButton.querySelector('[data-fullscreen-label]').textContent = label;
    });

    fullscreenButton.addEventListener('click', async () => {
        try {
            if (document.fullscreenElement === cabinet) {
                await document.exitFullscreen();
            } else {
                await cabinet.requestFullscreen();
            }
        } catch {
            document.querySelector('[data-fullscreen-status]').textContent = 'Fullscreen is unavailable in this browser.';
        }
    });
}

// Mirror the existing character states instead of maintaining a second game state.
const textDisplay = document.getElementById('text-display');
const raceLane = document.querySelector('.race-lane');

if (textDisplay && raceLane) {
    const updateKart = () => {
        const total = textDisplay.querySelectorAll('.char').length;
        const typed = textDisplay.querySelectorAll('.char.text-green-600, .char.text-red-500').length;
        raceLane.style.setProperty('--race-progress', `${total ? typed / total * 100 : 0}%`);
    };

    new MutationObserver(updateKart).observe(textDisplay, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class'],
    });
    updateKart();
}

// Keep keyboard focus inside each open game dialog, including the country picker.
const dialogs = Array.from(document.querySelectorAll('[role="dialog"]'));
const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]):not([readonly]), select, textarea, [tabindex="0"]';
const visibleControls = dialog => Array.from(dialog.querySelectorAll(focusableSelector))
    .filter(control => control.getClientRects().length && !control.closest('[hidden]'));

for (const dialog of dialogs) {
    let wasOpen = false;
    let previousFocus = null;
    const syncDialog = () => {
        const open = !dialog.classList.contains('hidden');
        if (open && !wasOpen) {
            previousFocus = document.activeElement;
            visibleControls(dialog)[0]?.focus({ preventScroll: true });
        } else if (!open && wasOpen && dialog.contains(document.activeElement)) {
            previousFocus?.focus({ preventScroll: true });
        }
        wasOpen = open;
    };

    dialog.addEventListener('keydown', event => {
        if (event.key !== 'Tab' || dialog.classList.contains('hidden')) return;
        const controls = visibleControls(dialog);
        const first = controls[0];
        const last = controls.at(-1);
        if (!first) return;
        if (event.shiftKey && (document.activeElement === first || !controls.includes(document.activeElement))) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });

    new MutationObserver(syncDialog).observe(dialog, { attributes: true, attributeFilter: ['class'] });
    syncDialog();
}

const practiceResult = document.getElementById('result-panel');
if (practiceResult) {
    new MutationObserver(() => {
        if (practiceResult.classList.contains('hidden')) return;
        const heading = practiceResult.querySelector('h2');
        heading?.setAttribute('tabindex', '-1');
        heading?.focus({ preventScroll: true });
        practiceResult.scrollIntoView({ behavior: 'auto', block: 'nearest' });
    }).observe(practiceResult, { attributes: true, attributeFilter: ['class'] });
}
