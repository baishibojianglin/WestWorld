import Vue from 'vue'
import App from './App'
import './static/icon/iconfont.css'   //引入外部字体图标
import './static/css/main.css'        //引入基本样式

Vue.config.productionTip = false
Vue.prototype.$url = 'http://westworld.com/index.php';

App.mpType = 'app'
const app = new Vue({
    ...App
})
app.$mount()

