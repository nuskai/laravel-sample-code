<?php
namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Log;

class StripeService {

  public function verifyRequest($raw_payload, $provided_signature){
    try {
      $endpoint_secret = getenv('STRIPE_ENPOINT_SECRET');
      $event = \Stripe\Webhook::constructEvent(
        $raw_payload, $provided_signature, $endpoint_secret
      );
    } catch(\UnexpectedValueException $e) {
      return false;
    } catch(\Stripe\Error\SignatureVerification $e) {
      return false;
    }
    return $event;
  }

  public function handleEvent($raw_payload, $provided_signature)
  {
    \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    $event = $this->verifyRequest($raw_payload, $provided_signature);
    if($event) {
      Log::info($event);
      switch ($event->type) {
        case "invoice.payment_succeeded":
          $this->processSuccessPayment($event);
          break;
        case "invoice.payment_failed":
          $this->processFailedPayment($event);
          break;
        default:
          Log::info("Don't know how to process an event with resource_type " . $event->type . "\n");
          break;

      }
      return true;
    }else{
      return false;
    }
  }

  public function processFailedPayment($event){
    $stripe_subscription_id = $event->data->object->subscription;
    $sub = \Laravel\Cashier\Subscription::where('stripe_id', $stripe_subscription_id)->first();
    if($sub){
      $user = $sub->user;
      if($user){
        $activation = Activation::where('user_id',$user->id)->get()[0];
        $activation->completed = 0;
        $activation->save();
      }
    }
  }


  public function processSuccessPayment($event){
    $stripe_subscription_id = $event->data->object->subscription;
    $sub = \Laravel\Cashier\Subscription::where('stripe_id', $stripe_subscription_id)->first();
    if($sub){
      $user = $sub->user;
      if($user){
        $activation = Activation::where('user_id',$user->id)->get()[0];
        $activation->completed = 1;
        $activation->save();
      }
    }
  }
}