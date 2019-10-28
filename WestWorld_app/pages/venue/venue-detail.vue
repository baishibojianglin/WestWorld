<template>
	<view>
		<view class="banner">
			<image class="banner-img" :src="banner.thumb"></image>
			<view class="banner-title">{{banner.venue_name}}</view>
		</view>
		<view class="article-meta">
			<text class="venue-phone" @click="callPhone(banner.venue_phone)"><text class="uni-icon uni-icon-phone"></text>{{banner.venue_phone}}</text>
			<text class="venue-address"><text class="uni-icon uni-icon-location"></text>{{banner.address}}</text>
			<text class="venue-distance">{{banner.distance}}(导航)<text class="uni-icon uni-icon-map" @click="openLocation()"></text></text>
		</view>
		<view class="article-content">
			<rich-text :nodes="htmlNodes"></rich-text>
		</view>
		<!-- #ifdef MP-WEIXIN || MP-QQ -->
		<ad v-if="htmlNodes.length > 0" unit-id="adunit-01b7a010bf53d74e"></ad>
		<!-- #endif -->
	</view>
</template>

<script>
	import htmlParser from '@/common/html-parser';
	
	export default {
		data() {
			return {
				banner: {},
				htmlNodes: []
			}
		},
		onLoad(event) {
			console.log(event)
			// TODO 后面把参数名替换成 payload
			const payload = event.detailData || event.payload;
			// 目前在某些平台参数会被主动 decode，暂时这样处理。
			try {
				this.banner = JSON.parse(decodeURIComponent(payload));
			} catch (error) {
				this.banner = JSON.parse(payload);
			}
			uni.setNavigationBarTitle({
				title: this.banner.venue_name
			});
			this.getDetail();
		},
		methods: {
			/**
			 * 获取场馆详情数据
			 */
			getDetail() {
				uni.request({
					url: 'https://unidemo.dcloud.net.cn/api/news/36kr/' + this.banner.post_id,
					success: (data) => {
						if (data.statusCode == 200) {
							var htmlString = data.data.content.replace(/\\/g, "").replace(/<img/g, "<img style=\"display:none;\"");
							this.htmlNodes = htmlParser(htmlString);
						}
					},
					fail: () => {
						console.log('fail');
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
			openLocation() {alert('查看位置')
				// 使用应用内置地图查看位置
				uni.getLocation({
				    type: 'gcj02', //返回可以用于uni.openLocation的经纬度
				    success: function (res) {
				        const latitude = res.latitude;
				        const longitude = res.longitude;
				        uni.openLocation({
				            latitude: latitude,
				            longitude: longitude,
				            success: function () {
				                console.log('success');
				            }
				        });
				    }
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
	.venue-distance {
		font-size: 30upx;
	}

	.article-content {
		padding: 0 30upx;
		overflow: hidden;
		font-size: 30upx;
		margin-bottom: 30upx;
	}
</style>
