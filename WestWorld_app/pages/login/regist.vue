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
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="repassword" placeholder="确认密码" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
			<input v-model="notecode" placeholder="短信验证码" />
		</view>
		<view class="text-area">
			<text class="getnotecode">获取验证码</text>
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
	import common from '../../common/common.js';

	export default {
		data() {
			return {
				username: '18765432101',
				password: '123456',
				repassword: '',
				notecode: '',
				content: ''
			}
		},
		components: {uniPopup},
		methods: {
			/**
			 * 提交注册信息
			 */
			register(){
				var self=this;
				
				// 客户端对账号信息进行校验
				if (this.repassword != this.password) {
					uni.showToast({
						icon: 'none',
						title: '两次输入密码不一致'
					});
					return;
				}
				
				// 发起网络请求
				uni.request({
					url: this.$url + '/api/v1/register',
					data: {
						phone: this.username,
						password: this.password,
						repassword: this.repassword,
						code: this.notecode,
					},
					header: {
						'content-type': 'application/x-www-form-urlencoded',
						'sign': common.sign(), // 签名
						'version': 1, // APP大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': '12345dg', // 设备号
					},
					method: 'POST',
					success:function(res){
						console.log(res);
						if(0 == res.data.status){ // 验证失败
							/* self.content = res.data.message;
							self.openPopup(); */
							uni.showToast({
							    icon: 'none',
							    title: res.data.message
							});
							// return;
						}else{ // 验证成功跳转
							uni.navigateTo({
								url: '../index/index'
							});
						}

					}
				})
			},

			//弹窗
			openPopup(){
				this.$refs.popup.open()
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
	}
	.loginbottom navigator{
		font-size: 24upx;
	}
	.popupstyle{
		font-size: 24upx;
	}
	.getnotecode{
		right: -300upx;
	}
</style>
