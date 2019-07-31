/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

require('../css/app.css');

global.axios = require('axios');

import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import AppAdmin from './components/AppAdmin.vue'

Vue.use(BootstrapVue)

new Vue({
  el: '#app',
  components: { AppAdmin },
  template: '<AppAdmin/>'
})