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
use Throwable;
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

        if (!$order) {
            Log::error("Order not found: {$this->orderId}");
            throw new \Exception("Order not found");
        }

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
            Log::warning("Order ID {$this->orderId} payment failed. Retrying...");
            throw new \Exception("Payment processing failed");

        }
        Log::info("Order {$this->orderId} payment completed.");

    }

    public function failed(Throwable $exception)
    {
        Log::error("Order processing job failed after retries for Order ID {$this->orderId}: " . $exception->getMessage());
    }



}
