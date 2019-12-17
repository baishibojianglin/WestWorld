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
		<button class="buttonwidth white" @click="bindLogin">登 录</button>
		<view class="loginbottom">
			<navigator url="./regist">注册用户</navigator>
			<!-- <text>|</text> -->
			<navigator url="../pwd/pwd">忘记密码？</navigator>
		</view>
		
		<view class="uni-btn-v uni-common-mt">
			<!-- #ifndef H5 -->
			<button type="primary" class="page-body-button" v-for="(value,key) in providerList" @click="tologin(value)" :key="key">{{value.name}}</button>
			<!-- #endif -->
		</view>
	</view>
</template>

<script>
	import common from '../../common/common.js';
	import {mapState, mapMutations} from 'vuex'; // 导入 vuex 的 mapState 和 mapMutations 方法
	
	export default {
		data() {
			return {
				username: '18765432101',
				password: 'abc123',
				providerList: []
			}
		},
		computed: {
			...mapState(['hasLogin']), // 对全局变量 hasLogin 进行监控
		},
		onLoad() {
			uni.getProvider({
				service: 'oauth',
				success: (result) => {
					this.providerList = result.provider.map((value) => {
						let providerName = '';
						switch (value) {
							case 'weixin':
								providerName = '微信登录'
								break;
							case 'qq':
								providerName = 'QQ登录'
								break;
							case 'sinaweibo':
								providerName = '新浪微博登录'
								break;
							case 'xiaomi':
								providerName = '小米登录'
								break;
							case 'alipay':
								providerName = '支付宝登录'
								break;
							case 'baidu':
								providerName = '百度登录'
								break;
							case 'toutiao':
								providerName = '头条登录'
								break;
						}
						return {
							name: providerName,
							id: value
						}
					});
		
				},
				fail: (error) => {
					console.log('获取登录通道失败', error);
				}
			});
		},
		methods: {
			...mapMutations(['login']), // 对全局方法 login 进行监控
			
			/**
			 * 账号密码登录
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
					url: this.$serverUrl + 'login', // '/dpc/api/v1/login'
					data: {
						phone: this.username,
						password: this.password,
					},
					header: {
						'sign': common.sign(), // 验签，TODO：对参数如did等进行AES加密，生成sign如：'6IpZZyb4DOmjTaPBGZtufjnSS4HScjAhL49NFjE6AJyVdsVtoHEoIXUsjrwu6m+o'
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
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
			},
			
			/**
			 * 第三方登录
			 * @param {Object} provider
			 */
			tologin(provider) {
				uni.login({
					provider: provider.id,
					success: (res) => {
						console.log('login success:', res);
						// 更新保存在 store 中的登录状态
						this.login(provider.id);
						
						// 获取用户信息
						uni.getUserInfo({
							provider: provider.id,
							success: function (infoRes) {
								console.log('用户昵称为：' + infoRes.userInfo.nickName);
							},
							fail: (err) => {
								console.log('getUserInfo fail:', err);
							}
						});
					},
					fail: (err) => {
						console.log('login fail:', err);
					}
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
		color: gray;/* #007aff */
		padding: 0 20upx;
	}
</style>
