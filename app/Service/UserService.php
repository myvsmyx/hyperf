<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\CommonCode;
use App\Constants\ErrorCode;
use App\Exception\LoginException;
use App\Model\InfoBese;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class UserService implements UserServiceInterface
{

    /**
     * @Inject
     * @var VisitorServiceInterface
     */
    private $visitorService;//游客操作

    /**
     * @Inject
     * @var RequestInterface
     */
    private $request;


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
        $registRewardConfig = config('registreward');
        $gameType = $this->request->input('gamelang', config('gametype') );//默认游戏语言
        if( isset( $registRewardConfig[$gameType][$lid] ) )
        {
            $registerReward = $registRewardConfig[$gameType][$lid];//奖励

            //注册发奖
            $data = [
                'uid' => $uid,
                'money' => $registerReward['money']['num'],
                'actid' => $registerReward['money']['actid'],
            ];
//            $rs = $this->actionUserService->updateUserMoney($data);
//            var_dump($rs);
        }
        return $this->returnCreateUserRs;
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
        $gameType = $this->request->input('gamelang', config('gametype') );//默认游戏语言
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
        switch ( $lid )
        {
            case CommonCode::LIDGUEST:
                $this->visitorService->recordUidSuid( $uid, $param['suid'] );
                break;
        }
    }
}