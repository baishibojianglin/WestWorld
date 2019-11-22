<template>
	<view>
		<view class="example-title"><!-- 报名步骤 --> 
			<image :src="venueData.thumb" style="width: 80upx; height: 80upx; border-radius: 10upx;"></image>
			<view>{{venueData.venue_name}}</view>
			<view><text class="uni-icon uni-icon-location"></text>{{venueData.address}}</view>
		</view>
		<view class="example-body">
			<uni-steps :options="stepsList" :active="active" />
		</view>
		
		<view class="example-title">{{active === 6 ? '' : '选择'}}{{ items[active] }}</view>
		<view class="example-body">
			<view class="content uni-common-mb">
				<!-- 场景 s -->
				<view v-show="current === 0">
					
					<radio-group @change="radioChangeScene">
					
						<uni-grid :column="3" :highlight="true"><!-- @change="scene" -->
							<uni-grid-item v-for="(item, index) in sceneList" :key="index" :style="{background: 'url(' + item.thumb +')'}">
								<!-- <image :src="item.thumb" class="image" mode="aspectFill" /> -->
								<view class="scene_name">
									<radio :id="'scene' + item.scene_id" :value="item.scene_id" :checked="item.checked"></radio>
									<label :for="'scene' + item.scene_id"><text class="text uni-bold">{{ item.scene_name }}</text></label>
								</view>
							</uni-grid-item>
						</uni-grid>
						
					</radio-group>
					
				</view>
				<!-- 场景 e -->
				<!-- 场次 s -->
				<view v-show="current === 1">
					<view class="uni-flex uni-row uni-common-pl">
						<view>可预订日期</view>
						<view>
							<view class="tag-view">
								<uni-tag :text="startDate" type="" size="small"/>
								<uni-tag text="~" type="" size="small"/>
								<uni-tag :text="endDate" type="" size="small"></uni-tag>
							</view>
						</view>
					</view>
					<view class="uni-common-mt">
						<!-- <view class="example-title">选择日期</view> -->
						<view class="uni-list">
							<view class="uni-list-cell uni-list-cell-pd">
								<view class="uni-list-cell-left">日期</view>
								<view class="uni-list-cell-db" @click="openCalendar">
									<view class="tag-view"><uni-tag :text="timeData.fulldate" type="success" size="small"></uni-tag></view>
								</view>
							</view>
						</view>
					</view>
					<view class="uni-common-mt">
						<!-- <view class="example-title">选择场次</view> -->
						<view class="uni-list">
							<view class="uni-list-cell uni-list-cell-pd">
								<view class="uni-list-cell-left">场次</view>
								<view class="uni-list-cell-db">
									<picker @change="bindPickerChange" :value="sessionArray[sessionIndex].session_id" :range="sessionArray" range-key="session">
										<!-- <view class="uni-input">{{sessionArray[sessionIndex].session}}</view> -->
										<view class="tag-view"><uni-tag :text="sessionArray[sessionIndex].session_time" type="success" size="small"></uni-tag> <text class="red uni-bold">￥{{ sessionArray[sessionIndex].session_price }}</text></view>
									</picker>
								</view>
							</view>
						</view>
					</view>
				</view>
				<!-- 场次 e -->
				<!-- 房间 s -->
				<view v-show="current === 2">
					<view class="uni-list">
						<radio-group @change="radioChangeRoom">
							<label class="uni-list-cell uni-list-cell-pd" v-for="(item, index) in roomList" :key="item.value">
								<view>
									<radio :value="item.room_id" :checked="index === currentRoom" />
								</view>
								<view>{{item.room_name}} <text class="red uni-bold">￥{{item.room_price}}</text></view>
							</label>
						</radio-group>
					</view>
				</view>
				<!-- 房间 e -->
				<!-- 人数 s -->
				<view v-show="current === 3">
					<uni-number-box :min="0" :max="3" :value="number" @change="changeNumber"></uni-number-box>
				</view>
				<!-- 人数 e -->
				<!-- 组队 s -->
				<view v-show="current === 4">
					<view class="uni-list">
						<view class="uni-list-cell uni-list-cell-pd">
							<view class="uni-list-cell-left">创建团队</view>
							<view><text class="uni-icon uni-icon-plus-filled red" @click="createTeam()"></text></view>
						</view>
						<view class="uni-list-cell uni-list-cell-pd" v-if="teamArray.length > 1">
							<view class="uni-list-cell-left">加入团队</view>
							<view class="uni-list-cell-db">
								<picker @change="pickerChangeTeam" :value="teamArray[teamIndex].team_id" :range="teamArray" range-key="team">
									<view class="tag-view"><uni-tag :text="teamArray[teamIndex].team" type="success" size="small"></uni-tag></view>
								</picker>
							</view>
						</view>
					</view>
				</view>
				<!-- 组队 e -->
				<!-- 装备 s -->
				<view v-show="current === 5">
					<view class="uni-list">
						<view class="uni-list-cell uni-list-cell-pd">
							<view class="uni-list-cell-left">装备</view>
							<view class="uni-list-cell-db">
								<picker @change="pickerChangeEquipment" :value="equipmentArray[equipmentIndex].equipment_id" :range="equipmentArray" range-key="equipment">
									<view class="tag-view"><uni-tag :text="equipmentArray[equipmentIndex].equipment_name" type="success" size="small"></uni-tag> <text class="red uni-bold">￥{{ equipmentArray[equipmentIndex].use_fee }}</text></view>
								</picker>
							</view>
						</view>
					</view>
				</view>
				<!-- 装备 e -->
				<view v-show="current === 6">
					入场费<text class="red uni-bold">￥{{ price }}</text>，确认支付
				</view>
			</view>
		</view>
		
		<view class="example-title">
			<uni-segmented-control :current="current" :values="items" :style-type="styleType" :active-color="activeColor" @clickItem="onClickItem" />
		</view>
		
		<button type="default" @click="change">{{ active === 6 ? '确 定' : '下一步' }}</button>
		
		<!-- 单独放在外面防止其他样式对其干扰 -->
		<uni-calendar ref="calendar" :lunar="tags[0].checked" :disable-before="tags[3].checked" :range="tags[5].checked" :start-date="startDate" :end-date="endDate" :date="date" :selected="selected" @confirm="confirmDate" @change="changeDate()" />
	</view>
