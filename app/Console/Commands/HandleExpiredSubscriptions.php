<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HandleExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:handle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover expired subscription and change it status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::table('subscriptions')
            ->where('status', '<>', Subscription::SUBSCRIPTION_STATUS_EXPIRED)
            ->where('end_date', '<', DB::raw('CURRENT_DATE'))
            ->update(['status' => Subscription::SUBSCRIPTION_STATUS_EXPIRED])
        ;
    }
}
