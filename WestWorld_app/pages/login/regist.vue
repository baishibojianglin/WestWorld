<template>
	<view class="content">
		<image class="logo" src="/static/logo.png"></image>
		<view class="text-area">
			<text class="iconfont iconyonghu"></text>
            <input  v-model="username" placeholder="请输入账号" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="password" placeholder="请输入密码" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="password" placeholder="请确认密码" />
		</view>
		<view class="text-area">
			<text class="iconfont iconmima"></text>
		    <input password="true" v-model="notecode" placeholder="短信验证码" />
		</view>
		<button class="buttonwidth white" v-on:click="login()">注 册</button>
		<view class="loginbottom">
			<navigator url="./login">已注册？去登录</navigator>
		</view>
		<uni-popup class="popupstyle" ref="popup"  type="center">{{content}}</uni-popup>
	</view>
</template>

<script>
	import uniPopup from "@/components/uni-popup/uni-popup.vue" //引入弹窗组件
	export default {
		data() {
			return {
				username:'',
				password:'',
				content:''
			}

		},
		components: {uniPopup},
		methods: {
			//提交登录信息
			login(){
			  var self=this;
			  uni.request({
				url:'http://www.hunter.com/index.php/home/Login/login',
				method:'POST',
				header:{'content-type': 'application/x-www-form-urlencoded'},
				data:{
					username:this.username,
					password:this.password
				},
				success:function(res){
					if(res.data.status==0){
						//验证失败
						self.content=res.data.words;
						self.openPopup();						
					}else{
						//验证成功跳转
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
</style>
