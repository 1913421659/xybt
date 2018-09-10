var instance = null;
var objRecord_read = {};
var objRecord_unread = {};
/*
 PRIVATE 				1		单聊
 DISCUSSION 			2		讨论组
 GROUP 					3		群组
 CHATROOM 				4		聊天室
 CUSTOMER_SERVICE 		5		客服
 SYSTEM 				6		系统消息
 APP_PUBLIC_SERVICE 	7		应用公众账号（应用内私有）
 PUBLIC_SERVICE 		8		公众账号 (公有)
 */
var conversationType = RongIMLib.ConversationType.PRIVATE;

function init(params, callbacks, modules) {
    var appKey = params.appKey;
    var token = params.token;
    var navi = params.navi || "";

    modules = modules || {};
    var RongIMLib = modules.RongIMLib || window.RongIMLib;
    var RongIMClient = RongIMLib.RongIMClient;
    var protobuf = modules.protobuf || null;

    var config = {};

    //私有云切换navi导航
    if (navi !== "") {
        config.navi = navi;
    }

    //私有云切换api
    var api = params.api || "";
    if (api !== "") {
        config.api = api;
    }

    //support protobuf url + function
    if (protobuf != null) {
        config.protobuf = protobuf;
    }

    RongIMLib.RongIMClient.init(appKey, null, config);

    instance = RongIMClient.getInstance();

    // 连接状态监听器
    RongIMClient.setConnectionStatusListener({
        onChanged: function (status) {
            switch (status) {
                case RongIMLib.ConnectionStatus.CONNECTED:
                    callbacks.getInstance && callbacks.getInstance(instance);
                    break;
            }
        }
    });

    /*
     文档：http://www.rongcloud.cn/docs/web.html#3、设置消息监听器

     注意事项：
     1：为了看到接收效果，需要另外一个用户向本用户发消息
     2：判断会话唯一性 ：conversationType + targetId
     3：显示消息在页面前，需要判断是否属于当前会话，避免消息错乱。
     4：消息体属性说明可参考：http://rongcloud.cn/docs/api/js/index.html
     */
    RongIMClient.setOnReceiveMessageListener({
        // 接收到的消息
        onReceived: function (message) {
            // 判断消息类型
            console.log("新消息: " + message.targetId);
            console.log(getData(message.targetId,message.conversationType));
            var tId = document.getElementById('currentUser').getAttribute('data');
            if(tId!= message.targetId && message.conversationType==1){
                console.log('========private not current==========');
                console.log(message);
                setData(message.targetId,message.content,'unread',message.conversationType);
                var count = getUnreadMessageCount(message.targetId,message.conversationType);
                if(count>0){
                    showContactLeft(message.targetId,message.content.user.name,message.content.user.portrait,message.conversationType,message.content.user.rank_name,message.content.content,count);
                }
            }else if(message.conversationType==1){
                console.log('private current');
                setData(message.targetId,message.content,'read',message.conversationType);
                showContactLeft(message.targetId,message.content.user.name,message.content.user.portrait,conversationType,message.content.user.rank_name,message.content.content,0);
                showMessage(message.content.user.name,message.content.user.portrait,message.content.content,'receive');
                callbacks.receiveNewMessage && callbacks.receiveNewMessage(message);
            }else if(message.conversationType==3 && tId!= message.targetId){
                console.log('group not current');
                console.log(message);
                setData(message.targetId,message.content,'unread',message.conversationType);
                var count = getUnreadMessageCount(message.targetId,message.conversationType);
                if(count>0){
                    showContactLeft(message.content.extra.id,message.content.extra.name,message.content.extra.headimg,message.conversationType,'',message.content.content,count);
                }
            }else if(message.conversationType==3){
                console.log('group current');
                showContactLeft(message.targetId,message.content.extra.name,message.content.extra.headimg,conversationType,'',message.content.content,0);
                setData(message.targetId,message.content,'read',message.conversationType);
                showMessage(message.content.user.name,message.content.user.portrait,message.content.content,'receive');
                callbacks.receiveNewMessage && callbacks.receiveNewMessage(message);
            }
        }
    });

    //开始链接
    RongIMClient.connect(token, {
        onSuccess: function (userId) {
            callbacks.getCurrentUser && callbacks.getCurrentUser({userId: userId});
            console.log("链接成功，用户id：" + userId);
            getConversationList();
            getConversationUnreadCount();
        },
        onTokenIncorrect: function () {
            //console.log('token无效');
        },
        onError: function (errorCode) {
            console.log("=============================================");
            var info = '';
            switch (errorCode) {
                case RongIMLib.ErrorCode.TIMEOUT:
                    info = '超时';
                    break;
                case RongIMLib.ErrorCode.UNKNOWN_ERROR:
                    info = '未知错误';
                    break;
                case RongIMLib.ErrorCode.UNACCEPTABLE_PaROTOCOL_VERSION:
                    info = '不可接受的协议版本';
                    break;
                case RongIMLib.ErrorCode.IDENTIFIER_REJECTED:
                    info = 'appkey不正确';
                    break;
                case RongIMLib.ErrorCode.SERVER_UNAVAILABLE:
                    info = '服务器不可用';
                    break;
            }
            console.log(errorCode);

        }
    });

    //joinGroup('15','群组聊天3');
}

