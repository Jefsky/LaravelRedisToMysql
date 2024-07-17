<?php
/**
 * FILE_NAME SystemLogService.php
 * @date 2024/6/28
 * @author Jefsky
 */

namespace App\Service\Api;

use App\Models\SystemLog;
use App\Service\Common\ApiBaseService as BaseService;

class SystemLogService extends BaseService
{
    protected static array $fields = ['id', 'type', 'part', 'user_id', 'content', 'add_time'];

    /**
     * addLog
     * @param $type 
     * @param $user_id
     * @param $content
     * @return mixed
     */
    public static function addLog($type, $user_id, $content)
    {
        $data['type'] = $type;
        $data['part'] = '2';
        $data['user_id'] = $user_id;
        $data['content'] = $content;
        $data['add_time'] = time();
        return SystemLog::insert($data);
    }

    // 批量录入系统日志
    public static function addLogs($datas)
    {
        return SystemLog::insert($datas);
    }
}
