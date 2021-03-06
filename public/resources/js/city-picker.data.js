/*!
 * Distpicker v1.0.2
 * https://github.com/tshi0912/city-picker
 *
 * Copyright (c) 2014-2016 Tao Shi
 * Released under the MIT license
 *
 * Date: 2016-02-29T12:11:36.473Z
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define('ChineseDistricts', [], factory);
    } else {
        // Browser globals.
        factory();
    }
})(function () {

    var ChineseDistricts = {
            86: {
                    项目: [
                {code: '340000', address: '小蚁直播'},
                {code: '110000', address: '微信导航'},
                {code: '500000', address: '安卓应用'},
                {code: '350000', address: '安卓游戏'},
                {code: '620000', address: 'VR软件'},
                {code: '440000', address: '生活服务'},
                {code: '450000', address: '企业服务'},
                {code: '520000', address: '宣传活动'},
                {code: '460000', address: '线下店铺'},
                {code: '230000', address: '商品促销'}]
                
            },
            110000: {
                110100: '资讯阅读',
                110200: '名人明星',
                110300: '影音娱乐',
                110400: '生活购物',
                110500: '社区交友',
                110600: '体育竞赛',
                110700: '文化教育',
                110800: '其他类别',
            },
            
            620000: {
                620100: 'VR应用',
                620200: 'VR游戏',
                620300: 'VR视频',
                620400: 'VR资讯',   
            },
           
            230000: {
                230100: '女装男装',
                230200: '鞋类箱包',
                230300: '母婴用品',
                230400: '护肤彩妆',
                230500: '汇吃美食',
                230600: '珠宝配饰',
                230700: '家装建材',
                230800: '家居家纺',
                230900: '百货市场',
                231000: '汽车用品',
                231100: '手机数码',
                231200: '家电办公',
                232700: '户外运动',
            },
          
            340000: {
                340100: '网游竞技',
                340200: '单机热游',
                340300: '娱乐综艺',
                340400: '手游休闲',     
            },
            
            350000: {
                350100: '角色扮演',
                350200: '休闲益智',
                350300: '动作冒险',
                350400: '网络游戏',
                350500: '体育竞速',
                350600: '飞行射击',
                350700: '经营策略',
                350800: '棋牌天地',
                350900: '儿童游戏'
            },
            
            
            440000: {
                440100: '金融理财',
                440200: '增值培训',
                440300: '便民服务',
                440400: '信用卡办理',
                440500: '休闲旅游',
               
            },
            
            450000: {
                450100: '公司注册',
                450200: '财务代理',
                450300: '发明专利',
                450400: '网络开发',
                450500: '公众号开发',
                450600: '系统开发',
                450700: 'APP开发',
                450800: '企业融资',
                
            },
            
            460000: {
                460100: '精美服装',
                460200: '母婴亲子',
                460300: '汽车服务',
                460400: '学习培训',
                469001: '电影',
                469002: '运动健身',
                469005: 'KTV',
                469006: '周边游',
                469007: '外卖',
                469021: '婚纱摄影',
                469022: '美食',
                469023: '家居家装',
                469024: '百货商店',
                469025: '生活服务',
                469026: '家电办公',
              
            },
            
            500000: {
                500100: '系统安全',
                500200: '通讯社交',
                500300: '影音视听',
                500400: '新闻阅读',
                500500: '生活休闲',
                500600: '主题壁纸',
                500700: '办公商务',
                500800: '摄影摄像',
                500900: '购物优惠',
                501000: '地图旅游',
                501100: '教育学习',
                501200: '金融理财',
                501300: '健康医疗',
                501400: '新闻资讯',
                
            },
            
            520000: {
                520100: '线下活动',
                520300: '会展活动 ',
                520400: '球赛问票',
                520500: '演唱会门票',
                520600: '路演活动',
            },
           
        
        }
        ;

    if (typeof window !== 'undefined') {
        window.ChineseDistricts = ChineseDistricts;
    }

    return ChineseDistricts;

});