function registerMessage(type) {
    var messageName = type; // 消息名称。
    var objectName = "s:" + type; // 消息内置名称，请按照此格式命名 *:* 。
    var mesasgeTag = new RongIMLib.MessageTag(true, true); //true true 保存且计数，false false 不保存不计数。
    var propertys = ["name", "age", "gender"]; // 消息类中的属性名。

    RongIMClient.registerMessageType(messageName, objectName, mesasgeTag, propertys);
}

//获取所有会话列表
function getConversationList(){
    instance.getRemoteConversationList ({
        onSuccess: function(list) {
            //list 会话列表
            console.log('会话列表');
            console.log(list);
        },
        onError: function(error) {
            //GetConversationList error
            console.log(error);
        }
    },null,null);
}

//指定多种会话类型获取未读消息数。
function getConversationUnreadCount(){
    instance.getConversationUnreadCount([conversationType],{
        onSuccess: function(count) {
            console.log("count:"+count);
        },
        onError: function(error) {
        }
    });
}

//拉取本地历史消息记录，如果本地没有历史消息，会自动从融云服务器拉去历史消息
function getHistoryMessage(targetId,pullMessageTime,count){
    if(!targetId){
        alert('参数错误');
        return ;
    }

    instance.getHistoryMessages(conversationType, targetId,pullMessageTime, count, {
        onSuccess: function(list, hasMsg) {
            console.log(list);
            //list 历史消息数组，hasMsg为boolean值，如果为true则表示还有剩余历史消息可拉取，为false的话表示没有剩余历史消息可供拉取。
        },
        onError: function(error) {
            //GetHistoryMessages error
        }
    });
}

function sendTextMessage() {
    /*
     文档： http://www.rongcloud.cn/docs/web.html#5_1、发送消息
     http://rongcloud.cn/docs/api/js/TextMessage.html
     1: 单条消息整体不得大于128K
     2: conversatinType 类型是 number，targetId 类型是 string
     */

    /*
     1、不要多端登陆，保证所有端都离线
     2、接收 push 设备设置:
     （1）打开系统通知提醒
     （2）小米设置 “授权管理” －> “自己的应用” 为自启动
     （3）应用内不要屏蔽新消息通知
     3、内置消息类型，默认 push，自定义消息类型需要
     pushData 显示逻辑顺序：自定义 > 默认
     4、发送其他消息类型与发送 TextMessage 逻辑、方式一致
     */
    var pushData = "pushData" + Date.now();

    var isMentioned = false;

    var textBox = document.getElementById('message_box');

    var sentUserId = document.getElementById('sendUserId').value;
    var sentUserName = document.getElementById('sendUserName').innerHTML;
    var sendUserImg = document.getElementById('sendUserImg').src
    var rank_name = document.getElementById('sendUserRankName').innerHTML;
    var c_type = document.getElementById('currentUser').getAttribute('ctype');
    var group={};

    var targetId = document.getElementById('currentUser').getAttribute('data');

    if(targetId=='' || !targetId){
        alert('请选择一个聊天对象');
        return ;
    }

    if(textBox.innerHTML==''){
        alert('请输入要发送的消息');
        return;
    }

    if(c_type=='GROUP'){
        conversationType = RongIMLib.ConversationType.GROUP;
        var groupname = jQuery('#currentUser').html();
        var headImg = jQuery('#currentUser').attr('imgv');
        group = {"id":targetId,"name":groupname,'headimg':headImg };
    }else if(c_type=='PRIVATE'){
        conversationType = RongIMLib.ConversationType.PRIVATE;
    }
    var content = {
        content: textBox.innerHTML,
        user: {
            "id": sentUserId,	//不支持中文及特殊字符
            "name": sentUserName,
            "portrait": sendUserImg,
            "rank_name":rank_name
        },
        extra: group
    };

    var msg = new RongIMLib.TextMessage(content);

    instance.sendMessage(conversationType, targetId, msg, {
        onSuccess: function (message) {
            setData(targetId,message.content,'read',conversationType);
            if(conversationType==1){
                showContactLeft(targetId,message.content.user.name,message.content.user.portrait,conversationType,message.content.user.rank_name,message.content.content,0);
            }else if(conversationType==3){
                showContactLeft(targetId,message.content.extra.name,message.content.extra.headimg,conversationType,'',message.content.content,0);
            }

            showMessage(message.content.user.name,message.content.user.portrait,message.content.content,'send');
            textBox.innerHTML='';
        },
        onError: function (errorCode, message) {
            console.log(message);
        }
    }, isMentioned, pushData);
}

