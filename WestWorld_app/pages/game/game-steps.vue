<template>
	<view>
		<view class="example-title">步骤 <image :src="venueData.thumb" style="width: 80upx; height: 80upx; border-radius: 10upx;"></image></view>
		<view class="example-body">
			<uni-steps :options="stepsList" :active="active" />
		</view>
		
		<view class="example-title">{{active === 5 ? '' : '选择'}}{{ items[active] }}</view>
		<view class="example-body">
			<view class="content uni-common-mb">
				<view v-show="current === 0">
					
					<radio-group @change="radioChange">
					
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
					选项卡2的内容
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
	</view>
</template>

<script>
	import {uniSteps, uniSegmentedControl, uniGrid, uniGridItem} from '@dcloudio/uni-ui'

	export default {
		components: {
			uniSteps, // 步骤条
			uniSegmentedControl, // 分段器
			uniGrid, uniGridItem
		},
		data() {
			return {
				venueData: {},
				
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
				
				sessionId: '', // 选中的场次ID
			}
		},
		onLoad(event) {
			// console.log(event)
			this.venueData = JSON.parse(event.venueData);
		},
		methods: {
			/* 步骤条 s */
			change() {console.log(this.active)
				// 步骤条
				if (this.active < this.stepsList.length - 1) {
					
					// 所有步骤必须判断是否选择场景
					if (!this.sceneId) {
						uni.showToast({
							title: '请选择场景',
							icon: 'none'
						});
						return;
					}
					// 除第一步外，其他步骤必须判断是否选择场次
					if (!this.sessionId && this.active !== 0) {
						uni.showToast({
							title: '请选择场次',
							icon: 'none'
						});
						return;
					}
					
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
			
			radioChange: function(e) {
				var checked = e.target.value
				// console.log(checked)
				
				this.sceneId = checked
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
</style>