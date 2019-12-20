<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\GameException;
use App\Exception\LoginException;
use App\Model\InfoBese;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;

class UserService implements UserServiceInterface
{

    /**
     * @Inject
     * @var VisitorServiceInterface
     */
    private $visitorService;//游客操作


    /**
     * @Inject
     * @var ActionUserServiceInterface
     */
    private $actionUserService;


    // 创建用户返回数据
    public $returnCreateUserRs = [
        'uid' => CommonCode::DBDEFAULTVAL,
        'addMoney' => CommonCode::DBDEFAULTVAL,
        'addPoint' => CommonCode::DBDEFAULTVAL,
        'addLottery' => CommonCode::DBDEFAULTVAL,
        'gift' => CommonCode::DBDEFAULTVAL,
        'addGold' => CommonCode::DBDEFAULTVAL,
    ];

    /**
     * 创建用户
     * @param array $param
     * @return mixed|void
     */
    public function createUser(array $param)
    {
        if( empty($param) || !is_array($param) || !isset($param['suid']) || !isset($param['gid']))
        {
            throw new LoginException( ErrorCode::CREATEUSERPARAMERR );
        }

        $lid = $param['lid'] ?? CommonCode::LIDGUEST;

        //生成新的uid
        $uid = intval( $this->createUid() );
        $param['uid'] = $uid;

        //个人信息创建
        $this->createPersonalInfo( $param );

        //游戏信息创建
        $this->createGameInfo( $uid, $lid );

        //个人最好记录信息创建
        $timezone = $param['timezone'] ?? CommonCode::DBDEFAULTVAL;
        $infoBestModel = make( InfoBese::class );
        $infoBestModel->createUser( $uid, $timezone );

        //uid进行相关映射
        $this->recordUid( $param );

        //返回新用户注册奖励
        $gameType = Context::get( CommonCode::GAMELANG );//默认游戏语言
        $registRewardConfig = config('registreward.'.$gameType);
        if( isset( $registRewardConfig[$lid] ) )
        {
            $registerReward = $registRewardConfig[$lid]; //奖励

            //注册发奖
            $data = [
                'uid'   => $uid,
                'money' => $registerReward['money']['num'],
                'actid' => $registerReward['money']['actid'],
            ];
            $this->actionUserService->updateUserMoney( $data );
            $this->returnCreateUserRs['addMoney']   = $registerReward['money']['num'] ?? 0;
            $this->returnCreateUserRs['addPoint']   = $registerReward['point']['num'] ?? 0;
            $this->returnCreateUserRs['gift']       = $registerReward['gift'] ?? 0;
            $this->returnCreateUserRs['addGold']    = $registerReward['gold']['num'] ?? 0;
        }
        $this->returnCreateUserRs['uid'] = $uid;
        return $this->returnCreateUserRs;
    }

    /**
     * 获取玩家的基本信息和游戏信息
     * @param int $uid
     * @return mixed|void
     */
    public function getUserBaseGameInfo( $uid = 0 )
    {
        if( empty($uid) )
        {
            throw new GameException( ErrorCode::SERVER_ERROR );
        }

        //获取游戏信息
        $gameInfo = $this->actionUserService->getGameInfo( $uid );

        //获取基本信息
        $baseInfo = $this->getUserBaseInfo( $uid );
        return array_merge( $gameInfo, $baseInfo );
    }

    /**
     * 获取用户信息
     * @param int $uid
     * @return mixed|void
     */
    public function getUserBaseInfo( $uid = 0 )
    {
        $table = 'info'.$uid%100;
        return DB::connection('user')->table($table)->where('uid', '=', $uid)->first();
    }

    /**
     * 创建uid
     */
    public function createUid()
    {
        $uid = Db::connection('user')->table('uids')->insertGetId(
            ['uid' => null, 'ctime' => time() ]
        );
        if( empty($uid) )
        {
            throw new LoginException( ErrorCode::CREATEUIDERROR );
        }
        return $uid;
    }

    /**
     * 创建个人信息
     * @param $param
     */
    public function createPersonalInfo( $param )
    {
        if( empty($param) || !is_array($param) || !isset($param['uid']) )
        {
            throw new LoginException( ErrorCode::CREATEUSERPARAMERR );
        }
        $time = time();
        $createParam = array(
            'uid'   => $param['uid'],
            'suid'  => $param['suid'],
            'gid'   => $param['gid'],
            'pid'   => $param['pid'] ?? CommonCode::PIDANDROID,
            'lid'   => $param['lid'] ?? CommonCode::LIDGUEST,
            'gender' => CommonCode::FEMALE,
            'version'=> $param['version'] ?? '1.0.0',
            'name'  => $param['name'] ?? 'unknow',
            'icon'  => $param['icon'] ?? '',
            'ltime' => $time,
            'rtime' => $time,
            'pay_amount'    => '0.00',
            'invite'    => CommonCode::DBDEFAULTVAL,
            'status'    => CommonCode::DBDEFAULTVAL,
        );
        $uid = $param['uid'];
        $table = 'info'.$uid%100;
        DB::connection('user')->table($table)->insert($createParam);
    }


    /**
     * 创建游戏信息
     * @param $param
     */
    public function createGameInfo( $uid = 0, $lid = 0)
    {
        if( empty($uid) || empty($lid) )
        {
            throw new LoginException( ErrorCode::CREATEUSERPARAMERR );
        }

        //设置初始值
        $gameType = Context::get( CommonCode::GAMELANG );//默认游戏语言
        $initMoney = config('initMoney');
        $money = $initMoney[$gameType][$lid] ?? config('defaultMoney');

        $param['uid'] = $uid;
        $param['money'] = $money;
        $param['exp'] = CommonCode::DBDEFAULTVAL;
        $param['safebox'] = CommonCode::DBDEFAULTVAL;
        $param['gold'] = CommonCode::DBDEFAULTVAL;
        $param['game'] = '';
        $param['point'] = CommonCode::DBDEFAULTVAL;

        //入库
        $table = 'game'.($uid % 100);
        Db::connection('game')->table($table)->insert($param);
    }

    /**
     * 根据不同的登录类型关系对应的uid
     * @param array $param
     */
    public function recordUid( $param = [] )
    {
        if( empty($param) || !is_array($param) || !isset($param['uid']) || !isset($param['lid']) )
        {
            throw new LoginException( ErrorCode::RECORDUIDPARAMERROR );
        }
        $lid = $param['lid'];
        $uid = $param['uid'];
//        switch ( $lid )
//        {
//            case CommonCode::LIDGUEST:
//                $this->visitorService->recordUidSuid( $uid, $param['suid'] );
//                break;
//        }
        if ( $lid == CommonCode::LIDGUEST )
        {
            $this->visitorService->recordUidSuid( $uid, $param['suid'] );
        }
    }
}