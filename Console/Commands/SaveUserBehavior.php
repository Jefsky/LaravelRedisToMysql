<?php

namespace App\Console\Commands;

use App\Service\Api\SystemLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class SaveUserBehavior extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:saveUserBehavior';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'saveUserBehaviorToMysql';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $update_datas = json_decode(Redis::get('user_active_behavior'), true);
        if (empty($update_datas)) {
            Log::info('执行redis->mysql无数据同步' . time());
            return Command::SUCCESS;
        }
        SystemLogService::addLogs($update_datas);
        Redis::del('user_active_behavior');
        Log::info('执行redis->mysql同步数据成功' . time());
        return Command::SUCCESS;
    }
}
