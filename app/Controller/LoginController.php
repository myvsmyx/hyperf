<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\LoginException;
use App\Service\VisitorServiceInterface;
use App\Service\UserServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;

class LoginController extends AbstractController
{

    /**
     * @Inject
     * @var VisitorServiceInterface
     */
    private $visitorService;

    /**
     * @Inject
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * 登录
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index()
    {
        //设置语言
        $lang = $this->request->input('lang', CommonCode::GAMETH);
        Context::set( CommonCode::GAMELANG, $lang );

        //必传参数
        $lid = $this->request->input('lid', CommonCode::DBDEFAULTVAL );
        if( !CommonCode::allowLoginLid( $lid ) )
        {
            throw new LoginException(ErrorCode::LIBERROR );
        }

        //验证签名
        $checkSignRs = $this->checkLoginSig( $this->request );
        if( !$checkSignRs )
        {
            throw new LoginException(ErrorCode::LOGINSIGNERROR );
        }

        //根据不同的lid登录类型，做不同的操作
        switch ($lid)
        {
            case CommonCode::LIDPHONE:
                $data = $this->loginByPhone($this->request);
                break;
            default:
                $data = $this->loginByGuest($this->request);
                break;
        }
        return $this->response->json($data);
    }

    /**
     * 手机登录
     */
    protected function loginByPhone()
    {

    }

    /**
     * 游客登录
     */
    protected function loginByGuest()
    {
        $imei = $this->request->input('imei', '');
        //通过imei加密，获取suid,通过它来获取游客的uid
        $suid = $this->encryptionImei( $imei );
        $uid = $this->visitorService->getUidBySuid( $suid );
        if( is_null($uid) )
        {
            //注册操作
            $createParams = $this->request->all();
            $createParams['suid'] = $suid;
            $createUserInfo = $this->userService->createUser( $createParams );
            return $createUserInfo;
        }
        return $uid;
    }


    /**
     * 验证登录签名
     * @param string $sign
     */
    private function checkLoginSig()
    {
        $param = $this->request->all();
        $sig = $param['sig'] ?? null;
        if( is_null($sig) )
        {
            return false;
        }
        unset($param['sig']);
        //处理开始
        ksort($param);
        $str = '';
        foreach((array)$param as $key => $value)
        {
            $str .= $key . '=' . $value.'&';
        }
        $str = trim($str, '&'). CommonCode::LOGINPRIKEY;
        return ( md5($str) == $sig ) ? true : false;
    }

    /**
     * 加密imei，基本游客登录使用
     * @param string $imei
     */
    private function encryptionImei( $imei = '' )
    {
        return md5($imei.CommonCode::ENCRYPTIONIMEI);
    }
}
