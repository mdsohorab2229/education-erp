import './bootstrap';
import 'bootstrap';

import 'tom-select/dist/css/tom-select.bootstrap5.css';
import TomSelect from 'tom-select';

import Alpine from 'alpinejs';
import attendance from './attendance';

window.Alpine = Alpine;
window.TomSelect = TomSelect;

Alpine.data('attendance', attendance);

Alpine.start();
