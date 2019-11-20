# 一.总台数据字典
（无需子系统数据库实现）
### 1.用户
### 2.角色
商户超级管理员  
商户管路员 0 
店长 1 
收银员2  　　
### 3.商户

# 二.用户登录
## 第一步：判断当前子系统登录状态
### 管理员端
 $token = $this->request->header("X-Token");
### 用户端
1.前后分离  
2.前后混合  

## 第二步：跳转登录总台登录
类型 | 地址
---|---
前台跳转登录 | http://testshop.card.nmgjoin.com/Login?return_url=' + encodeURI(return_url_str)
后台跳转登录 | http://vip.card.nmgjoin.com/SubSystemLogin/?gfappid=' + appid + '&return_url=' + url
## 第三步：回跳cookie获得jwt 后端反解用户基本信息
jwt(后台)  token_id:be0748ec77bd(用于校验jwt安全性)  
jwtuser(前台)  token_id:ingjiuser(用于校验jwt安全性)  
 
jwtusr(前台)示例
```
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJhZG1pbiIsImV4cCI6MjU1OTgwMzEyMywiYXVkIjoiY2FyZC5ubWdqb2luLmNvbSIsImp0aSI6ImxpbmdqaXVzZXIiLCJJRCI6MTMsIm9wZW5pZCI6Im9jMDdWMUJkb2hlbkQyV1l6MDhmSm52bURRQW8iLCJNZXJjaGFudElEIjoxLCJNZXJjaGFudEFwcGlkIjoid3hmNzJmMWQzNDliMmVkZmFjIiwiTmFtZSI6bnVsbCwibmlja25hbWUiOiLph5Hms73lpJUiLCJNZXJjaGFudE5hbWUiOiLmtYvor5XllYbmiLciLCJoZWFkaW1ndXJsIjoiaHR0cDovL3RoaXJkd3gucWxvZ28uY24vbW1vcGVuL1EzYXVIZ3p3ek00MnM1U082QWlhTk1hb1J5QWREY1JXd3FURDcyakxWZGplenhRcVlNQnVOS29vSTZJNFFOYkpGOFdQUnI2ZkVBaDFSamtZZWxSSWV6US8xMzIifQ.EdmNjCWeMwW4ctb1Ce74xTyWcqsF1XjIfPncS0mUQYk
```
字段名称 | 字段描述
---|---
MerchantID  | 总台商户ID
MerchantName|商户名称
MerchantAppid   | 商户Appid
ID      | 用户ID
openid  | 微信appid
Name        | 名称
nickname    |   昵称
headimgurl  |   头像 

```
// 01.判断用户登录状态
  if (!store.state.merchant.merchant.appid) {
    next('/404')
    return false
  }
  if (!store.state.user.user) {
    // 02.判断JWT
    var jwtuser = Cookie.get('jwtuser')

    var sourceUrl = process.env.VUE_APP_BASE_URL + 'm_' + store.state.merchant.merchant.appid + '#' + to.fullPath
    var loginUrl = 'http://vip.card.nmgjoin.com/SubSystemLogin/?gfappid=' + store.state.merchant.merchant.appid + '&return_url=' + escape(sourceUrl)

    if (!jwtuser) {
      window.location.href = loginUrl
      return false
    } else {
      // JWT存在 设置用户信息
      apiUser.GetUserInfo(jwtuser).then(res => {
        // 如果当前用户不是本商户重新获取用户信息
        if (res.data['MerchantAppid'] !== store.state.merchant.merchant.appid) {
          window.location.href = loginUrl
          return false
        }
        store.dispatch('user/update', res.data)
        store.dispatch('merchant/update', res.data)
        next()
      })
    }
  } else {
    next()
  }
```
```
/**
     * 从请求中获取Token 值
     * @return mixed
     */
	public function Check_JWT_Token($appid){

        //验证jwt token
        $request_url = \request()->Url(true);//原始请求URL
        $login_url="http://vip.card.nmgjoin.com/SubSystemLogin/?gfappid=".$appid."&return_url=".urlencode($request_url);

        //从 cookie 或者url 中获取 token
        $token = cookie("jwtuser");

        if(empty($token)){
            $this->redirect($login_url);
        }
        else{
            $is_success =JWT::validateToken($token,$this->admin_token_id);

            if($is_success["code"]!=0){ //校验不成功
                $this->error("jwt 验证错误",$login_url);
            }
            else{//添加用戶状态信息
                $this->CurrentUser=JWT::getUserData($token);

                //判断当前商户是否为用户登录商户
                if(!empty($appid)&&$this->CurrentUser["MerchantAppid"]!=$appid){
                    $this->redirect($login_url);
                }
            }
        }
    }
```

# 三.支付对接
### 1.下预付单
---
后端下单接口地址  
```
http://{api 域名}/api/PrepayOrder/AddOrder
```
参数 | 必填 | 描述 | 默认
---|---|---|---
OrderNum    | √ | 子系统订单号  
Amount      | √ | 支付金额
NotifyLink  | √ | PC 端支付成功跳转页
NotifyMobileLink | √ |  移动端支付成功跳转页
DetailUrl   | √ | 子系统订单详情页
NotifyUrl   | √ | 支付成功异步通知页面
MShopID     |  | 店铺ID
Memo | √ | 备注信息(显示在结账确认页)
appid | √ | 商户appid
type  | √ | 类型餐饮 1 商城6


### 2.同步跳转支付
---
前台跳转地址
```
http://{api 域名}/Consume/Index?ordernum={订单号}&gfappid={商户appid}
```
### 3.异步通知处理

1.支付回调通知

pay_type | 描述
---|---
1 | 现金
2 | 银行卡
3 | 店内微信
4 | 支付宝
5 | 储值卡
6 | 手工调账
7 | 线上微信支付

2.撤销退款订单回调

### 4 退款接口
1.整单撤销(未支付)
```
http://{api 域名}/api/PrepayOrder/CancelOrder
```
参数 | 必填 | 描述 | 默认
---|---|---|---
OrderNum    | √ | 子系统订单号  
AccountID      | √ | 操作员ID

### 2.退款（已经支付）
> 接口地址: http://{api 域名}/api/PrepayOrder/RefundOrder  
> 接口类型: POST

请求接口
参数 | 必填 | 描述 | 默认
---|---|---|---
OrderNum    | √ | 子系统订单号  
AccountID      | √ | 操作员ID
RefundAmount    |√ |退款金额
响应参数
参数 | 必填 | 描述 
---|---|---
code    | √ | 代码 0 正常 
msg      | √ | 信息
# 三.查询类接口

### 1.查询商户所有门店
```
http://{api 域名}/api/Shop?MerchantID={商户号}  
eg:shop.card.nmgjoin.com/api/Shop?MerchantID=1
```
### 1.查询订单详情


> 接口地址：http://testshop.card.nmgjoin.com/api/Order/GetOrder  
> 接口类型：POST

接口请求
参数 | 描述
---|---
url | http://testshop.card.nmgjoin.com/api/Order/GetOrder
参数|  
$merchantId| 
outtradeno|

接口响应

参数名称 | 描述
---|---
TotalFee | 总计支付
OutTradeNo | 订单号
RealFee | 实际支付

# 四.资金接口

### 1.增加余额
> 接口地址：http://testshop.card.nmgjoin.com/api/Order/AddBlance  
> 接口类型：POST

接口请求
参数 | 描述
---|---
$merchantId| 
memo| 增加余额描述  

接口响应

参数名称 | 描述
---|---
TotalFee | 总计支付

