import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.css';

window.Alpine = Alpine;
window.$ = $;
window.jQuery = $;

select2();

Alpine.start();