</template>

<script>
	import {uniSteps, uniSegmentedControl, uniGrid, uniGridItem, uniCalendar, uniTag, uniNumberBox} from '@dcloudio/uni-ui';
	import {mapState} from 'vuex';
	import common from '@/common/common.js';
	import Aes from '@/common/Aes.js';

	export default {
		components: {
			uniSteps, // Steps 步骤条
			uniSegmentedControl, // SegmentedControl 分段器
			uniGrid, uniGridItem, // Grid 宫格
			uniCalendar, // Calendar 日期
			uniTag, // Tag 标签
			uniNumberBox, // NumberBox 数字输入框
		},
		data() {
			/* 日期 s */
			/**
			 * 时间计算
			 */
			function getDate(date, AddMonthCount = 0, AddDayCount = 0) {
				if (typeof date !== 'object') {
					date = date.replace(/-/g, '/')
				}
				let dd = new Date(date)
				dd.setMonth(dd.getMonth() + AddMonthCount) // 获取AddDayCount天后的日期
				dd.setDate(dd.getDate() + AddDayCount) // 获取AddDayCount天后的日期
				let y = dd.getFullYear()
				let m = dd.getMonth() + 1 < 10 ? '0' + (dd.getMonth() + 1) : dd.getMonth() + 1 // 获取当前月份的日期，不足10补0
				let d = dd.getDate() < 10 ? '0' + dd.getDate() : dd.getDate() // 获取当前几号，不足10补0
				return y + '-' + m + '-' + d
			}
			let tags = [{
					id: 0,
					name: '农历',
					checked: false,
					attr: 'lunar'
				},
				{
					id: 1,
					name: '开始日期(' + getDate(new Date(), 0) + ')',
					checked: true,
					value: getDate(new Date(), 0),
					attr: 'startDate'
				},
				{
					id: 2,
					name: '结束日期(' + getDate(new Date(), 0, 7) + ')',
					value: getDate(new Date(), 0, 7),
					checked: true,
					attr: 'endDate'
				},
				{
					id: 3,
					name: '禁用今天之前的日期',
					checked: false,
					attr: 'disableBefore'
				},
				{
					id: 4,
					name: '自定义当前日期(' + getDate(new Date(), 1) + ')',
					value: getDate(new Date(), 1),
					checked: false,
					attr: 'date'
				},
				{
					id: 5,
					name: '范围选择',
					checked: false,
					attr: 'range'
				},
				{
					id: 6,
					name: '打点',
					value: [{
							date: getDate(new Date(), 0, -1),
							info: '打卡'
						},
						{
							date: getDate(new Date(), 0),
							info: '签到',
							data: {
								custom: '自定义信息',
								name: '自定义消息头'
							}
						},
						{
							date: getDate(new Date(), 0, 1),
							info: '已打卡'
						}
					],
					checked: false,
					attr: 'selected'
				}
			]
			/* 日期 s */
			
			return {
				userData: [], // 用户信息
				
				venueId: '', // 场馆ID
				venueData: {}, // 场馆信息
				
				/* 步骤条 s */
				active: 0,
				stepsList: [
					{title: '1', desc: '场景'}, 
					{title: '2', desc: '场次'}, 
					{title: '3', desc: '组队'}, 
					{title: '4', desc: '房间'}, 
					{title: '5', desc: '人数'}, 
					{title: '6', desc: '装备'}, 
					{title: '7', desc: '确定'},
				],
				/* 步骤条 e */
				
				/* 分段器 s */
				items: ['场景', '场次', '房间', '人数', '组队', '装备', '确定'],
				colors: ['#007aff', '#4cd964', '#dd524d'],
				current: 0,
				colorIndex: 0,
				activeColor: '#4cd964',
				styleType: 'text',
				/* 分段器 e */
				
				/* 选择场景 s */
				sceneList: [], // 场景列表，如：[{scene_id: '1', scene_name: '城市city', thumb: '/static/img/home.png'}, {scene_id: '2', scene_name: '乡村country', thumb: '/static/img/home.png'}]
				
				sceneId: '', // 选中的场景ID
				/* 选择场景 e */
				
				/* 选择场次 s */
				/* 可预订日期 s */
				tags,
				date: '',
				startDate: tags[1].value,
				endDate: tags[2].value,
				timeData: {
					clockinfo: '',
					date: '',
					fulldate: getDate(new Date()),
					lunar: '',
					month: '',
					range: '',
					year: ''
				},
				selected: [],
				infoShow: false,
				showCalendar: false,
				/* 可预订日期 e */
				
				/* 场次 s */
				sessionArray: [{}], //场景列表，如：[{session_id: 1, session_time: '10:00~11:00', session_price: 1.00, session: '10:00~11:00' + ' ￥1.00'}, {…}]
				sessionIndex: 0,
				/* 场次 e */
				
				sessionId: '', // 选中的场次ID
				/* 选择场次 e */
				
				/* 选择房间 s */
				roomList: [], // 场景房间列表，如：[{room_id: '1', room_name: 'room1', scene_id: '1', room_price: 1.00}, {…}]
				currentRoom: '',
				
				roomId: '', // 选中的房间ID
				/* 选择房间 e */
				
				/* 选择人数 s */
				number: '', // 人数
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
				price: '', // 入场费
				/* 确认支付 e */
			}
		},
		onLoad(event) {
			// console.log(event)
			this.venueId = event.id; // 场馆ID
			this.venueData = JSON.parse(event.venueData); // 场馆数据
			this.getSceneList(this.venueId); // 获取场景列表
			
			this.getUserInfo(); // 获取用户信息
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			/**
			 * 获取用户信息
			 */
			getUserInfo () {
				let self = this;
				if (this.hasLogin) {
					uni.request({
						url: this.$serverUrl + 'user/1',
						header: {
							'sign': common.sign(), // 签名
							'version': getApp().globalData.version, // 应用大版本号
							'model': getApp().globalData.systemInfo.model, // 手机型号
							'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
							'did': getApp().globalData.did, // 设备号
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success:function(res){
							let userData = JSON.parse(Aes.decode(res.data.data)); // 用户信息
							// console.log('用户信息：', userData);
							self.userData = userData;
						}
					})
				}
			},
			
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
				// 选好第1~3步外，其他步骤必须判断是否选择人数
				if ((this.number < 1 || this.number > 3) && this.active > 2) {
					uni.showToast({
						title: '请输入1~3人',
						icon: 'none'
					});
					this.current = this.active = 3;
					return;
				}
				// 选好第1~4步外，其他步骤必须判断是否选择组队
				if (!this.teamId && this.active > 3) {
					uni.showToast({
						title: '请创建或加入团队',
						icon: 'none'
					});
					this.current = this.active = 4;
					return;
				}
				// 选好第1~5步外，其他步骤必须判断是否选择装备
				if (!this.equipmentId && this.active > 4) {
					uni.showToast({
						title: '请选择装备',
						icon: 'none'
					});
					this.current = this.active = 5;
					return;
				}
				// 计算入场费
				if (this.active >= 5) {
					// 计算入场费
					var session_price = parseFloat(this.sessionArray[this.sessionIndex].session_price);
					var room_price = parseFloat(this.roomList[this.currentRoom].room_price);
					var number = parseInt(this.number);
					var use_fee = parseFloat(this.equipmentArray[this.equipmentIndex].use_fee);
					this.price = ((session_price + room_price) * number + use_fee).toFixed(2);
				}
				// 发起支付
				if (this.active == 6) {
					let self = this;
					uni.showModal({
						title: '发起支付',
						success:function(res){
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/API/request-payment/request-payment?price=' + self.price
								})
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
			onClickItem(index) {
				if (this.current !== index) {
					// 分段器
					this.current = index
					
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
				    	'sign': common.sign(), // 签名
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
				
				// 初始化选中的场次ID、房间ID、组队ID、装备ID、入场费
				this.sessionId = ''; this.sessionIndex = 0;
				this.roomId = ''; this.currentRoom = '';
				this.teams = []; this.teamArray = [{}]; this.teamIndex = 0; this.teamId = '';
				this.equipmentId = ''; this.equipmentIndex = 0;
				this.price = '';
				
				this.getSessionList(this.venueId, this.sceneId); // 获取场次列表
				this.getSceneRoomList(this.venueId, this.sceneId); // 获取房间列表
				this.getEquipmentList(this.venueId, this.sceneId); // 获取装备列表
			},
			/* 选择场景 e */
			
			/* 选择场次 s */
			/* 日期 s */
			openCalendar() {
				this.reckon()
				this.$refs.calendar.open()
			},
			reckon() {
				if (this.tags[1].checked) {
					this.startDate = this.tags[1].value
				} else {
					this.startDate = ''
				}
				if (this.tags[2].checked) {
					this.endDate = this.tags[2].value
				} else {
					this.endDate = ''
				}
				if (this.tags[4].checked) {
					this.date = this.tags[4].value
				} else {
					this.date = ''
				}
				if (this.tags[6].checked) {
					this.selected = this.tags[6].value
				} else {
					this.selected = []
				}
			},
			changeDate(e) {
				console.log('change 返回:', e)
				this.timeData = e
				this.infoShow = true
			},
			confirmDate(e) {
				// 处理日期格式
				e.month = (Array(2).join(0) + e.month).slice(-2); // 获取当前日期的月份，不足10补0
				e.date = e.date < 10 ? '0' + (e.date) : e.date; // 获取当前日期的号数，不足10补0
				e.fulldate = e.year + '-' + e.month + '-' + e.date;
				
				console.log('confirm 返回:', e)
				this.timeData = e
				this.infoShow = true
				
				// TODO：根据日期显示场次
				if (e.fulldate) {
					
				}
			},
			retract() {
				this.infoShow = !this.infoShow
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
				    	'sign': common.sign(), // 签名
				    	'version': getApp().globalData.version, // 应用大版本号
				    	'model': getApp().globalData.systemInfo.model, // 手机型号
				    	'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
				    	'did': getApp().globalData.did, // 设备号
				    },
				    success: (res) => {
				        // console.log(res.data);
						// 场次列表
						let sessionArray = res.data.data.data;
						sessionArray.forEach ((item, index) => {
							// 场次时间
							item.session_time = item.start_time + '~' + item.end_time;
							// 场次时间、价格
							item.session = item.start_time + '~' + item.end_time + ' ￥' + item.session_price;
						})
						self.sessionArray = sessionArray;
				    }
				});
			},
			
			/**
			 * 选择场次
			 * @param {Object} e
			 */
			bindPickerChange: function(e) {
				// console.log('picker发送选择改变，携带值为：' + e.target.value)
				this.sessionIndex = e.target.value
				
				this.sessionId = this.sessionArray[this.sessionIndex].session_id; // 选中的场次ID
				console.log('比赛场次：sessionDate=' + this.timeData.fulldate, 'sessionId=' + this.sessionId)
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
				    	'sign': common.sign(), // 签名
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
						this.getTeamList(this.venueId, this.sceneId, this.timeData.fulldate, this.sessionId, this.roomId);
						
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
			changeNumber(value) {
				this.number = value
			},
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
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					success: (res) => {
						// console.log(res.data);
						// 比赛场次组队列表
						let teams = self.teams = res.data.data;
						let teamArray = JSON.parse(teams.session_teams_detail);
						teamArray.forEach ((item, index) => {
							item.team = String('team-' + item.team_id + ' （已加入' + item.players_number + '人）');
						})
						self.teamArray = teamArray;
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
				uni.request({
					url: this.$serverUrl + 'session_teams/' + this.teams.session_teams_id,
					data: {
						session_teams_id: this.teams.session_teams_id,
						team_id: this.teamId,
						user_id: this.userData.user_id,
						number: this.number
					},
					header: {
						'sign': common.sign(), // 签名
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					method: 'PUT',
					success: (res) => {
						console.log('joinTeam返回', res.data);
						uni.showToast({
							title: res.data.message,
							icon: 'none'
						});
						// TODO：从数据库获取已加入比赛场次的相关信息，如人数、团队ID
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
									session_date: self.timeData.fulldate,
									session_id: self.sessionId,
									room_id: self.roomId,
									user_id: self.userData.user_id,
									number: self.number
								},
								header: {
									'sign': common.sign(), // 签名
									'version': getApp().globalData.version, // 应用大版本号
									'model': getApp().globalData.systemInfo.model, // 手机型号
									'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
									'did': getApp().globalData.did, // 设备号
								},
								method: 'POST',
								success: (res) => {
									console.log('joinTeam返回', res.data);
									uni.showToast({
										title: res.data.message,
										icon: 'none'
									});
									// TODO：从数据库获取已加入比赛场次的相关信息，如人数、团队ID
								}
							});
						}
					}
				});
			},
			/* 选择组队 e */
			
			/* 选择装备 s */
			/**
			 * 获取场次列表
			 * @param {Object} venueId
			 * @param {Object} sceneId
			 */
			getEquipmentList(venueId, sceneId) {
				let self = this;
				uni.request({
				    url: this.$serverUrl + 'equipment',
				    data: {
				        venue_id: venueId,
						scene_id: sceneId
				    },
				    header: {
				    	'sign': common.sign(), // 签名
				    	'version': getApp().globalData.version, // 应用大版本号
				    	'model': getApp().globalData.systemInfo.model, // 手机型号
				    	'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
				    	'did': getApp().globalData.did, // 设备号
				    },
				    success: (res) => {
				        // console.log(res.data);
						// 装备列表
						let equipmentArray = res.data.data.data;
						equipmentArray.forEach ((item, index) => {
							item.equipment = item.equipment_name + ' ￥' + item.use_fee;
						})
						self.equipmentArray = equipmentArray;
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
				
				this.equipmentId = this.equipmentArray[this.equipmentIndex].equipment_id; // 选中的装备ID
				console.log('装备equipmentId = ', this.equipmentId)
			},
			/* 选择装备 e */
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
		background: #fff
	}

	.example-info {
		padding: 30upx;
		color: #3b4144;
		background: #fff
	}

	button {
		margin: 30upx;
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
		padding: 10upx;
		border-radius: 10upx;
	}
	
	.tag-view {
		margin: 10upx 20upx;
		display: inline-block;
	}
	
	.uni-icon-clear {
		color: #999;
	}
</style>
