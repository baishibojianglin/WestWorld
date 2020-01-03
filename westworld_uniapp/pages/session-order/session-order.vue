<template>
	<view>
		<!-- uniapp loading加载动画 -->
		<w-loading text="加载中…" mask="true" click="false" ref="loading"></w-loading>
		
		<view class="uni-padding-wrap uni-common-mt" :style="stylePosition">
			<uni-segmented-control :current="current" :values="items" style-type="text" active-color="#4cd964" @clickItem="onClickItem" />
		</view>
		
		<view v-for="(item, index) in sessionOrderList" :key="index" ref="sessionOrderDom">
			<uni-card :title="item.venue_name" :extra="item.pay_status ? '已付款' : '未付款'" :note="(item.pay_status && item.order_status != 3) ? '' : 'Tips'" :thumbnail="item.thumb" :is-shadow="true" @click="clickCard(item.session_order_id)">
				
				<uni-swipe-action>
					<uni-swipe-action-item :options="options" @click="swipeActionClick($event, item)" >
						<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
							<view class="text">
								<view class="uni-text-small">比赛日期：<text class="uni-bold">{{ item.session_date }}</text></view>
								<view class="uni-text-small">比赛场次：<text class="uni-bold">{{ item.start_time + '~' + item.end_time }}</text></view>
								<view class="uni-text-small">订单编号：{{ item.order_sn }}</view>
								<view class="red uni-bold">￥{{ item.total_price > 0 ? item.total_price : item.order_price }}</view>
							</view>
							<view class="text">
								<!-- 二维码 -->
								<tki-qrcode :cid="'tki-qrcode-canvas' + item.session_order_id" :val="val + item.session_order_id" :size="size" :onval="onval" :loadMake="loadMake" :usingComponents="true" @result="qrR" v-if="(item.order_status == 1 || item.order_status == 2) ? true : ifShow" :ref="'qrcode' + item.session_order_id" />
							</view>
						</view>
					</uni-swipe-action-item>
				</uni-swipe-action>
				
				<template slot="footer">
					<view class="footer-box" v-if="0 == item.pay_status">
						<view @click.stop="footerClick('取消比赛', item.session_order_id, item.order_sn, index)"> <button class="mini-btn" type="default" size="mini" plain="true">取消比赛</button></view>
						<view @click.stop="footerClick('付款', item.session_order_id, item.order_sn, '', item.total_price)"> <button class="mini-btn" type="warn" size="mini" plain="true">付款</button></view>
					</view>
					<view class="footer-box" v-if="item.order_status == 3">
						<view @click.stop="footerClick('删除比赛', item.session_order_id, item.order_sn, index)"> <button class="mini-btn" type="default" size="mini" plain="true">删除比赛</button></view>
					</view>
				</template>
			</uni-card>
		</view>
	</view>
</template>

