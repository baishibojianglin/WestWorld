<template>
	<view class="page">
		<view class="uni-padding-wrap uni-common-mt">
			<view class="uni-flex uni-row" v-if="hasLogin">
				<view class="text uni-flex" style="width: 200upx;height: 220upx;-webkit-justify-content: center;justify-content: center;-webkit-align-items: center;align-items: center;">
					<image :src="userData.avatar" style="width: 200upx;height: 200upx;"></image>
				</view>
				<view class="uni-flex uni-column" style="-webkit-flex: 1;flex: 1;-webkit-justify-content: space-between;justify-content: space-between;">
					<view class="text" style="height: 120upx;text-align: left;padding-left: 20upx;padding-top: 10upx;">
						<view>{{userData.user_name}}</view>
						<view style="font-size: 20upx;margin-top: 10upx">{{userData.signature}}</view>
					</view>
					<view class="uni-flex uni-row">
						<view class="text" style="-webkit-flex: 1;flex: 1;">{{userData.phone}}</view>
						<view class="text" style="-webkit-flex: 1;flex: 1;">{{userData.email}}</view>
					</view>
				</view>
			</view>
			
			<!-- <view v-if="!hasLogin">现在是未登录状态，点击按钮进行登录</view>
			<view v-else>现在是登录状态，您的用户token是：{{userInfo.token}}</view> -->
			<button type="default" @click="bindLogin">{{hasLogin ? '退出登录' : '登录'}}</button>
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	import common from '../../common/common.js';
	import Aes from '../../common/Aes.js';
	
	export default {
		data() {
			return {
				userData: {}
			}
		},
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
		},
		onLoad:function(){
			let self = this
			
			if (this.hasLogin) {
				uni.request({
					url: this.$serverUrl + '/api/v1/user/1',
					header: {
						'sign': common.sign(), // 签名，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': '12345dg', // 设备号
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success:function(res){
						let userData = JSON.parse(Aes.decode(res.data.data));
						userData.avatar = userData.avatar ? self.$imgServerUrl + userData.avatar.replace(/\\/g, "/") : '../../static/img/user.png'; //用户头像
						self.userData = userData;
					}
				})
			}
		}
	}
</script>

<style>
	.text {
		margin: 15upx 10upx;
		padding: 0 20upx;
		background-color: #ebebeb;
		height: 70upx;
		line-height: 70upx;
		text-align: center;
		color: #777;
		font-size: 26upx;
	}
	
	/* .page{
		padding: 50upx 30upx;
	}
	view{
		line-height: 1.5;
		font-size: 32upx;
	} */
	button{
		margin-top: 30upx;
		height: 80upx;
		line-height: 80upx;
	}
</style>
