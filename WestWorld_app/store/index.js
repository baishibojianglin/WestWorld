import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
	state: { // 全局变量定义处
		// forcedLogin: false, // 是否需要强制登录
		hasLogin: false, // 是否登录
		userInfo: {} // 存放用户账号数据
	},
	mutations: { // 全局方法定义处
		/**
		 * 登录
		 * @param {Object} state
		 * @param {Object} provider
		 */
		login(state, provider) {
		    state.hasLogin = true;
			state.userInfo = provider; // 将请求中如res.data.data对象存入userInfo
			// state.userInfo.token = provider.token
			// state.userInfo.userName = provider.user_name
			uni.setStorage({ // 将用户信息保存到本地缓存
				key: 'userInfo',
				data: provider
			})
			console.log('setStorage userInfo', state.userInfo);
		},
		
		/**
		 * 退出登录
		 * @param {Object} state
		 */
		logout(state) {
		    state.hasLogin = false;
			state.userInfo = {};
			uni.removeStorage({
				key: 'userInfo', // 根据键名移除对应位置的缓存数据
			})
		}
	}
})

export default store
