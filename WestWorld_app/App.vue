<script>
	import {mapMutations} from 'vuex';
	
	export default {
		// 全局变量
		globalData: {
			systemInfo: '', // 设备系统信息
			version: 1, // 应用大版本号
			did: '12345dg', // 设备号
		},
		
		onLaunch: function() {
			// console.log('App Launch')
			let self = this;
			
			// 获取设备系统信息
			uni.getSystemInfo({
				success: function (res) {
					self.globalData.systemInfo = res; // getApp().globalData.systemInfo = res
				}
			});
			
			// 获取缓存（异步）
			uni.getStorage({
				key: 'userInfo',
				success:(res) => {
					self.login(res.data);
					// 如果还需要重新校验或是想要刷新token的有效时间 就再联网请求一次
					/* uni.request({
						url: `${this.$serverUrl}/auth.php`,
						header: {
							"Content-Type": "application/x-www-form-urlencoded",
							"Token":res.data.token
						},
						data: {
							"username":res.data.user_name
						},
						method: "POST",
						success: (e) => {
							if (e.statusCode === 200 && e.data.code === 0) {
								this.login(e.data);
							}
						}
					}) */
				}
			});
			
			// 显示 tabBar 某一项的右上角的红点
			uni.showTabBarRedDot({
				index: 1
			});
		},
		onShow: function() {
			// console.log('App Show')
		},
		onHide: function() {
			// console.log('App Hide')
		},
		
		methods: {
			...mapMutations(['login'])
		}
	}
</script>

<style>
	/*每个页面公共css */
	
	/* uni.css - 通用组件、模板样式库，可以当作一套ui库应用 */
	@import './common/uni.css';
</style>
