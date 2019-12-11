<?php
declare (strict_types=1);

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\LoginException;
use App\Model\Visitorid;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\DbConnection\Db;

class VisitorService implements VisitorServiceInterface
{

    /**
     * @Inject
     * @var ConfigInterface
     */
    private $dbconfig;

    /**
     * @Inject
     * @var RequestInterface
     */
    private $request;


    /**
     * 通过suid获取uid
     * @param int $suid
     */
    public function getUidBySuid( $suid )
    {
        $this->setConnectionInfo('visitor' );
        $visitorModel = make(Visitorid::class );
        $table = $visitorModel->setTable( $suid );
        $info = Db::table($table)->where('suid', '=', $suid)->first();
        return $info['uid'] ?? null;
    }

    /**
     * 绑定uid和suid的关系
     * @param int $uid
     * @param string $suid
     */
    public function recordUidSuid( $uid = 0, $suid = '' )
    {
        $this->setConnectionInfo('visitor' );
        $visitorModel = make(Visitorid::class );
        $table = $visitorModel->setTable( $suid );
        Db::table($table)->insert(['uid' => $uid, 'suid' => $suid]);
    }

    /**
     * 设置数据库信息
     * @param string $dbname
     */
    protected function setConnectionInfo( $dbname = '' )
    {
        //游戏类型
        $defaultGameType = $this->dbconfig->get('gametype', 'th');
        $gameType = $this->request->input('gamelang', $defaultGameType );//默认游戏语言

        //游戏数据库配置
        $dbInfo = $this->dbconfig->get('dbdispatch.'.$dbname );
        if( !$dbInfo || !isset($dbInfo[$gameType]) )
        {
            throw new LoginException( ErrorCode::DBINFONOTFOUND );
        }

        //设置对应的数据库
        $dbKey = 'databases.default.database';
        $dbVal = $dbInfo[$gameType];
        $this->dbconfig->set($dbKey, $dbVal);
    }
}