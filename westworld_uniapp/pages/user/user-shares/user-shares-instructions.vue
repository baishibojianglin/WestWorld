<template>
	<view>
		<uni-card :is-shadow="true">
			<view class="article-content">
				<rich-text :nodes="htmlNodes"></rich-text>
			</view>
		</uni-card>
	</view>
</template>

<script>
	import {uniCard} from '@dcloudio/uni-ui';
	import {mapState} from 'vuex';
	import common from '@/common/common.js';
	
	export default {
		components: {uniCard},
		data() {
			return {
				htmlNodes: [], // 用户红利说明
			}
		},
		onLoad(event) {
			this.getUserSharesInstructions();
		},
		computed: mapState(['userInfo']), // 对全局变量 userInfo 进行监控
		methods: {
			/**
			 * 获取用户红利说明
			 */
			getUserSharesInstructions() {
				uni.request({
					url: this.$serverUrl + 'system_config/17',
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					success: (res) => {
						// console.log(res.data.data.value);
						this.htmlNodes = res.data.data.value;
					}
				})
			}
		}
	}
</script>

<style>

</style>
