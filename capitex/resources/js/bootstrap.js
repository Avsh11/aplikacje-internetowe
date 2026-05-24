import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';  // Serwer musi wiedziec ze to zapytanie AJAX