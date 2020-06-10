<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use ProductHelper;
class CreateStripePlan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $product;
    protected $productPrice;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $productPrice)
    {
        $this->product = $product;
        $this->productPrice = $productPrice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
      // Amount to stripe
      $amount = ProductHelper::stripeAmountCalculation($this->productPrice->currency, $this->productPrice->price);
      $plan_monthly = \Stripe\Plan::create(array(
          "amount" => $amount,
          "interval" => "month",
          "product" => array(
              "name" => $this->product->productName
          ),
          "currency" => $this->productPrice->currency
      ));
      $amount_yearly_plan = $amount * 10;
      $plan_yearly = \Stripe\Plan::create(array(
          "amount" => $amount_yearly_plan,
          "interval" => "year",
          "product" => array(
              "name" => $this->product->productName
          ),
          "currency" => $this->productPrice->currency
      ));
      $this->productPrice->stripe_plan_id = $plan_monthly->id;
      $this->productPrice->stripe_yearly_plan_id = $plan_yearly->id;
      $this->productPrice->save();
    }
}
