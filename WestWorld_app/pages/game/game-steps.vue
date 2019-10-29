<template>
	<view>
		<view class="example-title">步骤</view>
		<view class="example-body">
			<uni-steps :options="list1" :active="active" />
		</view>
		<button type="primary" @click="change">{{ active === 5 ? '确定' : '下一步' }}</button>
	</view>
</template>

<script>
	import {uniSteps} from '@dcloudio/uni-ui'

	export default {
		components: {
			uniSteps
		},
		data() {
			return {
				/* 步骤 s */
				active: 0,
				list1: [{
					title: '1',
					desc: '场景'
				}, {
					title: '2',
					desc: '时间'
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
				/* 步骤 e */
			}
		},
		onLoad(event) {
			console.log(event)
		},
		methods: {
			change() {
				if (this.active < this.list1.length - 1) {
					this.active += 1
				} else {
					this.active = 0
				}
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
</style>