<template>
	<view>
		<view class="banner">
			<image class="banner-img" :src="sceneDetail.thumb"></image>
			<view class="banner-title">{{sceneDetail.scene_name}}</view>
		</view>
		
		<view class="uni-padding-wrap uni-common-mt">
			<uni-segmented-control :current="current" :values="items" style-type="text" active-color="#4cd964" @clickItem="onClickItem" />
		</view>
		<view class="content">
			<view v-if="current === 0">
				<view class="uni-common-mt article-content">
					<rich-text :nodes="sceneDetail.scene_description"></rich-text>
				</view>
			</view>
			<view v-if="current === 1">
				<view class="uni-common-mt article-content">
					<rich-text :nodes="sceneDetail.game_rules"></rich-text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {uniSegmentedControl} from '@dcloudio/uni-ui';
	import common from '@/common/common.js';
	
	export default {
		components: {
			uniSegmentedControl, // SegmentedControl 分段器
		},
		data() {
			return {
				sceneId: '',
				sceneDetail: {},
				
				items: ['场景描述', '比赛规则'],
				current: 0,
			}
		},
		onLoad(event) {
			this.sceneId = event.scene_id; // 场景ID
			this.getSceneDetail(); // 获取场景信息
			this.current = (event.types == this.items[0]) ? 0 : 1;
			
			// 动态设置当前页面的标题
			uni.setNavigationBarTitle({
				title: this.items[this.current]
			});
		},
		methods: {
			/**
			 * 获取场景信息
			 */
			getSceneDetail() {
				let self = this;
				uni.request({
					url: this.$serverUrl + 'scene/' + this.sceneId,
					header: {
						'sign': common.sign(), // 验签
						'version': getApp().globalData.version, // 应用大版本号
						'model': getApp().globalData.systemInfo.model, // 手机型号
						'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
						'did': getApp().globalData.did, // 设备号
					},
					success:function(res){
						let sceneDetail = res.data.data;
						sceneDetail.thumb = sceneDetail.thumb ? self.$imgServerUrl + sceneDetail.thumb.replace(/\\/g, "/") : '/static/img/home.png'; // 场景缩略图
						self.sceneDetail = sceneDetail;
					}
				})
			},
			
			onClickItem(e) {
				if (this.current !== e) {
					this.current = e;
					
					// 动态设置当前页面的标题
					uni.setNavigationBarTitle({
						title: this.items[this.current]
					});
				}
			}
		}
	}
</script>

<style>
	.banner {
		height: 360upx;
		overflow: hidden;
		position: relative;
		background-color: #ccc;
	}

	.banner-img {
		width: 100%;
	}
	
	.banner-title {
		max-height: 84upx;
		overflow: hidden;
		position: absolute;
		left: 30upx;
		bottom: 30upx;
		width: 90%;
		font-size: 32upx;
		font-weight: 400;
		line-height: 42upx;
		color: white;
		z-index: 11;
	}
	
	.article-content {
		padding: 0 30upx;
		overflow: hidden;
		font-size: 30upx;
		margin-bottom: 30upx;
	}
</style>
