<template>
	<view>
		<view class="uni-padding-wrap uni-common-mt">
			<form @submit="formSubmit">
				<view class="uni-form-item uni-column" v-if="userData.key === 'user_name'">
					<!-- <view class="title">{{ userData.title }}</view> -->
					<input class="uni-input" :name="userData.key" v-model="userData.value" maxlength="10" :placeholder="userData.title" />
				</view>
				<view class="uni-form-item uni-column" v-if="userData.key === 'gender'">
					<!-- <view class="title">性别</view> -->
					<radio-group name="gender" @change="radioChange">
						<label class="uni-list-cell uni-list-cell-pd">
							<text>男</text>
							<radio value="1" :checked="userData.value == 1 ? true : false" />
						</label>
						<label class="uni-list-cell uni-list-cell-pd">
							<text>女</text>
							<radio value="2" :checked="userData.value == 2 ? true : false" />
						</label>
					</radio-group>
				</view>
				<view class="uni-form-item uni-column" v-if="userData.key === 'signature'">
					<!-- <view class="title">{{ userData.title }}</view> -->
					<input class="uni-input" :name="userData.key" v-model="userData.value" maxlength="50" placeholder="请输入个性签名" />
				</view>
				<view class="uni-btn-v">
					<button form-type="submit">保存</button>
				</view>
			</form>
		</view>
	</view>
</template>

<script>
	import {mapState, mapMutations} from 'vuex';
	import common from '@/common/common.js';
	import Aes from '@/common/Aes.js';
	var graceChecker = require("@/common/graceChecker.js");
	
	export default {
		data() {
			return {
				userData: {}
			}
		},
		onLoad(event) {
			this.userData = event;
			
			// 动态设置当前页面的标题
			if (event.title) {
				uni.setNavigationBarTitle({
					title: event.title
				})
			}
		},
		computed: mapState(['hasLogin', 'userInfo']), // 对全局变量 hasLogin、userInfo 进行监控
		methods: {
			/**
			 * 提交表单
			 * @param {Object} e
			 */
			formSubmit: function(e) {
				// console.log('form发生了submit事件，携带数据为：' + JSON.stringify(e.detail.value))
			    var rule = []; // 定义表单规则
				var data = {}; // 定义 uni.request 请求的参数
				switch (this.userData.key){
					case 'user_name':
						rule = [{name: "user_name", checkType: "string", checkRule: "1,10", errorMsg: "昵称应为1-10个字符"}];
						data = {user_name: this.userData.value};
						break;
					case 'gender':
						rule = [{name: "gender", checkType: "in", checkRule:"1,2", errorMsg:"请选择性别"}];
						data = {gender: this.userData.value};
						break;
					case 'signature':
						rule = [{name: "signature", checkType: "string", checkRule: "1,50", errorMsg: "个性签名应为1-50个字符"}];
						data = {signature: this.userData.value};
						break;
					default:
						break;
				}
			    //进行表单检查
			    var formData = e.detail.value;
			    var checkRes = graceChecker.check(formData, rule);
			    if(checkRes){
			        // uni.showToast({title:"验证通过!", icon:"none"});
					this.editUserInfo(data);
			    }else{
			        uni.showToast({ title: graceChecker.error, icon: "none" });
			    }
			},
			
			/**
			 * 更新用户信息
			 */
			editUserInfo(data) {
				let self = this
				if (this.hasLogin) {
					uni.request({
						url: this.$serverUrl + 'user/' + this.userInfo.user_id,
						data: data,
						header: {
							'sign': common.sign(), // 验签
							'version': getApp().globalData.version, // 应用大版本号
							'model': getApp().globalData.systemInfo.model, // 手机型号
							'apptype': getApp().globalData.systemInfo.platform, // 客户端平台
							'did': getApp().globalData.did, // 设备号
							'access-user-token': this.userInfo.token
						},
						method: 'PUT',
						success: function(res){
							if (res.statusCode == 201) {
							    uni.navigateBack()
							    return;
							}
						}
					})
				}
			},
			
			/**
			 * radio-group 选中项发生变化时触发 change 事件
			 * @param {Object} evt
			 */
			radioChange(evt) {
				this.userData.value = evt.target.value;
			}
		}
	}
</script>

<style>

</style>
