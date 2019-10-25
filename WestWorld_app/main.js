import Vue from 'vue'
import App from './App'

import pageHead from './components/page-head.vue' // 导入pageHead
import './static/icon/iconfont.css'   // 导入外部字体图标
import './static/css/main.css'        // 导入基本样式
import store from './store' // 导入Vuex.Store

Vue.config.productionTip = false
Vue.prototype.$serverUrl = 'http://westworld.com/index.php/api/v1/'
Vue.prototype.$imgServerUrl = 'http://westworld.com/'
Vue.prototype.$store = store

Vue.component('page-head', pageHead) // 注册pageHead

App.mpType = 'app'

const app = new Vue({
	store,
    ...App
})
app.$mount()
