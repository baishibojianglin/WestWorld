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
						<view class="uni-media-list-text-top">{{ value.venue_name }}</view>
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
	import {mapState, mapMutations} from 'vuex';
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
				title: '附近场馆',
				
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
		onLoad() {
			this.getList();
			
			uni.getLocation({
			    type: 'wgs84',
			    success: function (res) {
			        console.log('当前位置的经度：' + res.longitude);
			        console.log('当前位置的纬度：' + res.latitude);
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
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			...mapMutations(['logout']), // 对全局方法 logout 进行监控
			getList() {
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
					header: {
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': '12345dg', // 设备号
						'access-user-token': this.userInfo.token
					},
					success: data => {
						if (data.statusCode == 200) {
							/* let list = this.setTime(data.data);
							this.listData = this.reload ? list : this.listData.concat(list);
							this.last_id = list[list.length - 1].id;
							this.reload = false; */
							
							// console.log(data)
							let venueList = data.data.data.list;
							venueList.forEach ((item, index) => {
								item.thumb = item.thumb ? self.$imgServerUrl + item.thumb.replace(/\\/g, "/") : '../../static/img/home.png'; // 场馆缩略图
							})
							self.listData = venueList;
							console.log(self.listData)
						}
					},
					fail: (data, code) => {
						console.log('fail' + JSON.stringify(data));
					}
				});
			},
			goDetail: function(e) {
				// 				if (!/前|刚刚/.test(e.published_at)) {
				// 					e.published_at = dateUtils.format(e.published_at);
				// 				}
				let detail = {
					author_name: e.author_name,
					cover: e.cover,
					id: e.id,
					post_id: e.post_id,
					published_at: e.published_at,
					title: e.title
				};
				uni.navigateTo({
					url: '../list2detail-detail/list2detail-detail?detailDate=' + encodeURIComponent(JSON.stringify(detail))
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
