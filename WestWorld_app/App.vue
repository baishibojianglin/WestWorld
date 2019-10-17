<script>
	export default {
		// 全局变量
		globalData: {
			systemInfo: '', // 设备系统信息
		},
		onLaunch: function() {
			console.log('App Launch')

			// 获取设备系统信息
			uni.getSystemInfo({
			    success: function (res) {
					// console.log(res)
					getApp().globalData.systemInfo = res
				},
			})
		},
		onShow: function() {
			console.log('App Show')
		},
		onHide: function() {
			console.log('App Hide')
		}
	}
</script>

<style>
	/*每个页面公共css */
</style>
