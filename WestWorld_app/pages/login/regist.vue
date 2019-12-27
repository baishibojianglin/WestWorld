<template>
	<view class="content">
		<image class="logo" src="/static/logo.png"></image>
		<view class="text-area">
			<text class="iconfont iconyonghu"></text>
            <input  v-model="username" placeholder="手机号码" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="password" placeholder="输入密码" />
			<!-- <view class="uni-form-item uni-column">
				<view class="title">可查看密码的输入框</view>
				<view class="with-fun">
					<input class="uni-input" placeholder="请输入密码" :password="showPassword" />
					<view class="uni-icon uni-icon-eye" :class="[!showPassword ? 'uni-active' : '']" @click="changePassword"></view>
				</view>
			</view> -->
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="repassword" placeholder="确认密码" />
		</view>
		<view class="text-area uni-flex uni-row">
			<view class="flex-item">
				<text class="iconfont iconmima"></text>
				<input v-model="notecode" placeholder="短信验证码" />
			</view>
			<view class="flex-item getnotecode">获取验证码</view>
		</view>
		<button class="buttonwidth white" v-on:click="register()">注 册</button>
		<view class="loginbottom">
			<navigator url="./login">已注册？去登录</navigator>
		</view>
		<uni-popup class="popupstyle" ref="popup" type="center">{{content}}</uni-popup>
	</view>
</template>

<script>
	import uniPopup from "@/components/uni-popup/uni-popup.vue"; //引入弹窗组件
	import common from '../../common/common.js';

	export default {
		data() {
			return {
				username: '18765432101',
				password: 'abc123',
				repassword: '',
				notecode: '',
				content: '',
				
				showPassword: true,
			}
		},
		components: {uniPopup},
		methods: {
			/**
			 * 提交注册信息
			 */
			register(){
				var self=this;
				
				// 客户端对表单验证
				// 手机号
				if (!this.username.match(/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/)) {
					uni.showToast({
						icon: 'none',
						title: '手机号不合法'
					});
				    return;
				}
				// 密码
				if (!this.password.match(/^[a-zA-Z]\w{5,17}$/)) {
				    uni.showToast({
						icon: 'none',
						title: '以字母开头，长度在6~18之间，只能包含字母、数字和下划线'
					});
				    return;
				}
				// 确认两次密码一致性
				if (this.repassword != this.password) {
					uni.showToast({
						icon: 'none',
						title: '两次输入密码不一致'
					});
					return;
				}

				// 发起网络请求
				uni.request({
					url: this.$serverUrl + 'register',
					data: {
						phone: this.username,
						password: this.password,
						repassword: this.repassword,
						code: this.notecode,
					},
					header: {
						'content-type': 'application/x-www-form-urlencoded',
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					method: 'POST',
					success:function(res){
						// console.log('register success', res);
						if(0 == res.data.status){ // 验证失败
							/* self.content = res.data.message;
							self.openPopup(); */
							uni.showToast({
							    icon: 'none',
							    title: res.data.message
							});
							return;
						}else{ // 验证成功跳转
							uni.navigateTo({
								url: '../login/login'
							});
						}
					}
				})
			},

			//弹窗
			openPopup(){
				this.$refs.popup.open()
			},
			
			/**
			 * 查看密码
			 */
			changePassword: function() {
				this.showPassword = !this.showPassword;
			},
		}
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		color: $uni-color-error;
	}
	.logo{
		height: 200upx;
		width: 200upx;
		margin: 100upx auto 50upx auto;
	}
	.text-area {
		position: relative;
		width: 80%;
		justify-content: center;
	    border-bottom: 1px solid #8F8F94;
	    margin-top: 50upx;
		line-height: 50upx;
	}
    .text-area input{
		width: 100%;
		margin-bottom: 20upx;
		font-size: 28upx;
		text-align: center;
	}
	.text-area text{
		position: absolute;
		left: 15upx;
		bottom: 15upx;
		font-size: 28upx;
		text-align: center;
	}
	.getnotecode{
		position: absolute;
		right: 15upx;
		bottom: 15upx;
		font-weight: bold;
		color: gray;
	}
	.buttonwidth{
		width: 80%;
		margin-top: 50upx;
		font-size: 28upx;
		text-align: center;
		background-color: #2B9939;
	}
	.loginbottom{
		margin-top: 30upx;
	}
	.loginbottom navigator{
		font-size: 24upx;
		color: gray;
	}
	.popupstyle{
		font-size: 24upx;
	}
</style>
