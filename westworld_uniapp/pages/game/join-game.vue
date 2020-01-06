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
		
		<!-- 步骤条 s -->
		<!-- <view class="example-body">
			<uni-steps :options="stepsList" :active="active" />
		</view> -->
		<!-- 步骤条 e -->
		
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
									<view class=""><radio :id="'scene' + item.scene_id" :value="item.scene_id" :checked="sceneId == item.scene_id ? true : item.checked"></radio><label :for="'scene' + item.scene_id"><text class="footer-box__item"> 选择</text></label></view>
									<view class="" @click.stop="footerClick('场景描述', item.scene_id)"><text class="footer-box__item"> 场景描述</text></view>
									<view class="" @click.stop="footerClick('比赛规则', item.scene_id)"><text class="footer-box__item"> 比赛规则</text></view>
								</view>
							</template>
						</uni-card>
					</radio-group>
				</view>
				<!-- 场景 e -->
				
				<!-- 场次 s -->
				<view v-if="current === 1">
					<view class="uni-flex uni-row">
						<view class="text uni-common-pl">可预订日期</view>
						<view class="text uni-common-pl uni-bold">{{ timeInfo.startDate }}~{{ timeInfo.endDate }}</view>
					</view>
					<view class="uni-common-mt">
						<!-- <view class="example-title">选择日期</view> -->
						<view class="uni-list">
							<view class="uni-list-cell uni-list-cell-pd">
								<view class="uni-list-cell-left">日期</view>
								<view class="uni-list-cell-db" @click="openCalendar">
									<view class="uni-flex uni-row" style="-webkit-justify-content: flex-end;justify-content: flex-end;">
										<text class="text uni-bold">{{ sessionDate }}</text>
										<text class="uni-icon uni-icon-arrowdown"></text>
									</view>
								</view>
								<!-- Calendar 日历组件 s -->
								<uni-calendar ref="calendar" :date="timeInfo.date" :insert="timeInfo.insert" :lunar="timeInfo.lunar" :startDate="timeInfo.startDate" :endDate="timeInfo.endDate" :range="timeInfo.range" @confirm="confirmDate" />
								<!-- Calendar 日历组件 e -->
							</view>
						</view>
					</view>
					<view class="uni-common-mt">
						<!-- <view class="example-title">选择场次</view> -->
						<view class="uni-list">
							<view class="uni-list-cell uni-list-cell-pd">
								<view class="uni-list-cell-left">场次</view>
								<view class="uni-list-cell-db">
									<picker @change="pickerChangeSession" :value="sessionArray[sessionIndex].session_id" :range="sessionArray" range-key="session">
										<view class="uni-flex uni-row" style="-webkit-justify-content: flex-end;justify-content: flex-end;">
											<view class="text uni-bold">
												<text>{{ sessionArray[sessionIndex].session_time }}</text>
												<text class="red">￥{{ sessionArray[sessionIndex].session_price || 0 }}</text>
											</view>
											<text class="uni-icon uni-icon-arrowdown"></text>
										</view>
									</picker>
								</view>
							</view>
						</view>
					</view>
				</view>
				<!-- 场次 e -->
				
				<!-- 房间 s -->
				<view v-if="current === 2">
					<view class="uni-list">
						<radio-group @change="radioChangeRoom">
							<label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in roomList" :key="index">
								<view>
									<radio :value="item.room_id" :checked="roomId === item.room_id" /><!-- :checked="index === currentRoom" -->
								</view>
								<view>{{item.room_name}} <text class="red uni-bold">￥{{item.room_price}}</text></view>
							</label>
						</radio-group>
					</view>
				</view>
				<!-- 房间 e -->
				
				<!-- 人数 s -->
				<!-- <view v-show="current === 3">
					<uni-number-box :min="0" :value="number" @change="changeNumber"></uni-number-box>
				</view> -->
				<!-- 人数 e -->
				
				<!-- 组队 s -->
				<view v-if="current === 3">
					<view class="uni-list">
						<view class="uni-list-cell uni-list-cell-pd">
							<view class="uni-list-cell-left">创建团队</view>
							<view><text class="uni-icon uni-icon-plus-filled red" @click="createTeam()"></text></view>
						</view>
						<view class="uni-list-cell uni-list-cell-pd" v-if="teams">
							<view class="uni-list-cell-left">加入团队</view>
							<view class="uni-list-cell-db">
								<picker @change="pickerChangeTeam" :value="teamArray[teamIndex].team_id" :range="teamArray" range-key="team">
									<view class="uni-flex uni-row" style="-webkit-justify-content: flex-end;justify-content: flex-end;">
										<text class="text uni-bold">{{ teamArray[teamIndex].team }}</text>
										<text class="uni-icon uni-icon-arrowdown"></text>
									</view>
								</picker>
							</view>
						</view>
					</view>
				</view>
				<!-- 组队 e -->
				
				<!-- 装备 s -->
				<view v-if="current === 4">
					<view class="uni-list">
						<view class="uni-list-cell uni-list-cell-pd">
							<view class="uni-list-cell-left">装备</view>
							<view class="uni-list-cell-db">
								<picker @change="pickerChangeEquipment" :value="equipmentArray[equipmentIndex].equipment_id" :range="equipmentArray" range-key="equipment">
									<view class="uni-flex uni-row" style="-webkit-justify-content: flex-end;justify-content: flex-end;">
										<view class="text uni-bold">
											<text>{{ equipmentArray[equipmentIndex].equipment_name }}</text>
											<text class="red">￥{{ equipmentArray[equipmentIndex].use_fee ? equipmentArray[equipmentIndex].use_fee : 0 }}</text>
										</view>
										<text class="uni-icon uni-icon-arrowdown"></text>
									</view>
								</picker>
							</view>
						</view>
					</view>
				</view>
				<!-- 装备 e -->
				
				<view v-if="current === 5">
					比赛费用<text class="red uni-bold">￥{{ price }}</text>，确认支付
				</view>
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
			uniCalendar, // Calendar 日历
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
				
				/* 选择场次 s */
				/* 可预订日期 s */
				showCalendar: false,
				timeInfo: {
					date: '', // 当前时间，默认为今天
					startDate: '2019-06-15',
					endDate: '2020-10-15',
					lunar: true, // 显示农历
					// range: true, // 范围选择，默认为false
					insert: false,
					selected: []
				},
				sessionDate: '', // 比赛场次日期
				/* 可预订日期 e */
				
				/* 场次 s */
				sessionArray: [{}], //场景列表，如：[{session_id: 1, session_time: '10:00~11:00', session_price: 1.00, session: '10:00~11:00' + ' ￥1.00'}, {…}]
				sessionIndex: 0,
				/* 场次 e */
				
				sessionId: '', // 选中的场次ID
				/* 选择场次 s */
				
				/* 选择房间 s */
				roomList: [], // 场景房间列表，如：[{room_id: '1', room_name: 'room1', scene_id: '1', room_price: 1.00}, {…}]
				currentRoom: '',
				
				roomId: '', // 选中的房间ID
				/* 选择房间 e */
				
				/* 选择人数 s */
				number: 1, // 人数
				/* 选择人数 e */
				
				/* 选择比赛场次组队 e */
				/* 比赛场次组队 s */
				teamArray: [{}], // 比赛场次组队列表，如：[{team_id: 1, team: '1'}, {team_id: 2, team: '2'}, {…}]
				teamIndex: 0,
				/* 比赛场次组队 e */
				
				teamId: '', // 选中的比赛场次组队ID
				
				teams: [], // 不同于teamArray
				/* 选择比赛场次组队 e */
				
				/* 选择装备 e */
				/* 装备 s */
				equipmentArray: [{}], // 装备列表，如：[{equipment_id: 1, equipment_name: 'EQP001', use_fee: 1.00, equipment: 'EQP001' + ' ￥1.00'}, {…}]
				equipmentIndex: 0,
				/* 装备 e */
				
				equipmentId: '', // 选中的装备ID
				/* 选择装备 e */
				
				/* 确认支付 s */
				price: 0, // 比赛费用
				/* 确认支付 e */
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
				// 选好第1步外，其他步骤必须判断是否选择比赛日期、场次
				if (!this.sessionDate && this.active !== 0) {
					uni.showToast({
						title: '请选择比赛日期',
						icon: 'none'
					});
					this.current = this.active = 1;
					return;
				}
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
				var checked = this.sceneId = e.target.value; // 选中的场景ID
				console.log('场景sceneId = ', this.sceneId);
				
				// 初始化选中的比赛场次日期、场次ID、房间ID、组队ID、装备ID、比赛费用
				this.sessionDate = ''; this.sessionId = ''; this.sessionIndex = 0;
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
			
			/* 选择场次 s */
			/* 日期 s */
			openCalendar() {
				this.$refs.calendar.open()
			},
			confirmDate(e) {
				console.log('confirmDate 返回:', e)
				this.sessionDate = e.fulldate;
			},
			/* 日期 e */
			
			/* 场次 s */
			/**
			 * 获取场次列表
			 * @param {Object} venueId
			 * @param {Object} sceneId
			 */
			getSessionList(venueId, sceneId) {
				let self = this;
				uni.request({
				    url: this.$serverUrl + 'session',
				    data: {
				        venue_id: venueId,
						scene_id: sceneId
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
						// 场次列表
						let sessionArray = res.data.data.data;
						if (sessionArray) {
							sessionArray.unshift({}); // 向数组 sessionArray 的开头添加一个空的 json{} 元素
							sessionArray.forEach ((item, index) => {
								// 场次时间
								item.session_time = item.start_time + '~' + item.end_time;
								// 场次时间、价格
								item.session = item.start_time + '~' + item.end_time + ' ￥' + item.session_price;
								if (0 == index) {
									item.session_time = '';
									item.session = '请选择…';
								}
							})
							self.sessionArray = sessionArray;
						}
				    }
				});
			},
			
			/**
			 * 选择场次
			 * @param {Object} e
			 */
			pickerChangeSession: function(e) {
				// console.log('picker发送选择改变，携带值为：' + e.target.value)
				this.sessionIndex = e.target.value
				
				this.sessionId = this.sessionArray[this.sessionIndex].session_id; // 选中的场次ID
				console.log('比赛场次：sessionDate=' + this.sessionDate, 'sessionId=' + this.sessionId)
				
				// 初始化比赛场次组队
				this.teams = []; this.teamArray = [{}]; this.teamIndex = 0; this.teamId = '';
			},
			/* 场次 e */
			/* 选择场次 e */
			
			/* 选择房间 s */
			/**
			 * 获取房间列表
			 * @param {Object} venueId
			 * @param {Object} sceneId
			 */
			getSceneRoomList(venueId, sceneId) {
				let self = this;
				uni.request({
				    url: this.$serverUrl + 'scene_room',
				    data: {
				        venue_id: venueId,
						scene_id: sceneId
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
						// 房间列表
						let roomList = res.data.data.data;
						roomList.forEach ((item, index) => {
							item.room_id = String(item.room_id);
						})
						self.roomList = roomList;
				    }
				});
			},
			
			/**
			 * 选择房间
			 * @param {Object} evt
			 */
			radioChangeRoom(evt) {
				for (let i = 0; i < this.roomList.length; i++) {
					if (this.roomList[i].room_id === evt.target.value) {
						this.currentRoom = i;
						this.roomId = evt.target.value;
						console.log('房间roomId = ', this.roomId);
						
						// 先初始化比赛场次组队，再获取比赛场次组队列表
						this.teams = []; this.teamArray = [{}]; this.teamIndex = 0; this.teamId = '';
						this.getTeamList(this.venueId, this.sceneId, this.sessionDate, this.sessionId, this.roomId);
						// 初始化比赛费用
						this.price = 0;
						
						break;
					}
				}
			},
			/* 选择房间 e */
			
			/* 选择人数 s */
			/**
			 * 选择人数
			 * @param {Object} value
			 */
			/* changeNumber(value) {
				this.number = value
			}, */
			/* 选择人数 e */
			
			/* 选择组队 s */
			/**
			 * 获取比赛场次组队列表
			 * @param {Object} venueId
			 * @param {Object} sceneId
			 * @param {Object} sessionDate
			 * @param {Object} sessionId
			 * @param {Object} roomId
			 */
			getTeamList(venueId, sceneId, sessionDate, sessionId, roomId) {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'session_teams',
					data: {
						venue_id: venueId,
						scene_id: sceneId,
						session_date: sessionDate,
						session_id: sessionId,
						room_id: roomId
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
						// 比赛场次组队列表
						let teams = self.teams = res.data.data;
						if (teams) {
							let teamArray = JSON.parse(teams.session_teams_detail);
							if (teamArray) {
								teamArray.unshift({});
								teamArray.forEach ((item, index) => {
									item.team = String('team-' + item.team_id + ' （已加入' + item.players_number + '人）');
									if (0 == index) {
										item.team = '请选择…';
									}
								})
								self.teamArray = teamArray;
							}
							
						}
					}
				});
			},
			
			/**
			 * 选择组队
			 * @param {Object} e
			 */
			pickerChangeTeam: function(e) {
				// console.log('picker发送选择改变，携带值为：' + e.target.value)
				this.teamIndex = e.target.value
				
				this.teamId = this.teamArray[this.teamIndex].team_id; // 选中的比赛场次组队ID
				// console.log('组队teamId=' + this.teamId)
				
				// 加入团队
				this.joinTeam();
			},
			
			/**
			 * 加入团队
			 */
			joinTeam () {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'session_teams/' + this.teams.session_teams_id,
					data: {
						session_date: self.sessionDate,
						session_teams_id: this.teams.session_teams_id,
						team_id: this.teamId,
						user_id: this.userInfo.user_id,
						number: this.number
					},
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					method: 'PUT',
					success: (res) => {
						// console.log('joinTeam返回', res.data);
						// TODO：从数据库获取已加入比赛场次的相关信息，如人数、团队ID
						self.teamId = res.data.data.team_id;
						console.log('joinTeam返回组队teamId=' + self.teamId)
						uni.showToast({
							title: res.data.message,
							icon: 'none'
						});
					}
				});
			},
			
			/**
			 * 创建团队
			 */
			createTeam () {
				let self = this;
				uni.showModal({
					title: '创建团队',
					content: '创建新的比赛团队',
					success: function (res) {
						if (res.confirm) {
							uni.request({
								url: self.$serverUrl + 'session_teams',
								data: {
									venue_id: self.venueId,
									scene_id: self.sceneId,
									session_date: self.sessionDate,
									session_id: self.sessionId,
									room_id: self.roomId,
									user_id: self.userInfo.user_id,
									number: self.number
								},
								header: {
									'sign': common.sign(), // 验签
									'version': getApp().globalData.version, // 应用大版本号
									'model': getApp().globalData.systemInfo.model, // 手机型号
									'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
									'did': getApp().globalData.did, // 设备号
								},
								method: 'POST',
								success: (res) => {
									// console.log('createTeam返回', res.data);
									// TODO：从数据库获取已加入比赛场次的相关信息，如人数、团队ID
									self.teamId = res.data.data.team_id;
									console.log('createTeam返回组队teamId=' + self.teamId)
									uni.showToast({
										title: res.data.message,
										icon: 'none'
									});
									
									// 获取比赛场次组队列表
									self.getTeamList(self.venueId, self.sceneId, self.sessionDate, self.sessionId, self.roomId);
								}
							});
						}
					}
				});
			},
			/* 选择组队 e */
			
			/* 选择装备 s */
			/**
			 * 获取装备列表
			 * @param {Object} venueId
			 * @param {Object} sceneId
			 */
			getEquipmentList(venueId, sceneId) {
				let self = this;
				uni.request({
				    url: this.$serverUrl + 'equipment',
				    data: {
				        venue_id: venueId,
						scene_id: sceneId,
						equipment_type: 1 // 付费装备
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
						// 装备列表
						let equipmentArray = res.data.data.data;
						if (equipmentArray) {
							equipmentArray.unshift({}); // unshift()方法在数组的开头添加一个或多个元素
							equipmentArray.forEach ((item, index) => {
								item.equipment = item.equipment_name + ' ￥' + item.use_fee;
								if (0 == index) {
									item.equipment = '请选择…';
								}
							})
						}
						self.equipmentArray = equipmentArray != '' ? equipmentArray : [{}];
				    }
				});
			},
			
			/**
			 * 选择装备
			 * @param {Object} e
			 */
			pickerChangeEquipment: function(e) {
				// console.log('picker发送选择改变，携带值为：' + e.target.value)
				this.equipmentIndex = e.target.value
				
				// 当前选中的装备
				let currentEquipment = this.equipmentArray[this.equipmentIndex];
				this.equipmentId = currentEquipment.equipment_id; // 选中的装备ID
				
				console.log('装备equipmentId = ', this.equipmentId)
			},
			/* 选择装备 e */
			
			/**
			 * 生成订单并跳转支付页面
			 */
			createOrder() {
				// 生成订单
				uni.request({
					url: this.$serverUrl + 'session_order',
					data: {
						user_id: this.userInfo.user_id,
						session_teams_id: this.teams.session_teams_id,
						team_id: this.teamId,
						venue_id: this.venueId,
						scene_id: this.sceneId,
						session_date: this.sessionDate,
						session_id: this.sessionId,
						start_time: this.sessionDate + ' ' + this.sessionArray[this.sessionIndex].start_time,
						end_time: this.sessionDate + ' ' + this.sessionArray[this.sessionIndex].end_time,
						session_price: this.sessionArray[this.sessionIndex].session_price,
						room_id: this.roomId,
						room_price: parseFloat(this.roomList[this.currentRoom].room_price),
						equipment_id: this.equipmentArray[this.equipmentIndex].equipment_id,
						equipment_use_fee: this.equipmentArray[this.equipmentIndex].use_fee ? parseFloat(this.equipmentArray[this.equipmentIndex].use_fee) : 0,
						order_price: this.price
					},
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
						'access-user-token': this.userInfo.token
					},
					method: 'POST',
					success: (res) => {
						if (res.statusCode == 201) { // 生成订单成功
							// 跳转支付页面
							uni.navigateTo({
								url: '/pages/API/request-payment/request-payment?price=' + this.price
							})
						} else { // 生成订单失败
							uni.showModal({
								title: '订单生成失败',
								content: res.data.message,
								showCancel: false,
								success:function(){
									// 跳转个人中心，查看比赛订单
									if (res.data.message == "已加入该场比赛") {
										uni.showModal({
											title: '查看比赛订单',
											// content: res.data.message,
											success:function(res1){
												if (res1.confirm) {
													uni.switchTab({
														url: '/pages/user/user'
													})
												}
											}
										});
									}
								}
							});
						}
					}
				})
			}
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
