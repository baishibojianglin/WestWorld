<template>
	<view class="content">
		<image class="logo" src="/static/logo.png"></image>
		<view class="text-area">
			<text class="iconfont iconyonghu"></text>
            <input  v-model="username" placeholder="手机号码" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
			<input v-model="notecode" placeholder="短信验证码" />
		</view>
		<view class="text-area">
			<text class="getnotecode">获取验证码</text>
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="password" placeholder="输入新密码" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="repassword" placeholder="确认新密码" />
		</view>
		<button class="buttonwidth white" v-on:click="register()">确 认</button>
		<view class="loginbottom">
			<navigator url="../login/regist">注册</navigator>
			<text>|</text>
			<navigator url="../login/login">登录</navigator>
		</view>
	</view>
</template>

<script>
	import common from '../../common/common.js';

	export default {
		data() {
			return {
				username: '18765432101',
				password: 'abc123',
				repassword: '',
				notecode: '',
				content: ''
			}
		},
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
						title: '手机号码不合法'
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
					url: this.$serverUrl + '/api/v1/pwd',
					data: {
						phone: this.username,
						password: this.password,
						repassword: this.repassword,
						code: this.notecode,
					},
					header: {
						'content-type': 'application/x-www-form-urlencoded',
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': '12345dg', // 设备号
					},
					method: 'PUT',
					success:function(res){
						if(0 == res.data.status){ // 验证失败
							uni.showToast({
							    icon: 'none',
							    title: res.data.message
							});
							// return;
						}else{ // 验证成功跳转
							uni.navigateTo({
								url: '../login/login'
							});
						}

					}
				})
			}
		}
	}
</script>

<style>
	.content {
		display: flex;
		flex-direction: column;
		align-items: center;
		justify-content: center;
		color:$uni-color-error;
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
		font-size: 28upx;
		text-align:center;
	}
	.text-area text{
		position: absolute;
		left:12%;
		bottom:2upx;
		font-size: 28upx;
		text-align:center;
	}
	.buttonwidth{
		width:80%;
		margin-top:50upx;
		font-size: 28upx;
		text-align:center;
		background-color:#2B9939;
	}
	.loginbottom{
		margin-top:30upx;
		display: flex;
		flex-direction: row;
		justify-content: center;
	}
	.loginbottom navigator, .loginbottom text{
		font-size: 24upx;
		color: #007aff;
		padding: 0 20upx;
	}
	.getnotecode{
		right: -300upx;
	}
</style>
