/**
 * Created by Administrator on 2017/8/8 0008.
 */
/**
 * Created by superman on 17/2/16.
 */
import Vuex from 'vuex'
import Vue from 'vue'
import * as types from './types'

Vue.use(Vuex);
export default new Vuex.Store({
  state: {
    user: {},
    token: null,
    title: ''
  },
  getters: {
    getUser: state => {
      return state.user
    }
  },
  mutations: {
    [types.LOGIN]: (state, data) => {
      sessionStorage.token = data;
      state.token = data;
    },
    [types.LOGOUT]: (state) => {
      sessionStorage.removeItem('token');
      state.token = null
    },
    [types.TITLE]: (state, data) => {
      state.title = data;
    },
    [types.USER]: (state, data) => {
      state.user = data
    }
  }
})
