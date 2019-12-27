<template>
	<view>
		<uni-list>
			<uni-list-item :thumb="userData.avatar" @click="toEditUserInfo('更换头像', 'avatar', userData.avatar)" />
			<uni-list-item title="昵称" :note="userData.user_name" @click="toEditUserInfo('更改昵称', 'user_name', userData.user_name)" />
			<uni-list-item title="性别" :note="userData.gender_msg" @click="toEditUserInfo('设置性别', 'gender', userData.gender)" />
			<uni-list-item title="个性签名" :note="userData.signature ? userData.signature : '（没有签名）'" @click="toEditUserInfo('个性签名', 'signature', userData.signature)" />
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
		onShow() {
			this.getUserInfo();
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
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
							
							self.userData = userData;
						}
					})
				}
			},
			
			/**
			 * 跳转编辑个人信息页
			 */
			toEditUserInfo(title, key, value) {
				if (key == 'avatar') { // 更换头像
					this.updateAvatar();
				} else {
					uni.navigateTo({
						url: '/pages/user/user-info/user-info-edit?title=' + title + '&key=' + key + '&value=' + value
					})
				}
			},
			
			/**
			 * 更换头像
			 */
			updateAvatar() {
				uni.chooseImage({
				    count: 1, //默认9
				    // sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
				    // sourceType: ['album'], // album 从相册选图，camera 使用相机，默认二者都有
				    success: function (res) {
				        console.log(JSON.stringify(res.tempFilePaths));
				    },
					fail:function(err){
						console.log(err)
					}
				});
			}
		}
	}
</script>

<style>

</style>
