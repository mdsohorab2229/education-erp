import './bootstrap';
import 'bootstrap';

import 'tom-select/dist/css/tom-select.bootstrap5.css';
import TomSelect from 'tom-select';

import Alpine from 'alpinejs';
import attendance from './attendance';
import marksEntry from './marks-entry';
import marksApproval from './marks-approval';
import Swal from 'sweetalert2';

window.Alpine = Alpine;
window.TomSelect = TomSelect;
window.Swal = Swal;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

Alpine.data('attendance', attendance);
Alpine.data('marksEntry', marksEntry);
Alpine.data('marksApproval', marksApproval);

Alpine.start();
