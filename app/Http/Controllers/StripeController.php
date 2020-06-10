<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Services\StripeService;
use Log;

class StripeController extends Controller
{

  public function webhook(Request $request)
  {
    $raw_payload = file_get_contents('php://input');
    $provided_signature = $_SERVER['HTTP_STRIPE_SIGNATURE'];
    $service = new StripeService;
    $result = $service->handleEvent($raw_payload, $provided_signature);
    return response()->json($result);
  }
}
