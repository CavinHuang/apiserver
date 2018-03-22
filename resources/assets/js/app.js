/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Vue from 'vue'
import App from './App.vue'
import ElementUI from 'element-ui'
import router from './routers'
import 'element-ui/lib/theme-default/index.css'
import 'font-awesome/css/font-awesome.css'
import store from './store'
import axios from './api/http'

///import IEcharts from 'vue-echarts-v3/src/full.vue'

Vue.use(ElementUI)

Vue.component(
  'passport-clients',
  require('./components/passport/Clients.vue')
);

Vue.component(
  'passport-authorized-clients',
  require('./components/passport/AuthorizedClients.vue')
);

Vue.component(
  'passport-personal-access-tokens',
  require('./components/passport/PersonalAccessTokens.vue')
);

Vue.prototype.axios = axios

const app = new Vue({
  el: '#app',
  router,
  store,
  template: '<App/>',
  components: { App }
});
