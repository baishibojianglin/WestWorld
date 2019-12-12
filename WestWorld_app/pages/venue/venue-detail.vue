<template>
	<view>
		<view class="banner">
			<image class="banner-img" :src="banner.thumb"></image>
			<view class="banner-title">{{banner.venue_name}}</view>
		</view>
		<view class="article-meta">
			<text class="venue-phone" @click="callPhone(banner.venue_phone)"><text class="uni-icon uni-icon-phone"></text>{{banner.venue_phone}}</text>
			<text class="venue-address"><text class="uni-icon uni-icon-location"></text>{{banner.address}}</text>
			<text class="venue-location"><text class="uni-icon uni-icon-map uni-bg-red" @click="openLocation()"></text></text>
		</view>
		
		<view class="uni-padding-wrap uni-common-mt uni-common-mb">
			<button type="warn" @click="toGame()">加入比赛</button>
		</view>
		
		<view class="article-content">
			<page-head :title="contentTitle"></page-head>
			<rich-text :nodes="htmlNodes"></rich-text>
		</view>
		<!-- #ifdef MP-WEIXIN || MP-QQ -->
		<ad v-if="htmlNodes.length > 0" unit-id="adunit-01b7a010bf53d74e"></ad>
		<!-- #endif -->
	</view>
</template>

<script>
	import htmlParser from '@/common/html-parser';
	import {mapState} from 'vuex';
	import common from '../../common/common.js';
	
	export default {
		data() {
			return {
				banner: {},
				htmlNodes: [],
				contentTitle: '场馆介绍'
			}
		},
		onLoad(event) {
			// TODO 后面把参数名替换成 payload
			const payload = event.detailData || event.payload;
			// 目前在某些平台参数会被主动 decode，暂时这样处理。
			try {
				this.banner = JSON.parse(decodeURIComponent(payload));
			} catch (error) {
				this.banner = JSON.parse(payload);
			}
			// 动态设置当前页面的标题
			uni.setNavigationBarTitle({
				title: this.banner.venue_name
			});
			this.getDetail();
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			/**
			 * 获取场馆详情数据
			 */
			getDetail() {
				uni.request({
					url: this.$serverUrl + 'venue/' + this.banner.venue_id,
					header: {
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					success: (data) => {
						if (data.statusCode == 200) {
							// 场馆信息
							let venueData = data.data.data;
							this.banner = venueData;
							
							// 场馆缩略图
							this.banner.thumb = venueData.thumb ? this.$imgServerUrl + venueData.thumb.replace(/\\/g, "/") : '../../static/img/home.png';
							
							// 场馆介绍
							var htmlString = venueData.venue_description.replace(/\\/g, "").replace(/<img/g, "<img style=\"display:none;\"");
							this.htmlNodes = htmlParser(htmlString);
						}
					},
					fail: (data) => {
						console.log('fail', data);
					}
				});
			},
			/**
			 * 拨打电话
			 * @param {Object} phone
			 */
			callPhone(phone){
				uni.makePhoneCall({
					phoneNumber: phone 
				});
			},
			/**
			 * 查看位置
			 */
			openLocation() {
				// 使用应用内置地图查看位置
				uni.openLocation({
				    latitude: Number(this.banner.latitude),
				    longitude: Number(this.banner.longitude),
				    success: function () {
				        console.log('success');
				    }
				});
			},
			/**
			 * 跳转比赛选择页
			 */
			toGame() {
				// 判断是否登录
				if (false == this.hasLogin) {
					uni.showModal({
						title: '登录',
						content: '请登录账号',
						success: function (res) {
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/login/login'
								});
							}
						}
					});
					return false;
				}
				
				// 跳转比赛选择页
				uni.navigateTo({
				    url: '/pages/game/join-steps?id=' + this.banner.venue_id + '&venueData=' + JSON.stringify(this.banner)
				});
			}
		}
	}
</script>

<style>
	.banner {
		height: 360upx;
		overflow: hidden;
		position: relative;
		background-color: #ccc;
	}

	.banner-img {
		width: 100%;
	}

	.banner-title {
		max-height: 84upx;
		overflow: hidden;
		position: absolute;
		left: 30upx;
		bottom: 30upx;
		width: 90%;
		font-size: 32upx;
		font-weight: 400;
		line-height: 42upx;
		color: white;
		z-index: 11;
	}

	.article-meta {
		padding: 20upx 40upx;
		display: flex;
		flex-direction: row;
		justify-content: flex-start;
		color: gray;
	}

	.venue-address {
		font-size: 26upx;
		line-height: 50upx;
		margin: 0 20upx;
	}

	.venue-phone,
	.venue-location {
		font-size: 30upx;
	}

	.article-content {
		padding: 0 30upx;
		overflow: hidden;
		font-size: 30upx;
		margin-bottom: 30upx;
	}
</style>
