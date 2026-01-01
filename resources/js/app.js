import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Toast helper function
window.showToast = function(message, type = 'info') {
    window.dispatchEvent(new CustomEvent('notify', {
        detail: {
            message: message,
            type: type
        }
    }));
};

Alpine.start();
