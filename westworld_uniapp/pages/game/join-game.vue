<template>
	<view>
		<!-- uniapp loading加载动画 -->
		<w-loading text="加载中…" mask="true" click="false" ref="loading"></w-loading>
		
		<!-- 场馆信息 -->
		<view class="example-title uni-common-mb">
			<image :src="venueData.thumb" style="width: 80upx; height: 80upx; border-radius: 10upx;"></image>
			<view>{{venueData.venue_name}}</view>
			<view><text class="uni-icon uni-icon-location"></text>{{venueData.address}}</view>
		</view>
		
		<!-- 分段器 s -->
		<view class="uni-padding-wrap" :style="stylePosition">
			<uni-segmented-control :current="current" :values="items" :style-type="styleType" :active-color="activeColor" @clickItem="onClickItem" />
		</view>
		
		<view class="example-title">{{active === 5 ? '' : '选择'}}{{ items[active] }}</view>
		<view class="example-body">
			<view class="content uni-common-mb">
				<!-- 场景 s -->
				<view v-if="current === 0">
					<radio-group @change="radioChangeScene">
						<uni-card v-for="(item, index) in sceneList" :key="index" :is-shadow="true" :title="item.scene_name" mode="style" :thumbnail="item.thumb" :extra="item.scene_name" note="Tips" click="">
							<!-- <text class="content-box-text">{{ item.scene_name }}</text> -->
							<template slot="footer">
								<view class="footer-box">
									<view class=""><radio :id="'scene' + item.scene_id" :value="item.scene_id" :checked="item.checked"></radio><label :for="'scene' + item.scene_id"><text class="footer-box__item"> 选择</text></label></view>
									<view class="" @click.stop="footerClick('场景描述', item.scene_id)"><text class="footer-box__item"> 场景描述</text></view>
									<view class="" @click.stop="footerClick('比赛规则', item.scene_id)"><text class="footer-box__item"> 比赛规则</text></view>
								</view>
							</template>
						</uni-card>
					</radio-group>
				</view>
				<!-- 场景 e -->
				<view v-if="current === 1"><text class="content-text">选项卡2的内容</text></view>
				<view v-if="current === 2"><text class="content-text">选项卡3的内容</text></view>
			</view>
		</view>
		<!-- 分段器 e -->
		
		<!-- 底部下一步按钮 -->
		<view class="uni-padding-wrap uni-common-mt uni-common-mb next_step">
			<button :type="active === 5 ? 'warn' : 'default'" @click="change">{{ active === 5 ? '确 定' : '下一步' }}</button>
		</view>
	</view>
</template>

