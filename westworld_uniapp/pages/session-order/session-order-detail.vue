<template>
	<view>
		<uni-card :title="sessionOrderDetail.venue_name" :extra="sessionOrderDetail.order_status_msg" :thumbnail="sessionOrderDetail.thumb" :is-shadow="true">
			
			<view class="uni-flex uni-row" style="-webkit-justify-content: space-between;justify-content: space-between;">
				<view class="text">
					<view class="uni-text-small">比赛日期：<text class="uni-bold">{{ sessionOrderDetail.session_date }}</text></view>
					<view class="uni-text-small">比赛场次：<text class="uni-bold">{{ sessionOrderDetail.start_time + '~' + sessionOrderDetail.end_time }}</text></view>
					<view class="uni-text-small">场景：<text class="uni-bold">{{ sessionOrderDetail.scene_name }}</text></view>
					<view class="uni-text-small">房间：<text class="uni-bold">{{ sessionOrderDetail.room_name }}</text></view>
					<view class="uni-text-small">比赛团队：<text class="uni-bold">{{ sessionOrderDetail.session_teams_id + "-" + sessionOrderDetail.team_id }}</text></view>
					<view class="uni-text-small">付费装备：<text :class="sessionOrderDetail.equipment_name ? 'uni-bold' : ''">{{ sessionOrderDetail.equipment_name ? sessionOrderDetail.equipment_name : "（未使用）" }}</text></view>
					<view class="uni-text-small">订单编号：{{ sessionOrderDetail.order_sn }}</view>
					<view class="red uni-bold">{{ '￥' + sessionOrderDetail.total_price > 0 ? sessionOrderDetail.total_price : sessionOrderDetail.order_price }}</view>
					<view class="uni-text-small" v-if="sessionOrderDetail.order_status == 0 || sessionOrderDetail.order_status == 1">
						距比赛开始：
						<uni-countdown :second="sessionOrderDetail.session_start_time" color="red" @timeup="timeup" class="uni-bold" />
					</view>
					<view class="uni-text-small" v-if="sessionOrderDetail.order_status == 2">
						距比赛结束：
						<uni-countdown :second="sessionOrderDetail.session_end_time" color="red" @timeup="timeup" class="uni-bold" />
					</view>
				</view>
				<view class="text">
					<!-- 二维码 -->
					<tki-qrcode :cid="'tki-qrcode-canvas' + sessionOrderDetail.session_order_id" :val="val + sessionOrderDetail.session_order_id" :size="size" :onval="onval" :loadMake="loadMake" :usingComponents="true" @result="qrR" v-if="(sessionOrderDetail.order_status == 1 || sessionOrderDetail.order_status == 2) ? true : ifShow" :ref="'qrcode' + sessionOrderDetail.session_order_id" />
				</view>
			</view>
			
		</uni-card>
		
		<view class="footer_button" v-if="(sessionOrderDetail.pay_status == 0 || sessionOrderDetail.order_status == 3) && sessionOrderDetail.order_status != 4">
			<uni-card :is-full="true" :is-shadow="true">
				<view class="footer-box" v-if="sessionOrderDetail.pay_status == 0">
					<view @click.stop="footerClick('取消比赛', sessionOrderDetail.session_order_id, sessionOrderDetail.order_sn)"> <button class="mini-btn" type="default" size="mini" plain="true">取消比赛</button></view>
					<view @click.stop="footerClick('付款', sessionOrderDetail.session_order_id, sessionOrderDetail.order_sn, sessionOrderDetail.total_price)"> <button class="mini-btn" type="warn" size="mini" plain="true">付款</button></view>
				</view>
				<view class="footer-box" v-if="sessionOrderDetail.order_status == 3">
					<view @click.stop="footerClick('删除比赛', sessionOrderDetail.session_order_id, sessionOrderDetail.order_sn)"> <button class="mini-btn" type="default" size="mini" plain="true">删除比赛</button></view>
				</view>
			</uni-card>
		</view>
	</view>
</template>

<script>
	import {uniCard, uniCountdown} from '@dcloudio/uni-ui';
	import tkiQrcode from '@/components/tki-qrcode/tki-qrcode.vue';
	import {mapState} from 'vuex';
	import common from '@/common/common.js';
	
	export default {
		components: {uniCard, uniCountdown, tkiQrcode},
		data() {
			return {
				/* 场次订单 s */
				sessionOrderId: '',
				sessionOrderDetail: {},
				/* 场次订单 e */
				
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
			this.sessionOrderId = event.session_order_id; // 比赛场次订单ID
			this.getSessionOrderDetail(); // 获取比赛场次订单详情
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			/**
			 * 获取比赛场次订单详情
			 */
			getSessionOrderDetail() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'session_order/' + this.sessionOrderId,
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					success:function(res){
						let sessionOrderDetail = res.data.data;
						// let timestamp = Date.parse(new Date())/1000; // 当前秒级时间戳
						if (sessionOrderDetail) {
							// 场馆缩略图
							sessionOrderDetail.thumb = sessionOrderDetail.thumb ? self.$imgServerUrl + sessionOrderDetail.thumb.replace(/\\/g, "/") : '/static/img/home.png';
							self.sessionOrderDetail = sessionOrderDetail;
						}
					}
				})
			},
			
			/**
			 * 返回二维码路径
			 * @param {Object} res
			 */
			qrR(res) {
				this.src = res;
			},
			
			/**
			 * 倒计时回调事件
			 */
			timeup() {
				uni.showToast({
					title: '时间到'
				})
			},
			
			/**
			 * 比赛场次订单操作按钮
			 * @param {Object} types
			 * @param {Object} session_order_id
			 * @param {Object} order_sn
			 */
			footerClick(types, session_order_id, order_sn, total_price) {
				let title = types == '付款' ? '发起支付' : '确认' + types;
				let self = this;
				uni.showModal({
					title: types,
					content: title,
					success:function(res){
						if (res.confirm) {
							if (types == '取消比赛') {
								self.changeSessionOrderStatus(session_order_id, 4);
							}
							if (types == '删除比赛') {
								self.deleteSessionOrder(session_order_id);
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
			 */
			changeSessionOrderStatus(session_order_id, order_status) {
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
						// 更新比赛场次订单状态，同时移除底部按钮
						this.sessionOrderDetail.order_status = order_status;
						this.sessionOrderDetail.order_status_msg = '已取消';
					},
					fail: (error) => {
						console.log(error)
					}
				})
			},
			
			/**
			 * 删除指定比赛场次订单
			 * @param {Object} session_order_id
			 */
			deleteSessionOrder(session_order_id) {
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
							icon: 'none',
							success() {
								uni.redirectTo({
									url: 'session-order'
								})
							}
						});
					},
					fail: (error) => {
						console.log(error)
					}
				})
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
	
	.footer_button{
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
	}
</style>
