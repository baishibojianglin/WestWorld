<template>
	<view class="uni-container">
		<view class="uni-panel" v-if="hasLogin">
			<view class="uni-panel-h">
				<view class="uni-flex uni-row userData">
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
							<view class="text" style="-webkit-flex: 1;flex: 1;">{{userData.create_time}}</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		
		<view class="uni-panel" v-for="(item, index) in list" :key="item.id" v-if="hasLogin">
		    <view class="uni-panel-h" :class="item.open ? 'uni-panel-h-on' : ''" @click="triggerCollapse(index)">
		        <text class="uni-panel-text">{{item.name}}</text>
		        <text class="uni-panel-icon uni-icon" :class="item.open ? 'uni-panel-icon-on' : ''">{{item.pages ? '&#xe581;' : '&#xe470;'}}</text>
		    </view>
		    <view class="uni-panel-c" v-if="item.open">
		        <view class="uni-navigate-item" v-for="(item2,key) in item.pages" :key="key" @click="goDetailPage(item2)">
		            <text class="uni-navigate-text">{{item2.name ? item2.name : item2}}</text>
		            <text class="uni-navigate-icon uni-icon">&#xe470;</text>
		        </view>
		    </view>
		</view>
		
		<view class="uni-panel">
			<!-- <view v-if="!hasLogin">现在是未登录状态，点击按钮进行登录</view>
			<view v-else>现在是登录状态，您的用户token是：{{userInfo.token}}</view> -->
			<view class="uni-btn-v uni-common-mt">
				<button type="default" @click="bindLogin">{{hasLogin ? '退出登录' : '登录'}}</button>
			</view>
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
				userData: {},
				list: [
					{
						name: '积分',
						open: true,
						pages: [{
							name: '已兑换积分',
							url: 'lazy-load'
						}, {
							name: '待兑换积分',
							url: 'lazy-load-custom'
						}],
						url: 'mpvue-picker'
					},
					{
						name: '历史比赛',
						url: 'mpvue-picker'
					},
					{
						name: '待参与活动',
						url: 'mpvue-picker'
					},
					{
						name: '个人红利',
						url: 'mpvue-picker'
					}
				]
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
			},
			triggerCollapse(e) {
				if (!this.list[e].pages) {
					this.goDetailPage(this.list[e].url);
					return;
				}
				for (var i = 0; i < this.list.length; ++i) {
					if (e === i) {
						this.list[i].open = !this.list[e].open;
					} else {
						this.list[i].open = false;
					}
				}
			},
			goDetailPage(e) {
			    if (this.navigateFlag) {
			        return;
			    }
			    this.navigateFlag = true;
				let path = e.url ? e.url : e;
				let url = ~path.indexOf('platform') ? path : '/pages/template/' + path + '/' + path;
				uni.navigateTo({
					url: url
				});
			    setTimeout(() => {
			        this.navigateFlag = false;
			    }, 200)
				return false;
			}
		},
		onLoad:function(){
			// 获取用户信息
			let self = this
			if (this.hasLogin) {
				uni.request({
					url: this.$serverUrl + 'user/1',
					header: {
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success:function(res){
						let userData = JSON.parse(Aes.decode(res.data.data)); // 用户信息
						userData.avatar = userData.avatar ? self.$imgServerUrl + userData.avatar.replace(/\\/g, "/") : '../../static/img/user.png'; // 用户头像
						
						// 用户创建时间
						let create_time = new Date(userData.create_time);
						userData.create_time = create_time.getFullYear() + '.' + create_time.getMonth() + '.' + create_time.getDate();
						
						self.userData = userData;
					}
				})
			}
		}
	}
</script>

<style>
	@import '../../common/uni-nvue.css';
	
	.userData .text {
		margin: 10upx 5upx;
		padding: 0 20upx;
		/* background-color: #ebebeb; */
		height: 70upx;
		line-height: 70upx;
		text-align: center;
		color: #777;
		font-size: 26upx;
	}
</style>