<script>
	import {uniSegmentedControl, uniCard, uniSwipeAction, uniSwipeActionItem} from '@dcloudio/uni-ui';
	import tkiQrcode from '@/components/tki-qrcode/tki-qrcode.vue';
	import {mapState} from 'vuex';
	import common from '@/common/common.js';
	
	export default {
		components: {uniSegmentedControl, uniCard, uniSwipeAction, uniSwipeActionItem, tkiQrcode},
		data() {
			return {
				/* 分段器 s */
				items: ['待付款', '已预约', '进行中', '已完成'],
				current: 0,
				stylePosition: '', // 分段器定位样式
				/* 分段器 e */
				
				/* SwipeAction 滑动操作 s */
				// SwipeAction 组件选项内容及样式
				options: [{
					text: '场馆',
					style: {
						backgroundColor: '#007aff'
					}
				},{
					text: '比赛规则',
					style: {
						backgroundColor: 'rgb(254,156,1)'
					}
				}],
				/* SwipeAction 滑动操作 e */
				
				// 比赛场次订单列表
				sessionOrderList: [], // 如 [{session_order_id: 1, session_date: '2019-12-12', start_time: '11:00:00', end_time: '12:00:00', order_price: 126, total_price: 0, pay_status: 0, venue_name: '场馆001', thumb: 'https://img-cdn-qiniu.dcloud.net.cn/new-page/uni.png'},…]
				
				/* 二维码 s */
				ifShow: false,
				val: 'http://west.dilinsat.com/venue/session_order/read?id=', // 要生成的二维码值
				size: 200, // 二维码大小
				unit: 'upx', // 单位
				background: '#b4e9e2', // 背景色
				foreground: '#309286', // 前景色
				pdground: '#32dbc6', // 角标色
				icon: '', // 二维码图标
				iconsize: 40, // 二维码图标大小
				lv: 3, // 二维码容错级别，一般不用设置，默认就行
				onval: true, // val值变化时自动重新生成二维码
				loadMake: true, // 组件加载完成后自动生成二维码
				src: '' , // 二维码生成后的图片地址或base64
				/* 二维码 e */
			}
		},
		onLoad(event) {
			// 动态设置当前页面的标题
			if (event.order_status) {
				this.current = parseInt(event.order_status)
			}
			uni.setNavigationBarTitle({
				title: this.items[this.current]
			});
			
			// 获取比赛场次订单列表
			this.getSessionOrderList();
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
		onPullDownRefresh() { // 监听用户下拉动作
			this.sessionOrderList = [];
			this.getSessionOrderList();
			setTimeout(function () {
				uni.stopPullDownRefresh();
			}, 1000);
		},
		onPageScroll(event) {
			// 分段器定位样式
			if (event.scrollTop > 0) {
				this.stylePosition = 'position: fixed; z-index: 9; top: 59upx; background-color: rgba(255, 255, 255, 0.9);';
			} else {
				this.stylePosition = '';
			}
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			/**
			 * 获取比赛场次订单列表
			 */
			getSessionOrderList() {
				uni.request({
					url: this.$serverUrl + 'session_order',
					data: {
						user_id: this.userInfo.user_id,
						order_status: this.current
					},
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					method: 'GET',
					success: (res) => {
						if (res.statusCode == 200) {
							let sessionOrderList = res.data.data.data;
							sessionOrderList.forEach ((item, index) => {
								// 场馆缩略图
								item.thumb = item.thumb ? this.$imgServerUrl + item.thumb.replace(/\\/g, "/") : '/static/img/home.png';
							})
							this.sessionOrderList = sessionOrderList;
						}
					},
					fail: (error) => {
						console.log(error)
					}
				})
			},
			
			/**
			 * 分段器
			 * @param {Object} e
			 */
			onClickItem(e) {
				if (this.current !== e.currentIndex) {
					this.current = e.currentIndex;
					
					// 动态设置当前页面的标题
					uni.setNavigationBarTitle({
						title: this.items[this.current]
					});
					
					// 获取比赛场次订单列表
					this.getSessionOrderList();
				}
			},
			
			/**
			 * 点击 Card 触发事件
			 * @param {Object} session_order_id
			 */
			clickCard(session_order_id) {
				uni.navigateTo({
					url: '/pages/session-order/session-order-detail?session_order_id=' + session_order_id
				})
			},
			
			/**
			 * 点击 SwipeAction 滑动操作选项按钮
			 * @param {Object} e
			 * @param {Object} item 循环列表中指定的数据
			 */
			swipeActionClick(e, item) {
				/* uni.showToast({
					title: `点击了${e.content.text}按钮`,
					icon: 'none'
				}) */
				if (e.index == 0) {
					// 跳转场馆详情页
					this.toVenueDetail(item);
				}
				if (e.index == 1) {
					// 查看比赛规则
					this.toSceneDetail('比赛规则', item.scene_id);
				}
			},
			
			/**
			 * 跳转场馆详情页
			 * @param {Object} e
			 */
			toVenueDetail: function(e) {
				let detail = {
					venue_id: e.venue_id,
					venue_name: e.venue_name
				};
				uni.navigateTo({
					url: '../venue/venue-detail?detailData=' + encodeURIComponent(JSON.stringify(detail))
				});
			},
			
			/**
			 * 查看比赛规则
			 * @param {Object} types
			 * @param {Object} scene_id
			 */
			toSceneDetail(types, scene_id) {
				uni.navigateTo({
					url: '/pages/scene/scene-detail?scene_id=' + scene_id + '&types=' + types
				});
			},
			
			/**
			 * 比赛场次订单操作按钮
			 * @param {Object} types
			 * @param {Object} session_order_id
			 * @param {Object} order_sn
			 * @param {Object} index
			 */
			footerClick(types, session_order_id, order_sn, index, total_price) {
				let title = types == '付款' ? '发起支付' : '确认' + types;
				let self = this;
				uni.showModal({
					title: title,
					success:function(res){
						if (res.confirm) {
							if (types == '取消比赛') {
								self.changeSessionOrderStatus(session_order_id, 4, index);
							}
							if (types == '删除比赛') {
								self.deleteSessionOrder(session_order_id, index);
							}
							if (types == '付款') {
								// 跳转支付页面
								uni.navigateTo({
									url: '/pages/API/request-payment/request-payment?price=' + total_price
								})
							}
						}
					}
				})
			},
			
			/**
			 * 改变比赛场次订单状态：取消比赛订单
			 * @param {Object} session_order_id
			 * @param {Object} order_status
			 * @param {Object} index
			 */
			changeSessionOrderStatus(session_order_id, order_status, index) {
				uni.request({
					url: this.$serverUrl + 'session_order/' + session_order_id,
					data: {
						order_status: order_status
					},
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					method: 'PUT',
					success: (res) => {
						let title = res.data.message;
						title = res.statusCode == 201 ? '取消比赛成功' : '取消比赛失败';
						uni.showToast({
							title: title,
							icon: 'none'
						});
						// 移除DOM节点
						this.removeSessionOrderDom(index);
					},
					fail: (error) => {
						console.log(error)
					}
				})
			},
			
			/**
			 * 删除指定比赛场次订单
			 * @param {Object} session_order_id
			 * @param {Object} index
			 */
			deleteSessionOrder(session_order_id, index) {
				uni.request({
					url: this.$serverUrl + 'session_order/' + session_order_id,
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					method: 'DELETE',
					success: (res) => {
						let title = res.data.message;
						title = res.statusCode == 200 ? '删除比赛成功' : '删除比赛失败';
						uni.showToast({
							title: title,
							icon: 'none'
						});
						// 移除DOM节点
						this.removeSessionOrderDom(index);
					},
					fail: (error) => {
						console.log(error)
					}
				})
			},
			
			/**
			 * 移除指定的比赛场次订单 DOM 节点
			 * @param {Object} index
			 */
			removeSessionOrderDom(index) {
				this.sessionOrderList.splice(index, 1); // 点击调用删除方法，在sessionOrderList数组里删除index这个下标项，逗号后面的1就代表删除当前的1项
			},
			
			/**
			 * 返回二维码路径
			 * @param {Object} res
			 */
			qrR(res) {
				this.src = res;
			}
		}
	}
</script>

<style>
	/* uni-card s */
	.footer-box {
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		justify-content: flex-end;
		flex-direction: row;
	}
	/* uni-card e */
	
	.mini-btn {
		margin-right: 10upx;
	}
</style>