<script>
	import {uniSteps, uniSegmentedControl, uniGrid, uniCard, uniGridItem, uniCalendar, uniTag, uniNumberBox} from '@dcloudio/uni-ui';
	import {mapState} from 'vuex';
	import common from '@/common/common.js';
	import Aes from '@/common/Aes.js';
	
	export default {
		components: {
			uniSteps, // Steps 步骤条
			uniSegmentedControl, // SegmentedControl 分段器
			uniGrid, uniGridItem, // Grid 宫格
			uniCard, // Card 卡片
			uniCalendar, // Calendar 日期
			uniTag, // Tag 标签
			uniNumberBox, // NumberBox 数字输入框
		},
		data() {
			return {
				venueId: '', // 场馆ID
				venueData: {}, // 场馆信息
				
				/* 步骤条 s */
				active: 0,
				stepsList: [
					{title: '1', desc: '场景'}, 
					{title: '2', desc: '场次'}, 
					{title: '3', desc: '组队'}, 
					{title: '4', desc: '房间'}, 
					// {title: '5', desc: '人数'}, 
					{title: '6', desc: '装备'}, 
					{title: '7', desc: '确定'},
				],
				/* 步骤条 e */
				
				/* 分段器 s */
				items: ['场景', '场次', '房间', '组队', '装备', '确定'],
				colors: ['#007aff', '#4cd964', '#dd524d'],
				current: 0,
				colorIndex: 0,
				activeColor: '#4cd964',
				styleType: 'text',
				stylePosition: '', // 分段器定位样式
				/* 分段器 e */
				
				/* 选择场景 s */
				sceneList: [], // 场景列表，如：[{scene_id: '1', scene_name: '城市city', thumb: '/static/img/home.png'}, {scene_id: '2', scene_name: '乡村country', thumb: '/static/img/home.png'}]
				sceneId: '', // 选中的场景ID
				/* 选择场景 e */
			}
		},
		onLoad(event) {
			// console.log(event)
			this.venueId = event.id; // 场馆ID
			this.venueData = event; // 场馆数据 //JSON.parse(event.venueData);
			this.getSceneList(this.venueId); // 获取场景列表
		},
		onReady() {
			let self = this;
			// 打开加载动画
			this.$refs.loading.open()
			setTimeout(function () {
				// 关闭加载动画
				self.$refs.loading.close()
			}, 1000);
		},
		onPageScroll(event) {
			// 分段器定位样式
			if (event.scrollTop >= 100) {
				// #ifndef APP-PLUS
				this.stylePosition = 'position: fixed; left: 0; right: 0; top: 89upx; z-index: 1; background-color: rgba(255, 255, 255, 0.9);';
				// #endif
				// #ifdef APP-PLUS
				this.stylePosition = 'position: fixed; left: 0; right: 0; top: 0; z-index: 1; background-color: rgba(255, 255, 255, 0.9);';
				// #endif
			} else {
				this.stylePosition = '';
			}
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			/* 步骤条 s */
			change() {
				// console.log(this.active)
				// 所有步骤必须判断是否选择场景
				if (!this.sceneId) {
					uni.showToast({
						title: '请选择场景',
						icon: 'none'
					});
					this.current = this.active = 0;
					return;
				}
				// 选好第1步外，其他步骤必须判断是否选择场次
				if (!this.sessionId && this.active !== 0) {
					uni.showToast({
						title: '请选择场次',
						icon: 'none'
					});
					this.current = this.active = 1;
					return;
				}
				// 选好第1、2步外，其他步骤必须判断是否选择房间
				if (!this.roomId && this.active > 1) {
					uni.showToast({
						title: '请选择房间',
						icon: 'none'
					});
					this.current = this.active = 2;
					return;
				}
				// 选好第1~3步外，其他步骤必须判断是否选择组队
				if (!this.teamId && this.active > 2) {
					uni.showToast({
						title: '请创建或加入团队',
						icon: 'none'
					});
					this.current = this.active = 3;
					return;
				}
				// 选好第1~4步外，其他步骤必须判断是否选择装备
				/* if (!this.equipmentId && this.active > 3) {
					uni.showToast({
						title: '请选择装备',
						icon: 'none'
					});
					this.current = this.active = 4;
					return;
				} */
				// 计算比赛费用
				if (this.active >= 4) {
					// 计算比赛费用
					var session_price = parseFloat(this.sessionArray[this.sessionIndex].session_price); // 场次费
					var room_price = parseFloat(this.roomList[this.currentRoom].room_price); // 房间费
					var equipment_use_fee = this.equipmentArray[this.equipmentIndex].use_fee ? parseFloat(this.equipmentArray[this.equipmentIndex].use_fee) : 0; // 装备使用费
					this.price = (session_price + room_price + equipment_use_fee).toFixed(2);
				}
				// 发起支付
				if (this.active == 5) {
					let self = this;
					uni.showModal({
						title: '发起支付',
						success:function(res){
							if (res.confirm) {
								// 生成订单并跳转支付页面
								self.createOrder();
							}
						}
					});
					return;
				}
				
				// 步骤条
				if (this.active < this.stepsList.length - 1) {
					this.active += 1
				} else {
					this.active = 0
				}
				
				// 分段器
				this.current = this.active
			},
			/* 步骤条 e */
			
			/* 分段器 s */
			onClickItem(e) {
				if (this.current !== e.currentIndex) {
					// 分段器
					this.current = e.currentIndex
					
					// 步骤条
					this.active = this.current
				}
			},
			/* 分段器 e */
			
			/* 选择场景 s */
			/**
			 * 获取场景列表
			 * @param {Object} venueId
			 */
			getSceneList(venueId) {
				let self = this;
				uni.request({
				    url: this.$serverUrl + 'scene',
				    data: {
				        venue_id: venueId
				    },
				    header: {
				    	'sign': common.sign(), // 验签
				    	'version': getApp().globalData.version, // 应用大版本号
				    	'model': getApp().globalData.systemInfo.model, // 手机型号
				    	'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
				    	'did': getApp().globalData.did, // 设备号
				    },
				    success: (res) => {
				        // console.log(res.data);
						// 场景列表
						let sceneList = res.data.data.data;
						sceneList.forEach ((item, index) => {
							// 场景缩略图
							item.thumb = item.thumb ? self.$imgServerUrl + item.thumb.replace(/\\/g, "/") : '/static/img/home.png';
							item.scene_id = String(item.scene_id);
						})
						self.sceneList = sceneList;
				    }
				});
			},
			
			/**
			 * 选择场景
			 * @param {Object} e
			 */
			radioChangeScene: function(e) {
				var checked = e.target.value;
				this.sceneId = checked; // 选中的场景ID
				console.log('场景sceneId = ', checked);
				
				// 初始化选中的场次ID、房间ID、组队ID、装备ID、比赛费用
				this.sessionId = ''; this.sessionIndex = 0;
				this.roomId = ''; this.currentRoom = '';
				this.teams = []; this.teamArray = [{}]; this.teamIndex = 0; this.teamId = '';
				this.equipmentId = ''; this.equipmentIndex = 0;
				this.price = 0;
				
				this.getSessionList(this.venueId, this.sceneId); // 获取场次列表
				this.getSceneRoomList(this.venueId, this.sceneId); // 获取房间列表
				this.getEquipmentList(this.venueId, this.sceneId); // 获取装备列表
			},
			
			/**
			 * 查看场景描述或比赛规则
			 * @param {Object} types
			 */
			footerClick(types, scene_id) {
				uni.navigateTo({
					url: '/pages/scene/scene-detail?scene_id=' + scene_id + '&types=' + types
				});
			},
			/* 选择场景 e */
		}
	}
