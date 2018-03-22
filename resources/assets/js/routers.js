/**
 * Created by Administrator on 2017/8/8 0008.
 */

import Vue from 'vue'
import VueRouter from 'vue-router'


import store from './store'
import * as types from './store/types'

Vue.use(VueRouter)

import Test from './components/test'
import Login from './components/Login'
import Home from './components/Home'
import Main from './components/Main'
import UserInfo from './components/UserInfo'
import Register from './components/Register'
import Welcome from './components/Welcome'

const router = new VueRouter({
  routes: [
    { path: '/', name: 'App', redirect: '/login',hidden: true },
    { path: '/login', name:'Login', component: Login, hidden: true },
    { path: '/register', name:'register', component: Register, hidden: true },
    { path: '/home', name:'主页', component: Home, iconCls: 'el-icon-message',meta: {
      requireAuth: true,  // 添加该字段，表示进入这个路由是需要登录的
    }, children:[
      { path: '/home/welcome', component: Welcome, name: '主页', hidden: true,meta: {
        requireAuth: true,  // 添加该字段，表示进入这个路由是需要登录的
      } },
      { path: '/main', component: Main, name: '应用管理', hidden: true,meta: {
        requireAuth: true,  // 添加该字段，表示进入这个路由是需要登录的
      } },
      { path: '/home/main', component: Main, name: '应用管理', hidden: false,meta: {
        requireAuth: true,  // 添加该字段，表示进入这个路由是需要登录的
      } },
      { path: '/home/userinfo', component: UserInfo, name:'个人信息', hidden: false, meta: {
        requireAuth: true
      }}
    ] },
  ]
})

router.beforeEach((to, from, next) => {
  if (to.meta.requireAuth) {  // 判断该路由是否需要登录权限
    if (!store.state.token && !sessionStorage.token) {  // 通过vuex state获取当前的token是否存在

      let redirect_url = to.fullPath.indexOf('redirect') != -1 ? to.fullPath :  {redirect: to.fullPath} ;
      next({
        path: '/login',
        query: redirect_url  // 将跳转的路由path作为参数，登录成功后跳转到该路由
      })
    } else {
      next();
    }
  }else {
    next();
  }
})


export default router
