import Alpine from 'alpinejs';

import './offline-auth';
import './pwa';

window.Alpine = Alpine;

Alpine.start();

if (window.location.pathname.startsWith('/guru')) {
    import('./finance-offline');
    import('./attendance-offline');
}
