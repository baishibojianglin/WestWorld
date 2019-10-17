import $ from './jquery-2.js';
import Aes from './Aes.js';

/**
 * 公共函数类
 */
const common = class{
	/**
	 * JavaScript将字典序升序排列类似php中的ksort函数
	 * @param {Object} jsonData
	 */
	JsonSort(jsonData) {
		try {
			let tempJsonObj = {};
			let sdic = Object.keys(jsonData).sort();
			sdic.map((item, index)=>{
				tempJsonObj[item] = jsonData[sdic[index]]
			})
			return tempJsonObj;
		} catch(e) {
			return jsonData;
		}
	}

	/**
	 * 生成每次请求的sign验签算法
	 * @param {Object} tempJson
	 */
	setSign(tempJson) {
		// 1.按字典排序
		let json = this.JsonSort(tempJson);

		// 2.以&符号拼接字符串数据，如'key1=123&key2=abc'
		let string = $.param(tempJson); // param() 方法创建数组或对象的序列化表示形式

		// 3.AES加密
		let encryptString = Aes.encode(string);
		// AES解密
		// let decryptString = Aes.decode(encryptString);console.log(decryptString);

		return encryptString;
	}
}

export default new common;