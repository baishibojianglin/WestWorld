<template>
	<view>
		<uni-list>
			<uni-list-item :thumb="userData.avatar" @click="toEditUserInfo('avatar')" />
			<uni-list-item title="昵称" :note="userData.user_name" @click="toEditUserInfo('user_name')" />
			<uni-list-item title="签名" :note="userData.signature" @click="toEditUserInfo('signature')" />
		</uni-list>
	</view>
</template>

<script>
	import {uniList, uniListItem} from '@dcloudio/uni-ui';
	import {mapState, mapMutations} from 'vuex';
	import common from '@/common/common.js';
	import Aes from '@/common/Aes.js';
	
	export default {
		components: {uniList, uniListItem},
		data() {
			return {
				userData: {}
			}
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		onLoad() {
			this.getUserInfo();
		},
		methods: {
			/**
			 * 获取用户信息
			 */
			getUserInfo() {
				let self = this
				if (this.hasLogin) {
					uni.request({
						url: this.$serverUrl + 'user/' + this.userInfo.user_id,
						header: {
							'sign': common.sign(), // 验签
							'version': getApp().globalData.version, // 应用大版本号
							'model': getApp().globalData.systemInfo.model, // 手机型号
							'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
							'did': getApp().globalData.did, // 设备号
							'access-user-token': this.userInfo.token
						},
						method: 'GET',
						success:function(res){
							// 用户信息
							let userData = JSON.parse(Aes.decode(res.data.data));
							userData.avatar = userData.avatar ? self.$imgServerUrl + userData.avatar.replace(/\\/g, "/") : '../../static/img/user.png'; // 用户头像
							userData.signature = userData.signature ? userData.signature : '（没有签名）'; // 个性签名
							
							self.userData = userData;
						}
					})
				}
			},
			
			/**
			 * 跳转编辑个人信息页
			 */
			toEditUserInfo(types) {
				uni.navigateTo({
					url: '/pages/user/user-info/user-info-edit?types=' + types
				})
			}
		}
	}
</script>

<style>

</style>
