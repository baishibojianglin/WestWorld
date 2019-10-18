<template>
	<view class="content">
		<image class="logo" src="/static/logo.png"></image>
		<view class="text-area">
			<text class="iconfont iconyonghu"></text>
            <input type="text" v-model="username" placeholder="手机号" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input type="password" v-model="password" placeholder="密码" />
		</view>
		<button class="buttonwidth white" @tap="bindLogin">登 录</button>
		<view class="loginbottom">
			<navigator url="./regist">注册用户</navigator>
			<text>|</text>
			<navigator url="./regist">忘记密码？</navigator>
		</view>
	</view>
</template>

<script>
	import common from '../../common/common.js';

	/* 生成签名 sign s */
	let get13Timestamp = (new Date()).getTime(); // 获取13位的时间戳
	let tempJson = {"did": "12345dg", "version": 1, 'time': get13Timestamp}; // 注意：此处为json对象，不是json字符串
	let sign = common.setSign(tempJson); console.log('sign', sign)
	/* 生成签名 sign e */

	export default {
		data() {
			return {
				username: '18235235456',
				password: '123456'
			}
		},
		methods: {
			/**
			 * 登录
			 */
			bindLogin() {
			    /**
			     * 客户端对账号信息进行一些必要的校验。
			     */
			    if (this.username.length < 5) {
			        uni.showToast({
			            icon: 'none',
			            title: '账号最短为 5 个字符'
			        });
			        return;
			    }
			    if (this.password.length < 6) {
			        uni.showToast({
			            icon: 'none',
			            title: '密码最短为 6 个字符'
			        });
			        return;
			    }
				
			    /**
				 * 使用 uni.request 将账号信息发送至服务端，客户端在回调函数中获取结果信息。
			     */
				uni.request({
					url: this.$url + '/api/v1/login', // '/dpc/api/v1/login'
					data: {
						phone: this.username,
						password: this.password,
					},
					header: {
						'sign': sign, // 签名，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': 1, // APP大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': '12345dg', // 设备号
					},
					method: 'POST',
					success: function(res){
						console.log(res);
						if (1 == res.data.status) {
						    // this.toMain(this.username);
							uni.reLaunch({
							    url: '../index/index', // 跳转到首页
							});
						} else {
						    uni.showToast({
						        icon: 'none',
						        title: res.data.message, // '用户账号或密码不正确'
						    });
						}
					},
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
	.logo {
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
</style>
