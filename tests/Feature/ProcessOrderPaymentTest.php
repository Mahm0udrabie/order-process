<?php

namespace Tests\Feature;

use App\Jobs\ProcessOrderPayment;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ProcessOrderPaymentTest extends TestCase
{
    use RefreshDatabase; // Ensures fresh DB for each test
    /**
     * A basic feature test example.
     */
    public function test_order_process_and_retries(): void
    {
        Bus::fake();
        $order = Order::factory()->create();

        $this->assertEquals('pending', $order->status);

        ProcessOrderPayment::dispatch($order->id);

        Bus::assertDispatched(ProcessOrderPayment::class, function ($job) use ($order) {
            return $job->orderId === $order->id;
        });

        $job = new ProcessOrderPayment($order->id);

        try {
            $job->handle();
        } catch (\Exception $e) {
            Log::error("Job failed: " . $e->getMessage());
        }

        $order->refresh();

        $this->assertTrue($job->orderProcessingStatus);

         // Retry the job
         $job->handle();

         $order->refresh();

        $this->assertContains($order->status, ['completed', 'failed']);

    }
}