//将聊天信息显示到聊天框中
function showMessage(username,headImg,msg,type){
    var str = '';
    if(type=='receive'){
        str = '<div class="chat_sent"><div class="media chat_3"><div class="media-left"><a href="#"class="chat_three" ><img class="media-object" src="'+headImg+'" alt="...">'+
        '</a></div><div class="media-body chat_t"><h6 >'+username+'</h6><div class="chat_con"><i class="msg_input"></i>'+
        msg+'</div></div></div></div>';
    }else if(type=='send'){
        str = '<div class="chat_r_2 clearfix"><div class="chat_4"> <div class="media chat_3"><div class="media-body chat_t"> <h6 >'+username+'</h6>'+
        '<div class="chat_con chat_conn"><i class="msg_inputt"></i>'+msg+
        '</div> </div> <div class="media-right"><a href="#"class="chat_three" > <img class="media-object" src="'+headImg+'" alt="...">'+
        '</a></div></div></div></div>';
    }

    var winDiv = document.getElementById('chat_window');
    winDiv.innerHTML = winDiv.innerHTML + str;
}



/*
 * 左侧联系人栏：分为两种
 * 1，收到非当前聊天用户的信息，显示到左侧
 * 2，点击右侧的联系人或者群组，显示到左侧
 */
function showContactLeft(uid,username,headImg,c_type,rank_name,msg,unreadCount){

    var contactStr = jQuery('#contactStr').val();
    var rank_name = (rank_name==''|| typeof rank_name == "undefined")?'':rank_name;

    var str='';
    var vid = uid+'_'+c_type;
    if(contactStr.indexOf(vid+',')==-1){
        contactStr += vid+','
        jQuery('#contactStr').val(contactStr);

        //选中的 ct-item 后面加上这个class active_aaa
        str = '<div class="ct-item" onclick="leftBoxItemEvent(this)"><input class="contactEach" id="'+vid+'" type="hidden" value="'+uid+'" data="'+rank_name+'" c_type="'+c_type+'"><div class="media"><div class="media-left media-middle"><a href="#" class="chat_three">'+
        '<img class="media-object" src="' + headImg + '" alt="..."></a></div><div class="media-body chat_title">'+
        '<h4 class="media-heading">'+username+'</h4><p><span style="width: 115px;height: 25px;">'+msg+'</span> ';
        if(unreadCount>0) {
            str +='<label class="label_1">'+unreadCount+'</label>';
        }

        str += '</p></div></div><img class="img_right" src="/resources/images/l_15.png" onclick="delBoxItemEvent(this);"></div>';
    }else{
        if(unreadCount>0){
            jQuery('#'+vid).parent().find('span').html(msg);

            var labelDom = jQuery('#'+vid).parent().find('label');
            console.log('lableDom==================');
            console.log(labelDom);
            if(labelDom.length>0){
                labelDom.html(unreadCount);
            }else{
                var pDom = jQuery('#'+vid).parent().find('p');
                pDom.html(pDom.html()+'<label class="label_1">'+unreadCount+'</label>');
            }
        }else{
            jQuery('#'+vid).parent().find('span').html(msg);
        }
    }

    var winDiv = jQuery('#contactBox');
    winDiv.html(winDiv.html() + str);
}



//重新连接
function reconnect(){
    RongIMClient.reconnect({
        onSuccess:function(){
            //重连成功
        },
        onError:function(){
            //重连失败
        }
    });
}

//缓存未能直接显示的聊天记录
function setData(sendUserId,record,isread,ctype){
    var isread = (isread==''|| typeof isread == "undefined")?'unread':isread;
    var type = (ctype==''|| typeof ctype == "undefined")?'1':ctype;
    var records = getData(sendUserId,type,isread);

    if(typeof records== "undefined"){
        records = [record];
    }else{
        records.push(record);
    }

    if(isread=='unread'){
        jQuery.data(objRecord_unread,'userRecord_'+sendUserId+'_'+type,records);
    }else{
        console.log('设置已读消息：sentUserId'+sendUserId);
        console.log(records);
        jQuery.data(objRecord_read,'userRecord_'+sendUserId+'_'+type,records);
    }


}

