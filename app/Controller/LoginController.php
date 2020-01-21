<?php

declare(strict_types=1);

namespace App\Controller;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\LoginException;
use App\Helper\CommonHelper;
use App\Service\VisitorServiceInterface;
use App\Service\UserServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;

class LoginController extends AbstractController
{

    //uid
    protected $uid = null;

    //suid
    protected $suid = null;

    //标识：是否为新建用户
    protected $isCreate = CommonCode::DBDEFAULTVAL;

    //标识：今日首次登陆
    protected $debutToday = CommonCode::DBDEFAULTVAL;

    //返回客户端参数
    protected $returnToClient = [
        'hallip' => '',
        'backupHallIp' => [],
        'user' => [],
        'token' => '',
        'game_list' => [],
        'api_url' => '',
        'img_url' => '',
        'upload_url' => '',
        'registerReward' => [],
    ];

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
                $uid = $this->loginByPhone($this->request);
                break;
            default:
                $uid = $this->loginByGuest($this->request);
                break;
        }

        //获取用户信息和游戏信息
        $userInfo = $this->userService->getUserBaseGameInfo( $uid );
        $status = $userInfo['status'] ?? CommonCode::ACCOUNTUNAVAILABLE;

        //判断帐号被封情况,被封则禁止登陆
        $checkAccountStatus = $this->checkBlockadeStatus( $status );
        if( !$checkAccountStatus )
        {
            throw new LoginException(ErrorCode::ACCOUNTUNAVAILABLE );
        }

        //判断是否为今日是否为首次登陆
        $this->checkDebutToday( $userInfo['ltime'] );

        //更新用户登录信息
        $this->updateUserLoginInfo();

        //老用户今日首登奖励
        $this->veteranPlayerReward( $uid, $userInfo['ltime'] );

        return $this->response->json( $userInfo );
    }

    /**
     * 手机登录
     */
    protected function loginByPhone()
    {
        $uid = 1;
        return $uid;
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
            $this->createUser( $createParams );
            $uid = $this->uid;
        }
        $this->suid = $suid;
        return $uid;
    }

    /**
     * 注册用户
     * @param array $param
     */
    protected function createUser( $param = [] )
    {
        $createUserInfo = $this->userService->createUser( $param );
        $this->uid = $createUserInfo['uid'];
        unset($createUserInfo['uid']);
        $this->returnToClient['registerReward'] = $createUserInfo;
        $this->isCreate = CommonCode::TRUEVALUE;
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

    /**
     * 检查账号被封锁情况
     * @param $status
     */
    private function checkBlockadeStatus( $status )
    {
        return $status == CommonCode::ACCOUNTAVAILABLE ? true : false;
    }


    /**
     * 检查是否为今日首次登陆
     */
    private function checkDebutToday( $loginTime = '' )
    {
        $time = CommonHelper::localStrToTime( 'today' );
        if( $time > $loginTime )
        {
            $this->debutToday = CommonCode::ACCOUNTUNAVAILABLE;
        }
    }

    /**
     * 事件更新用户登录信息
     */
    private function updateUserLoginInfo()
    {
        $data = [
            'uid'   => $this->uid,
            'suid'  => $this->suid,
            'gid'   => $this->request->input('gid', ''),
            'pid'   => $this->request->input('pid', ''),
            'ltime' => time(),
            'version' => $this->request->input('version', '1.0.0'),
        ];
        $this->userService->updateUserLoginInfoEvent( $data );
    }

    /**
     * 老玩家回归奖励
     */
    private function veteranPlayerReward( $uid = 0, $ltime = '' )
    {
        if( $this->isCreate == CommonCode::DBDEFAULTVAL && $this->debutToday == CommonCode::TRUEVALUE )
        {
            $curTime = time();
            $lossDays = config('lossdays', CommonCode::LOSSDAYS );
            //流失玩家回归
            if( ( $curTime - $ltime ) > ( $lossDays * 86400 ) )
            {
                $version = $this->request->input('version', '1.0.0');
                $this->userService->userReturnReward( $uid, $version );
            }
        }
    }
}
