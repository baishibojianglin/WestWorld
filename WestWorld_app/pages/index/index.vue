<template>
	<view class="page">
		<view v-if="!hasLogin">现在是未登录状态，点击按钮进行登录</view>
		<view v-else>现在是登录状态，您的用户token是：{{userInfo.token}}</view>
		<button type="primary" @click="bindLogin">{{hasLogin ? '退出登录' : '登录'}}</button>
	</view>
</template>
<script>
	import {mapState, mapMutations} from 'vuex';
	
	export default {
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			...mapMutations(['logout']), // 对全局方法 logout 进行监控
			bindLogin() {
				if (this.hasLogin) {
					this.logout()
				} else {
					uni.navigateTo({
						url: '/pages/login/login'
					})
				}
			}
		}
	}
</script>

<style>
	.page{
		padding: 50upx 30upx;
	}
	view{
		line-height: 1.5;
		font-size: 32upx;
	}
	button{
		margin-top: 30upx;
		height: 80upx;
		line-height: 80upx;
	}
</style>
