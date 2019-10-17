<template>
	<view class="content">
		<image class="logo" src="/static/logo.png"></image>
		<view class="text-area">
			<text class="iconfont iconyonghu"></text>
            <input type="text" v-model="username" placeholder="请输入账号" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input type="password" v-model="password" placeholder="请输入密码" />
		</view>
		<button class="buttonwidth white" @tap="bindLogin">登 录</button>
		<view class="loginbottom">
			<navigator url="./regist">未注册？去注册</navigator>
		</view>
	</view>
</template>

<script>
	import common from '../../common/common.js';
	import Aes from '../../common/Aes.js';

	// 生成签名 sign
	let get13Timestamp = (new Date()).getTime(); // 获取13位的时间戳, 'time': get13Timestamp
	let tempJson = '{"did":"12345dg","version":1}'; // 注意：不要有空格
	let encode = Aes.encode(tempJson);
	let sign = common.setSign(tempJson);
	console.log('sign', sign)

	export default {
		data() {
			return {
				username: '18235235456',
				password: ''
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
					url: '/dpc/api/v1/login', // this.$url+'/api/v1/login'
					data: {
						phone: this.username,
						password: this.password,
					},
					header: {
						'content-type': "application/json; charset=utf-8",
						'sign': sign, // 签名，TODO：对参数如did进行AES加密 '6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						// 'version': 1, // APP大版本号
						// 'model': getApp().globalData.systemInfo.model, // 手机型号
						// 'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						// 'did': '12345dg', // 设备号
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
	}
	.loginbottom navigator{
		font-size: 24upx;
	}
</style>
