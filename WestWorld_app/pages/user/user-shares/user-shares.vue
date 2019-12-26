<template>
	<view>
		<uni-card title="用户红利" thumbnail="" extra="" note="Tips" :is-shadow="true">
			<view><text class="red uni-bold">{{ user_shares }}</text></view>
			<template v-slot:footer>
				<view class="footer-box">
					<view @click.stop="footerClick('说明')">说明</view>
				</view>
			</template>
		</uni-card>
	</view>
</template>

<script>
	import {uniCard} from '@dcloudio/uni-ui';
	
	export default {
		components: {uniCard},
		data() {
			return {
				user_shares: 0, // 用户红利
			}
		},
		onLoad(event) {
			this.user_shares = event.user_shares;
		},
		methods: {
			footerClick(types) {
				let content = '模态弹窗内容';
				uni.showModal({
					title: '用户红利说明',
					content: content,
					showCancel: false,
					/* success: function (res) {
						if (res.confirm) {
							console.log('用户点击确定');
						} else if (res.cancel) {
							console.log('用户点击取消');
						}
					} */
				})
				
				if (types == "说明") {
					uni.navigateTo({
						url: '/pages/user/user-shares/user-shares-instructions'
					})
				}
			}
		}
	}
</script>

<style>

</style>
