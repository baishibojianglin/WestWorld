<template>
	<view class="content">
		<!-- uniapp loading加载动画 -->
		<w-loading text="" mask="true" click="false" ref="loading"></w-loading>
		
		<!-- swiper s -->
		<view class="uni-margin-wrap">
			<!-- uniSwiperDot s -->
			<uni-swiper-dot :info="adList" :current="current" :mode="mode" field="ad_name">
				<swiper class="swiper-box" @change="swiperChange" :indicator-dots="false" :autoplay="autoplay" :interval="interval" :duration="duration" circular="true">
					<swiper-item v-for="(item, index) in adList" :key="index">
						<view class="swiper-item" :style="{background: item.bgcolor + ' url(' + item.ad_pic + ')'}" @click="toAdDetail(item.ad_link)">
							<image :src="item.ad_pic" mode="aspectFill" style="width: 100%;" />
						</view>
					</swiper-item>
				</swiper>
			</uni-swiper-dot>
			<!-- uniSwiperDot e -->
		</view>
		<!-- swiper e -->
		
		<!-- 场馆列表 s -->
		<page-head :title="title"></page-head>
		
		<view class="uni-list">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in listData" :key="key" @click="toVenueDetail(value)">
				<view class="uni-media-list">
					<image class="uni-media-list-logo" :src="value.thumb"></image>
					<view class="uni-media-list-body">
						<view class="uni-media-list-text-top">
							<text class="uni-bold">{{ value.venue_name }}</text>
							<text style="float: right;">{{ value.distance }}</text>
						</view>
						<view class="uni-media-list-text-bottom">
							<text>{{ value.venue_phone }}</text>
							<text style="margin-left: 20upx; text-align: right;">{{ value.address }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="status" :content-text="contentText" />
		<!-- 场馆列表 e -->
		
		<!-- 可拖动悬浮按钮 s -->
		<drag-button :isDock="true" :existTabBar="true" @btnClick="getVenueListByLocation"><!-- @btnTouchstart="btnTouchstart" @btnTouchend="btnTouchend" -->
			<!-- 使用内容插槽 -->
			<template slot="content">  
				<!-- 这里放自定义的内容 -->
				<text class="uni-icon uni-icon-location"></text>
			</template>
		</drag-button>
		<!-- 可拖动悬浮按钮 e -->
	</view>
</template>

<script>
	import {uniSearchBar, uniSwiperDot, uniLoadMore} from '@dcloudio/uni-ui';
	var dateUtils = require('../../common/util.js').dateUtils;
	import {mapState} from 'vuex';
	import common from '../../common/common.js';
	import dragButton from "@/components/drag-button/drag-button.vue";
	
	export default {
		components: {
			uniSearchBar, // SearchBar 搜索栏
			uniSwiperDot, // SwiperDot 轮播图指示点
			uniLoadMore, // LoadMore 加载更多
			dragButton // 可拖动悬浮按钮
		},
		data() {
			return {
				/* 顶部轮播 s */
				/* swiper s */
				// background: ['color1', 'color2', 'color3'],
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/* swiper e */
				
				adList: [{}], // 顶部轮播列表
				
				/* uniSwiperDot s */
				current: 0,
				mode: 'round',
				/* uniSwiperDot e */
				/* 顶部轮播 e */
				
				/* 场馆列表 s */
				title: '场 馆',
				listFlag: 0, // 获取附近或全部场馆列表的标识
				
				/* uniLoadMore s */
				listData: [],
				last_id: '',
				reload: false,
				status: 'more',
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '没有更多'
				},
				/* uniLoadMore e */
				/* 场馆列表 e */
			}
		},
		globalData: {
			longitude: '', // 当前位置的经度
			latitude: '', // 当前位置的纬度
		},
		onLoad() {
			this.getTopBanner(); // 获取顶部轮播列表
			this.getVenueListByLocation(); // 通过定位获取场馆列表
		},
		onReady() {
			let self = this;
			//打开加载动画
			this.$refs.loading.open()
			setTimeout(function () {
				//关闭加载动画
				self.$refs.loading.close()
			}, 1000);
		},
		onPageScroll(event) {
			
		},
		onPullDownRefresh() { // 监听用户下拉动作
			this.reload = true;
			this.last_id = '';
			this.getTopBanner();
			this.getVenueList();
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		onReachBottom() {
			this.status = 'more';
			this.getVenueList();
		},
		computed: mapState(['userInfo']), // 对全局变量 userInfo 进行监控
		methods: {
			/**
			 * 通过定位获取场馆列表
			 */
			getVenueListByLocation() {
				let self = this;
				this.listData = [];
				this.last_id = '';
				
				uni.showModal({
					title: '授权定位',
					content: '获取你的地理位置，查看场馆',
					cancelText: '全部',
					confirmText: '附近',
					success: function (res) {
						// 获取当前的地理位置、速度
						uni.getLocation({
							type: 'wgs84',
							success: function (res1) {
								// console.log('当前位置的经度：' + res.longitude);
								// console.log('当前位置的纬度：' + res.latitude);
								getApp().globalData.longitude = res1.longitude.toFixed(6);
								getApp().globalData.latitude = res1.latitude.toFixed(6);
								
								if (res.confirm == true) {
									self.title = '附 近 场 馆';
									self.listFlag = 1;
									self.getVenueList(); // 获取（附近）场馆列表数据
								}
								if (res.cancel == true) {
									self.title = '场 馆';
									self.listFlag = 0;
									self.getVenueList(); // 获取（全部）场馆列表数据
								}
							}
						});
					}
				});
			},
			
			swiperChange(e) {
				this.current = e.detail.current
			},
			/**
			 * 获取顶部轮播列表
			 */
			getTopBanner() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'ad',
					data: {
						position_id: 2, // 广告位ID
					},
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					success: data => {
						if (data.statusCode == 200) {
							let adList = data.data.data.data;
							adList.forEach ((item, index) => {
								// 广告图片
								item.ad_pic = item.ad_pic ? self.$imgServerUrl + item.ad_pic.replace(/\\/g, "/") : '/static/img/home.png';
							})
							self.adList = adList;
						}
					},
					fail: (data, code) => {
						console.log('fail' + JSON.stringify(data));
					}
				});
			},
			/**
			 * 跳转广告详情页
			 * @param {Object} url
			 */
			toAdDetail(url) {
				if (url.substr(0, 7).toLowerCase() == "http://" || url.substr(0, 8).toLowerCase() == "https://") { // 跳转外部链接
					window.location.href = url;
				} else {
					uni.navigateTo({
						url: url
					});
				}
			},
			
			/**
			 * 获取场馆列表数据
			 */
			getVenueList() {
				let self = this;
				
				// 声明向服务端传递的data参数
				var data = {
					longitude: getApp().globalData.longitude, //103.989685
					latitude: getApp().globalData.latitude, //30.716756
					listFlag: this.listFlag, // 获取附近或全部场馆列表的标识
					size: 10, // 首次加载条数
					// column: 'venue_id,venue_name,thumb,venue_phone,address' //需要的字段名
				};
				if (this.last_id) {
					//说明已有数据，目前处于上拉加载
					this.status = 'loading';
					data.minId = this.last_id;
					// data.time = new Date().getTime() + '';
					data.size = 5; // 加载更多时每次加载条数
				}
				uni.request({
					url: this.$serverUrl + 'venue', //'https://unidemo.dcloud.net.cn/api/news'
					data: data,
					/* data: {
						longitude: getApp().globalData.longitude, //103.989685
						latitude: getApp().globalData.latitude, //30.716756
						listFlag: this.listFlag, // 获取附近或全部场馆列表的标识
					}, */
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					success: data => {
						if (data.statusCode == 200) {
							let list = data.data.data.list;
							// let list = this.setTime(data.data.data.list);
							list.forEach ((item, index) => {
								// 场馆缩略图
								item.thumb = item.thumb ? self.$imgServerUrl + item.thumb.replace(/\\/g, "/") : '../../static/img/home.png';
								
								// 场馆和用户的距离
								if (1 < item.distance) {
									item.distance = item.distance + '㎞'; // 后端PHP计算的距离
								} else {
									item.distance = item.distance * 1000 + 'm';
								}
								// item.distance = common.distance(globalData.latitude, globalData.longitude, item.latitude, item.longitude) + ' ㎞'; // 前端JS计算的距离
							})
							
							this.listData = this.reload ? list : this.listData.concat(list); // 根据回调函数 success 的写法，此处 this 与 self 相同
							this.last_id = list[list.length - 1].venue_id;
							this.reload = false;
						} else if (data.statusCode == 404) {
							// 判断数据已经全部加载
							if (this.last_id == data.data.data.maxId) {
								this.status = '';return;
							}
						}
					},
					fail: (data, code) => {
						console.log('fail' + JSON.stringify(data));
					}
				});
			},
			/**
			 * 跳转场馆详情页
			 * @param {Object} e
			 */
			toVenueDetail: function(e) {
				let detail = {
					venue_id: e.venue_id,
					venue_name: e.venue_name,
					/* thumb: e.thumb,
					address: e.address,
					venue_phone: e.venue_phone,
					longitude: e.longitude,
					latitude: e.latitude */
				};
				uni.navigateTo({
					url: '../venue/venue-detail?detailData=' + encodeURIComponent(JSON.stringify(detail))
				});
			},
			setTime: function(items) {
				var newItems = [];
				items.forEach(e => {
					newItems.push({
						venue_id: e.venue_id,
						venue_name: e.venue_name,
						thumb: e.thumb,
						venue_phone: e.venue_phone,
						address: e.address
						// published_at: dateUtils.format(e.published_at)
					});
				});
				return newItems;
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
	}
	
	/* swiper s */
	.uni-margin-wrap {
		width:690upx;
		margin:0 30upx;
	}
	.swiper {
		height: 300upx;
	}
	.swiper-item {
		display: block;
		height: 300upx;
		line-height: 300upx;
		text-align: center;
	}
	/* swiper e */
	
	/* 场馆列表 s */
	.uni-media-list-logo {
		width: 180upx;
		height: 140upx;
	}
	
	.uni-media-list-body {
		height: auto;
		justify-content: space-around;
	}
	
	.uni-media-list-text-top {
		height: 74upx;
		font-size: 28upx;
		overflow: hidden;
	}
	
	.uni-media-list-text-bottom {
		display: flex;
		flex-direction: row;
		justify-content: space-between;
	}
	/* 场馆列表 e */
</style>
