import Vue from 'vue'
import App from './App'
import './static/icon/iconfont.css'   //引入外部字体图标
import './static/css/main.css'        //引入基本样式
import store from './store' //引入Vuex.Store

Vue.config.productionTip = false
Vue.prototype.$serverUrl = 'http://westworld.com/index.php'
Vue.prototype.$imgServerUrl = 'http://westworld.com/'
Vue.prototype.$store = store

App.mpType = 'app'

const app = new Vue({
	store,
    ...App
})
app.$mount()
