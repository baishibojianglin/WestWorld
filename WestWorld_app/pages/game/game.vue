<template>
	<view>
		<view class="uni-padding-wrap uni-common-mt">
			<button class="uni-common-mt" type="default" @click="toSessionOrder">查看比赛</button>
			<button class="uni-common-mt" type="default" @click="toVenue">加入比赛</button>
		</view>
	</view>
</template>

<script>
	import {mapState} from 'vuex';
	
	export default {
		data() {
			return {
				
			}
		},
		computed: mapState(['hasLogin']), // 对全局变量 hasLogin 进行监控
		methods: {
			/**
			 * 查看比赛
			 */
			toSessionOrder() {
				// 判断是否登录
				if (false == this.hasLogin) {
					uni.showModal({
						title: '登录',
						content: '请登录账号',
						success: function (res) {
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/login/login'
								});
							}
						}
					});
					return false;
				}
				
				uni.navigateTo({
					url: '/pages/session-order/session-order'
				})
			},
			
			/**
			 * 加入比赛
			 */
			toVenue() {
				uni.navigateTo({
					url: '/pages/venue/venue'
				})
			}
		}
	}
</script>

<style>

</style>
