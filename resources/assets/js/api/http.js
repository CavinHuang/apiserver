/**
 * Created by Administrator on 2017/8/8 0008.
 */
import axios from 'axios'
import store from '../store/index.js'
import * as types from '../store/types'
import router from '../routers'

// axios 配置
axios.defaults.timeout = 5000;
axios.defaults.baseURL = 'http://api.tywebs.cn/api';

// http://api.tywebs.cn/api   http://127.0.0.1:8000/api/

// http request 拦截器
axios.interceptors.request.use(
  config => {
  if (sessionStorage.token) {
    config.headers.Authorization = `Bearer ${sessionStorage.token}`;
  }
  return config;
},
err => {
  return Promise.reject(err);
});

// http response 拦截器
axios.interceptors.response.use(
  response => {
  return response;
},
error => {
  if (error.response) {
    switch (error.response.status) {
      case 401:
        // 401 清除token信息并跳转到登录页面
        store.commit(types.LOGOUT);
        let redirect_rul = router.currentRoute.fullPath.indexOf('?') != -1 ? router.currentRoute.fullPath.split('?')[1] : router.currentRoute.fullPath;

        router.replace({
          path: '/login',
          query: {redirect: redirect_rul}
        })
    }
  }
  // console.log(JSON.stringify(error));//console : Error: Request failed with status code 402
  return Promise.reject(error.response.data)
});

export default axios;

