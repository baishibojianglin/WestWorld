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
			<navigator url="../pwd/pwd">忘记密码？</navigator>
		</view>
	</view>
</template>

<script>
	import common from '../../common/common.js';
	import {mapState, mapMutations} from 'vuex'; // 导入 vuex 的 mapState 和 mapMutations 方法
	
	export default {
		data() {
			return {
				username: '18765432101',
				password: 'abc123'
			}
		},
		computed: {
			...mapState(['hasLogin']), // 对全局变量 hasLogin 进行监控
		},
		methods: {
			...mapMutations(['login']), // 对全局方法 login 进行监控
			
			/**
			 * 登录
			 */
			bindLogin() {
				let self = this;
				
			    /**
			     * 客户端对账号信息进行一些必要的校验。
			     */
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
				
			    /**
				 * 使用 uni.request 将账号信息发送至服务端，客户端在回调函数中获取结果信息。
			     */
				uni.request({
					url: this.$serverUrl + '/api/v1/login', // '/dpc/api/v1/login'
					data: {
						phone: this.username,
						password: this.password,
					},
					header: {
						'sign': common.sign(), // 签名，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': 1, // APP大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': '12345dg', // 设备号
					},
					method: 'PUT',
					success: function(res){
						// console.log('login success', res);
						if (1 == res.data.status) {
							// self.toMain(res.data.data);
							self.login(res.data.data);
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
			},
			
			/**
			 * 定义登录成功后跳转到首页的函数
			 * @param {Object} provider
			 */
			toMain(provider){
				this.login(provider);
				uni.reLaunch({
					url: '../index/index', // 跳转到首页
				});
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
