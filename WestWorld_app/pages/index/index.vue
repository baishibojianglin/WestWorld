<template>
	<view class="content">
		<!-- swiper s -->
		<view class="uni-margin-wrap">
			<swiper class="swiper" :indicator-dots="indicatorDots" :autoplay="autoplay" :interval="interval" :duration="duration">
				<swiper-item>
					<view class="swiper-item uni-bg-red">A</view>
				</swiper-item>
				<swiper-item>
					<view class="swiper-item uni-bg-green">B</view>
				</swiper-item>
				<swiper-item>
					<view class="swiper-item uni-bg-blue">C</view>
				</swiper-item>
			</swiper>
		</view>
		<!-- swiper e -->
		
		<!-- 附近场馆 s -->
		<page-head :title="title"></page-head>
		
		<view class="uni-list">
			<view class="uni-list-cell" hover-class="uni-list-cell-hover" v-for="(value, key) in listData" :key="key" @click="goDetail(value)">
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
		<!-- 附近场馆 e -->
	</view>
</template>

<script>
	import uniLoadMore from '@/components/uni-load-more/uni-load-more.vue';
	var dateUtils = require('../../common/util.js').dateUtils;
	import {mapState} from 'vuex';
	import common from '../../common/common.js';
	
	export default {
		components: {
			uniLoadMore
		},
		data() {
			return {
				/* swiper s */
				background: ['color1', 'color2', 'color3'],
				indicatorDots: true,
				autoplay: true,
				interval: 2000,
				duration: 500,
				/* swiper e */
				
				/* 附近场馆 s */
				title: '场 馆',
				
				listData: [],
				last_id: '',
				reload: false,
				status: 'more',
				contentText: {
					contentdown: '上拉加载更多',
					contentrefresh: '加载中',
					contentnomore: '没有更多'
				}
				/* 附近场馆 e */
			}
		},
		globalData: {
			longitude: '', // 当前位置的经度
			latitude: '', // 当前位置的纬度
		},
		onLoad() {
			// this.getList();
			let self = this;
			
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
								// 获取（指定距离）场馆列表数据
								self.getList(1);
							}
							if (res.cancel == true) {
								// 获取（所有）场馆列表数据
								self.getList(0);
							}
						}
					});
				}
			});
		},
		onPullDownRefresh() {
			this.reload = true;
			this.last_id = '';
			this.getBanner();
			this.getList();
		},
		onReachBottom() {
			this.status = 'more';
			this.getList();
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
			 * 获取场馆列表数据
			 * @param {Object} listFlag
			 */
			getList(listFlag) {
				let self = this
				
				var data = {
					column: 'id,post_id,title,author_name,cover,published_at' //需要的字段名
				};
				if (this.last_id) {
					//说明已有数据，目前处于上拉加载
					this.status = 'loading';
					data.minId = this.last_id;
					data.time = new Date().getTime() + '';
					data.pageSize = 10;
				}
				uni.request({
					url: this.$serverUrl + 'venue', //'https://unidemo.dcloud.net.cn/api/news'
					// data: data,
					data: {
						longitude: getApp().globalData.longitude, //103.989685
						latitude: getApp().globalData.latitude, //30.716756
						listFlag: listFlag, // 获取附近或所有场馆列表的标识
					},
					header: {
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					success: data => {
						if (data.statusCode == 200) {
							/* let list = this.setTime(data.data);
							this.listData = this.reload ? list : this.listData.concat(list);
							this.last_id = list[list.length - 1].id;
							this.reload = false; */
							
							let venueList = data.data.data.list;
							venueList.forEach ((item, index) => {
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
							self.listData = venueList;
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
			goDetail: function(e) {
				let detail = {
					venue_id: e.venue_id,
					venue_name: e.venue_name,
					thumb: e.thumb,
					address: e.address,
					venue_phone: e.venue_phone,
					longitude: e.longitude,
					latitude: e.latitude
				};
				uni.navigateTo({
					url: '../venue/venue-detail?detailData=' + encodeURIComponent(JSON.stringify(detail))
				});
			},
			setTime: function(items) {
				var newItems = [];
				items.forEach(e => {
					newItems.push({
						author_name: e.author_name,
						cover: e.cover,
						id: e.id,
						post_id: e.post_id,
						published_at: dateUtils.format(e.published_at),
						title: e.title
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
	
	/* 附近场馆 s */
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
	/* 附近场馆 e */
</style>
