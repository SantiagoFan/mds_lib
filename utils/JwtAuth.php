<?php
/**
 * Created by PhpStorm.
 * User: Administrator-范文刚
 * Date: 2019/3/22
 * Time: 17:14
 */

namespace join\utils;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use think\exception\HttpResponseException;
use think\facade\Request;


class JwtAuth
{
    private $token_id = 'join_token';//id防止重复
    private $secret ="join_secret";//加密钥匙

    private $audience ="nmgjoin.com";//接收者
    private $issuer ="nmgjoin.com";//颁发者

    private $cookie_name ="";

    function __construct($token_id,$secret,$audience="nmgjoin.com",$issuer ="nmgjoin.com")
    {
        $this->token_id= $token_id;
        $this->secret =$secret;
        $this->audience =$audience;
        $this->issuer =$issuer;

        $this->cookie_name =$this->token_id."_jwt";
    }

    /**
     * 获取用户信息
     * @param $tokenstr
     * @return array|string Claims data
     */
    public function getData($tokenstr){
        $token = (new Parser())->parse((string) $tokenstr);
        $data =[];
        $userDate = $token->getClaims();

        foreach($userDate as $k=>$v){
            if(in_array($k,['iss','aud','jti','iat','exp']))continue;//过滤jwt 字段
            $data[$k] = $v->getValue();
        }
        return $data;
    }
    /**
     * 检测Token是否过期与篡改
     * @param token
     * @return boolean
     **/
    public function validateToken($tokenstr = null)
    {
        $signer  = new Sha256();
        try{
            $token = (new Parser())->parse((string) $tokenstr);

            //验证是否修改过
            if(!$token->verify($signer,$this->secret)){
                return ['code'=>-1,"msg"=>"token sign faild"];
            }
            // 验证是否过期
            $data = new ValidationData();
            $data->setIssuer($this->issuer);
            $data->setAudience($this->audience);
            $data->setId($this->token_id);
            if ($token->validate($data)){
                return ['code'=>0,"msg"=>"validate success"];
            }
            else{
                return ['code'=>-1,"msg"=>"validate error"];
            }
        }
        catch (\Exception $e){
            return ['code'=>-1,"msg"=>$e->getMessage()];
        }
    }

    /**
     * 获取用户信息
     */
    public function GetUserData($login_url){

        $token = cookie($this->cookie_name);

        if (empty($token)) {
            //ajax
            if (Request::isAjax()) {
                $response = json(['code' => 50014, 'message' => '未登录请登录后重试']);
                throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
            } else {
                throw new HttpResponseException(redirect($login_url));
            }
        } else {
            $is_success = $this->validateToken($token);

            if ($is_success["code"] != 0) { //校验不成功

                if (Request::isAjax()) {
                    $response = json(['code' => 50012, 'message' => '登录超时请重新登录']);
                    throw new HttpResponseException($response);// 参考tp 框架内部处理redirect 和error的思路直接输出结果
                } else {
                    throw new HttpResponseException(redirect($login_url));
                }

            } else {//添加用戶状态信息
                return $this->getData($token);
            }
        }
    }


    /**
     * 通过用户信息创建 token 串 并存储到cookie 中
     **/
    public function CreateToken($data = [],$domain="")
    {
        $signer  = new Sha256();
        $build = new Builder();

        $build->setIssuer($this->issuer) //发布者
        ->setAudience($this->audience) //接收者
        ->setId($this->token_id, true) //对当前token设置的标识
        ->setIssuedAt(time())  //token创建时间
        ->setExpiration(time() + (86400)); //token有效期时长

        foreach ($data as $k=>$v){
            $build->set($k, $v);//自定义数据
        }
        $token = $build->sign($signer, $this->secret)//设置签名
        ->getToken();//获取加密后的token，转为字符串
        cookie($this->cookie_name,$token,['domain'=>$domain]);

        return (String) $token;
    }

    /**
     * 登出时删除cookie
     * @param string $domain
     */
    public function RemoveToken($domain=""){
        //  删除cookie

        cookie($this->cookie_name,null,['domain'=>$domain]);
    }
}
