<template>
	<view>
		<view class="example-title">报名步骤 <image :src="venueData.thumb" style="width: 80upx; height: 80upx; border-radius: 10upx;"></image></view>
		<view class="example-body">
			<uni-steps :options="stepsList" :active="active" />
		</view>
		
		<view class="example-title">{{active === 5 ? '' : '选择'}}{{ items[active] }}</view>
		<view class="example-body">
			<view class="content uni-common-mb">
				<view v-show="current === 0">
					
					<radio-group @change="changeRadio">
					
						<uni-grid :column="3" :highlight="true"><!-- @change="scene" -->
							<uni-grid-item v-for="(item, index) in sceneList" :key="index">
								<image :src="item.thumb" class="image" mode="aspectFill" />
								<text class="text">{{ item.text }}</text>
								
								<radio :id="item.scene_id" :value="item.scene_id" :checked="item.checked"></radio>
								
							</uni-grid-item>
						</uni-grid>
						
					</radio-group>
					
				</view>
				<view v-show="current === 1">
					<view class="uni-common-pl" style="display: flex">
						<text style="margin-top: 20upx;">可预订日期</text>
						<view class="tag-view">
							<uni-tag :text="startDate" type="success" />
							<text>~</text>
							<uni-tag :text="endDate" type="success"></uni-tag>
						</view>
						<!-- <button type="warn" size="mini" plain="true">{{ startDate }} ~ {{ endDate }}</button> -->
					</view>
					<view style="display: flex">
						<button type="button" size="mini" @click="openCalendar">选择日期</button>
						<text style="margin-top: 40upx;">{{ timeData.fulldate }}</text>
					</view>
					<view style="display: flex">
						<button type="button" size="mini" @click="openCalendar">选择场次</button>
						<text style="margin-top: 40upx;">{{ timeData.fulldate }}</text>
					</view>
				</view>
				<view v-show="current === 2">
					选项卡3的内容
				</view>
				<view v-show="current === 3">
					选项卡4的内容
				</view>
				<view v-show="current === 4">
					选项卡5的内容
				</view>
				<view v-show="current === 5">
					选项卡6的内容
				</view>
			</view>
			<uni-segmented-control :current="current" :values="items" :style-type="styleType" :active-color="activeColor" @clickItem="onClickItem" />
		</view>
		
		<button type="primary" @click="change">{{ active === 5 ? '确定' : '下一步' }}</button>
		
		<!-- 单独放在外面防止其他样式对其干扰 -->
		<uni-calendar ref="calendar" :lunar="tags[0].checked" :disable-before="tags[3].checked" :range="tags[5].checked" :start-date="startDate" :end-date="endDate" :date="date" :selected="selected" @confirm="confirmDate" @change="changeDate()" />
	</view>
</template>

<script>
	import {uniSteps, uniSegmentedControl, uniGrid, uniGridItem, uniCalendar, uniTag} from '@dcloudio/uni-ui'

	export default {
		components: {
			uniSteps, // Steps 步骤条
			uniSegmentedControl, // SegmentedControl 分段器
			uniGrid, uniGridItem, // Grid 宫格
			uniCalendar, // Calendar 日期
			uniTag, // Tag 标签
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
					name: '结束日期(' + getDate(new Date(), 0, 5) + ')',
					value: getDate(new Date(), 0, 5),
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
				venueData: {}, // 场馆信息
				
				/* 步骤条 s */
				active: 0,
				stepsList: [{
					title: '1',
					desc: '场景'
				}, {
					title: '2',
					desc: '场次'
				}, {
					title: '3',
					desc: '价格'
				}, {
					title: '4',
					desc: '人数'
				}, {
					title: '5',
					desc: '设备'
				}, {
					title: '6',
					desc: '确定'
				}],
				/* 步骤条 e */
				
				/* 分段器 s */
				items: ['场景', '场次', '价格', '人数', '设备', '确定'],
				colors: ['#007aff', '#4cd964', '#dd524d'],
				current: 0,
				colorIndex: 0,
				activeColor: '#4cd964',
				styleType: 'text',
				/* 分段器 e */
				
				/* 选择场景 s */
				sceneList: [{
						scene_id: '1',
						scene_name: 'city',
						thumb: '../../static/img/home.png',
						text: '城市',
						value: '值'
					},
					{
						scene_id: '2',
						scene_name: 'country',
						thumb: '../../static/img/home.png',
						text: '乡村',
						value: '值',
						// checked: ''
					},
					{
						scene_id: '3',
						scene_name: '3',
						thumb: '../../static/img/home.png',
						text: '草原'
					},
					{
						scene_id: '4',
						scene_name: '4',
						thumb: '../../static/img/home.png',
						text: '森林'
					},
					{
						scene_id: '5',
						scene_name: '5',
						thumb: '../../static/img/home.png',
						text: '沙漠'
					},
					{
						scene_id: '6',
						scene_name: '6',
						thumb: '../../static/img/home.png',
						text: '雪地'
					}
				],
				
				sceneId: '', // 选中的场景ID
				/* 选择场景 e */
				
				/* 选择场次 s */
				/* 日期 s */
				tags,
				date: '',
				startDate: tags[1].value,
				endDate: tags[2].value,
				timeData: {
					clockinfo: '',
					date: '',
					fulldate: '',
					lunar: '',
					month: '',
					range: '',
					year: ''
				},
				selected: [],
				infoShow: false,
				showCalendar: false,
				/* 日期 e */
				
				sessionId: '', // 选中的场次ID
				/* 选择场次 e */
			}
		},
		onLoad(event) {
			// console.log(event)
			this.venueData = JSON.parse(event.venueData);
		},
		methods: {
			/* 步骤条 s */
			change() {console.log(this.active)
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
			// 选择场景
			changeRadio: function(e) {
				var checked = e.target.value
				// console.log(checked)
				
				this.sceneId = checked
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
				e.month = (Array(2).join(0) + e.month).slice(-2);
				e.date = e.date < 10 ? '0' + (e.date) : e.date;
				e.fulldate = e.year + '-' + e.month + '-' + e.date
				console.log('confirm 返回:', e)
				this.timeData = e
				this.infoShow = true
			},
			retract() {
				this.infoShow = !this.infoShow
			}
			/* 日期 e */
			/* 选择场次 e */
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

	.example-title:after {
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
	}

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
	
	.tag-view {
		margin: 10upx 20upx;
		display: inline-block;
	}
</style>
