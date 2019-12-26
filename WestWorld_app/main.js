import Vue from 'vue'
import App from './App'

import store from './store' // 导入Vuex.Store
import './static/icon/iconfont.css'   // 导入外部字体图标
import './static/css/main.css'        // 导入基本样式

// 全局导入和注册 page-head 组件
import pageHead from './components/page-head.vue' // 导入pageHead
Vue.component('page-head', pageHead) // 注册pageHead
// 全局注入 w-loading 组件
import wLoading from "@/components/w-loading/w-loading.vue"
Vue.component('w-loading', wLoading)

Vue.config.productionTip = false
Vue.prototype.$serverUrl = 'http://westworld.com/index.php/api/v1/'
Vue.prototype.$imgServerUrl = 'http://westworld.com/'
Vue.prototype.$store = store

App.mpType = 'app'

const app = new Vue({
	store,
    ...App
})
app.$mount()
