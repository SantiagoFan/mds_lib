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
use think\Exception;


class JWT
{
    /**
     * 创建Token
     * @param $uid 需要保存的用户身份标识
     * @return String
     **/
    public static function createToken($data = [])
    {
        $signer  = new Sha256();
        $secret = "ling@ji)!join*"; //签名秘钥
        $build = new Builder();

        $build->setIssuer('admin') //发布者
            ->setAudience('card.nmgjoin.com') //接收者
            ->setId('factory_flag', true) //对当前token设置的标识
            ->setIssuedAt(time())  //token创建时间
            ->setExpiration(time() + (86400)); //token有效期时长

            foreach ($data as $k=>$v){
                $build->set($k, $v);//自定义数据
            }
            $token = $build->sign($signer, $secret)//设置签名
            ->getToken();//获取加密后的token，转为字符串

        return (String) $token;
    }

    /**
     * 检测Token是否过期与篡改
     * @param token
     * @return boolean
     **/
    public static function validateToken($tokenstr = null,$token_id, $secret = "ling@ji)!join*")
    {
        $signer  = new Sha256();
        try{
            $token = (new Parser())->parse((string) $tokenstr);

            //验证是否修改过
            if(!$token->verify($signer,$secret)){
                return ['code'=>-1,"msg"=>"token sign faild"];
            }
            // 验证是否过期
            $data = new ValidationData();
            $data->setIssuer('admin');
            $data->setAudience('card.nmgjoin.com');
            $data->setId($token_id);
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
     * 获取管理员用户数据
     * @param $tokenstr
     * @return array|null
     */
    public static function getAdminData($tokenstr){
        try{
            $token = (new Parser())->parse((string) $tokenstr);
            $userDate=[];
            $userDate["ID"]=$token->getClaim("ID");
            $userDate["RealName"]=$token->getClaim("RealName");
            $userDate["MerchantID"]=$token->getClaim("MerchantID");
            $userDate["MerchantName"]=$token->getClaim("MerchantName");
            $userDate["MShopID"]=$token->getClaim("MShopID");
            $userDate["MShopName"]=$token->getClaim("MShopName");
            $userDate["MAccountRole"]=$token->getClaim("MAccountRole");

            $userDate["LastLoginTime"]=$token->getClaim("LastLoginTime");//上次登录时间
            $userDate["MerchantAppid"]= $token->getClaim("MerchantAppid");
            return $userDate;
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
    /**
     * 获取前台客户端信息
     *
     */
    public static function GetUserData($tokenstr){
        try{
            $token = (new Parser())->parse((string) $tokenstr);
            $userDate=[];

            $userDate["ID"]=$token->getClaim("ID");
            $userDate["openid"]=$token->getClaim("openid");
            $userDate["MerchantID"]=$token->getClaim("MerchantID");
            $userDate["MerchantName"]=$token->getClaim("MerchantName");
            $userDate["MerchantAppid"]=$token->getClaim("MerchantAppid");
            $userDate["Name"]=$token->getClaim("Name");
            $userDate["nickname"]=$token->getClaim("nickname");
            $userDate["headimgurl"]=$token->getClaim("headimgurl");


            return $userDate;
        }
        catch (\Exception $e){
            return null;
        }
    }


    /**
     * 获取工厂用户数据
     * @param $tokenstr
     * @return array|null
     */
    public static function getFactoryData($tokenstr){
        try{
            $token = (new Parser())->parse((string) $tokenstr);
            $userDate=[];
            $userDate["ID"]=$token->getClaim("ID");
            $userDate["RealName"]=$token->getClaim("RealName");
            $userDate["MerchantID"]=$token->getClaim("MerchantID");
            $userDate["MerchantName"]=$token->getClaim("MerchantName");
            $userDate["MShopID"]=$token->getClaim("MShopID");
            $userDate["MShopName"]=$token->getClaim("MShopName");
            $userDate["MAccountRole"]=$token->getClaim("MAccountRole");

            $userDate["LastLoginTime"]=$token->getClaim("LastLoginTime");//上次登录时间
            $userDate["MerchantAppid"]= $token->getClaim("MerchantAppid");
            return $userDate;
        }
        catch (\Exception $e){
            return $e->getMessage();
        }
    }
}