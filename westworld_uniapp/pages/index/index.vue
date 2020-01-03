<template>
	<view class="content">
		<!-- uniapp loading加载动画 -->
		<w-loading text="" mask="true" click="true" ref="loading"></w-loading>
		
		<!-- #ifndef APP-PLUS -->
		<view class="header uni-flex uni-row" :style="styleHeader">
			<view class="text" style="width: 120upx;margin-top: 20upx;">
				<text class="uni-icon uni-icon-location uni-icon-warn" @click="getVenueListByLocation()"></text>
				<text class="uni-bold">定位</text>
			</view>
			<view class="text" style="-webkit-flex: 1;flex: 1;">
				<uni-search-bar placeholder="搜索场馆" @confirm="search" @input="input" @cancel="cancel" />
				<view class="search-result uni-center">
					<text class="search-result-text"><!-- 当前输入为： -->{{ searchVal }}</text>
				</view>
			</view>
		</view>
		<!-- #endif -->
		
		<!-- swiper s -->
		<view class="uni-margin-wrap">
			<!-- <swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration" circular="true">
				<swiper-item v-for="(value, key) in adList" :key="key">
					<view class="swiper-item uni-bg-red" :style="{background: value.bgcolor + ' url(' + value.ad_pic + ')'}" @click="toAdDetail(value.ad_link)">{{ value.ad_name }}</view>
				</swiper-item>
			</swiper> -->
			
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
							{{ value.venue_name }}
							<text style="float: right;margin-right: 20upx;">{{ value.distance }}</text>
						</view>
						<view class="uni-media-list-text-bottom">
							<text>{{ value.address }}</text>
							<text>{{ value.venue_phone }}</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<uni-load-more :status="status" :content-text="contentText" />
		<!-- 场馆列表 e -->
	</view>
</template>

<script>
	import {uniSearchBar, uniSwiperDot, uniLoadMore} from '@dcloudio/uni-ui';
	// import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	var dateUtils = require('../../common/util.js').dateUtils;
	import {mapState} from 'vuex';
	import common from '../../common/common.js';
	
	export default {
		components: {
			uniSearchBar, // SearchBar 搜索栏
			uniSwiperDot, // SwiperDot 轮播图指示点
			uniLoadMore // LoadMore 加载更多
		},
		data() {
			return {
				/* 搜索栏 s */
				styleHeader: '',
				searchVal: '',
				/* 搜索栏 e */
				
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
				mode: 'nav',
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
			// 顶部样式
			if (event.scrollTop > 0) {
				this.styleHeader = 'position: fixed; z-index: 9; top: 87upx; background-color: rgba(255, 255, 255, 0.9);';
			} else {
				this.styleHeader = '';
			}
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
		
		/**
		 * 当 searchInput 配置 disabled 为 true 时触发
		 */
		onNavigationBarSearchInputClicked(e) {
			console.log('事件执行了')
			uni.navigateTo({
				url: '/pages/index/search/search'
			});
		},
		/**
		 *  点击导航栏 buttons 时触发
		 */
		onNavigationBarButtonTap() {
			uni.showModal({
				title: '提示',
				content: '用户点击了功能按钮，这里仅做展示。',
				success: res => {
					if (res.confirm) {
						console.log('用户点击了确定');
					}
				}
			});
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
			
			/* 搜索栏 s */
			search(res) {
				uni.showToast({
					title: '搜索：' + res.value,
					icon: 'none'
				})
				this.getVenueListByLocation();
			},
			input(res) {
				this.searchVal = res.value
			},
			cancel(res) {
				uni.showToast({
					title: '点击取消，输入值为：' + res.value,
					icon: 'none'
				})
			},
			/* 搜索栏 e */
			
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
						position_id: 1, // 广告位ID
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
				
				// 搜索条件
				if(this.searchVal != null) {
					data.venue_name = this.searchVal;
				}
				
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
	
	/* 顶部 s */
	.header {
		width:690upx;
	}
	/* 顶部 e */
	
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
