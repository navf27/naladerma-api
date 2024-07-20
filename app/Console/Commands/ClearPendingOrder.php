<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearPendingOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-pending-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingOrders = Order::where('status', 'pending')->get();
        $dataDeleted = 0;
        $now = Carbon::now();

        foreach ($pendingOrders as $order) {
            $createdTime = Carbon::parse($order->created_at);
            $diffInHours = $createdTime->diffInHours($now);

            if ($diffInHours >= 1) {
                $order->delete();
                $dataDeleted = $dataDeleted + 1;
            }
        }

        info('Pending order deleted : ', $dataDeleted);
    }
}
