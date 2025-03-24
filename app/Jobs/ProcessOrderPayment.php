<?php

namespace App\Jobs;

use App\Models\Order;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessOrderPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    public $orderProcessingStatus= false;

    /**

     * The number of times the job may be attempted.

     *

     * @var int

     */

     public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::findOrFail($this->orderId);

        // Updates order status to processing.
        $order->update(['status' => 'processing']);
        $this->orderProcessingStatus = $order->status == 'processing';

        // Simulates an external payment API call (use sleep(2) to mimic delay).
        sleep(2);

        $orderStatus = [
            'completed',
            'failed'
        ];

        // Randomly marks the order as completed or failed.
        $randomStatusIndex = rand(0,1);

        $status = $orderStatus[$randomStatusIndex];

        $order->update(['status' => $status]);

        $isCompleted = $status === 'completed';

        // Logs success or failure.
        if($isCompleted) {
            Log::info("Order ".$order->id." completed");
        } else {
            Log::error("Order  ".$order->id." failed");
        }
    }

    public function failed(Exception $exception)
    {
        $order = Order::find($this->orderId);
        if ($order) {
            $order->update(['status' => 'failed']);
            Log::error("Job Order  ".$order->id." failed: ".$exception->getMessage());
        }
    }

}