</script>

<style>
	page {
		display: flex;
		flex-direction: column;
		box-sizing: border-box;
		background-color: #efeff4
	}

	view {
		font-size: 28upx;
		line-height: inherit
	}

	.example {
		padding: 0 30upx 30upx
	}

	.example-title {
		display: flex;
		justify-content: space-between;
		align-items: center;
		font-size: 32upx;
		color: #464e52;
		padding: 30upx;
		margin-top: 20upx;
		position: relative;
		background-color: #fdfdfd
	}

	.example-title__after {
		position: relative;
		color: #031e3c
	}

	/* .example-title:after {
		content: '';
		position: absolute;
		left: 0;
		margin: auto;
		top: 0;
		bottom: 0;
		width: 10upx;
		height: 40upx;
		border-top-right-radius: 10upx;
		border-bottom-right-radius: 10upx;
		background-color: #031e3c
	} */

	.example .example-title {
		margin: 40upx 0
	}

	.example-body {
		border-top: 1px #f5f5f5 solid;
		padding: 30upx;
		background: #fff;
		margin-bottom: 200upx;
	}

	.example-info {
		padding: 30upx;
		color: #3b4144;
		background: #fff
	}

	.next_step {
		/* flex-direction: column; */
		position: fixed;
		left: 0;
		right: 0;
		bottom: 0;
	}
	
	.image {
		width: 50upx;
		height: 50upx;
	}
	
	.text {
		font-size: 26upx;
		margin-top: 10upx;
	}
	.scene_name {
		background-color: rgba(255, 255, 255, 0.6);
		padding: 5upx;
		margin: 5upx;
		border-radius: 10upx;
	}
	
	.tag-view {
		margin: 10upx 20upx;
		display: inline-block;
	}
	
	.uni-icon-clear {
		color: #999;
	}
	
	/* uni-card s */
	.footer-box {
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		justify-content: space-between;
		flex-direction: row;
	}
	
	.footer-box__item {
		align-items: center;
		padding: 10rpx 0;
		font-size: 30rpx;
		color: #666;
	}
	/* uni-card e */
</style>