//获取聊天记录
function getData(sendUserId,ctype,isread){
    var isread = (isread==''|| typeof isread == "undefined")?'unread':isread;
    var type = (ctype==''|| typeof ctype == "undefined")?'1':ctype;
    if(isread=='unread'){
        return jQuery.data(objRecord_unread,'userRecord_'+sendUserId+'_'+type);
    }else{
        return jQuery.data(objRecord_read,'userRecord_'+sendUserId+'_'+type);
    }
}

function delData(sendUserId,type,isread){
    var isread = (isread==''|| typeof isread == "undefined")?'unread':isread;
    if(isread=='unread'){
        jQuery.removeData(objRecord_unread,'userRecord_'+sendUserId+'_'+type);
    }else{
        jQuery.removeData(objRecord_read,'userRecord_'+sendUserId+'_'+type);
    }
}

//聊天窗口显示未读消息的所属者以及未读条数
function showUnreadMessage(sendUserId,ctype){
    var type = (ctype==''|| typeof ctype == "undefined")?'1':ctype;
    var records = getData(sendUserId,type,'unread');
    console.log('===================showUnreadMessage==========================');
    console.log(records);
    if(!(typeof records== "undefined")) {
        jQuery.each(records,function(index,item){
            showMessage(item.user.name,item.user.portrait,item.content,'receive');
            //读取信息后将信息放入已读缓存，删除未读表
            setData(sendUserId,item,'read',type);
        });

        delData(sendUserId,type,'unread');
    }
}

//显示已读信息,显示所有的聊天记录
function showReadMessage(sendUserId,ctype){
    var type = (ctype==''|| typeof ctype == "undefined")?'1':ctype;
    var records = getData(sendUserId,type,'read');
    console.log('==========param=======');
    console.log('sendUserId='+sendUserId+'|'+'type='+type+'|');
    console.log('===================showReadMessage==========================');
    console.log(records);
    if(!(typeof records== "undefined")) {
        jQuery.each(records,function(index,item){
            //区分发送者跟接受者
            var isSend = item.user.id == userId ?'send':'receive';
            showMessage(item.user.name,item.user.portrait,item.content,isSend)
        });
    }
}

function getUnreadMessageCount(sendUserId,ctype){
    var type = (ctype==''|| typeof ctype == "undefined")?'1':ctype;
    var records = jQuery.data(objRecord_unread,'userRecord_'+sendUserId+'_'+type);

    return records.length;
}

function leftBoxItemEvent(th){
    $('#chat_window').html('');
    var ctype = $(th).find('input').attr('c_type');
    var targetId = $(th).find('input').val();
    var username =  $(th).find('h4').html();
    var rank_name = $(th).find('input').attr('data');

    var label = $(th).find('label').remove();

    if(ctype==1){
        $('#currentUser').attr('data',targetId);
        $('#currentUser').html('<span>'+rank_name+'</span>'+username);
        $('#currentUser').attr('ctype','PRIVATE');
    }else if(ctype==3){
        $('#currentUser').attr('data',targetId);
        $('#currentUser').attr('ctype','GROUP');
        $('#currentUser').html(username);
    }

    showReadMessage(targetId,ctype);//切换的时候，先显示已读信息
    showUnreadMessage(targetId,ctype);//显示
}

function delBoxItemEvent(th){
    var t = jQuery(th).parent().find('.contactEach').attr('id');
    jQuery('#contactStr').val(jQuery('#contactStr').val().replace(t+',',''));
    var cur_data = jQuery('#currentUser').attr('data');
    var cur_type = jQuery('#currentUser').attr('ctype');
    if(cur_data && cur_type){
        if(cur_type=='GROUP'){
            cur_type = 3;
        }else if(cur_type=='PRIVATE'){
            cur_type = 1;
        }
        var cur = cur_data+'_'+ cur_type;

        if(cur == t){
            jQuery('#currentUser').attr('data','');
            jQuery('#currentUser').attr('ctype','');
            jQuery('#currentUser').attr('imgv','');
            jQuery('#currentUser').html('');
            jQuery('#chat_window').html('');
        }
    }
    jQuery(th).parent().remove();
    cancelBubble();//阻止冒泡
}

//阻止冒泡
function cancelBubble(e) {
    var evt = e ? e : window.event;
    if (evt.stopPropagation) {        //W3C
        evt.stopPropagation();
    }else {       //IE
        evt.cancelBubble = true;
    }
}