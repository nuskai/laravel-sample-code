<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\User;
use App\Services\GocardlessService;
use Log;

class GoCardlessController extends Controller
{

  public function flowCallback(Request $request)
  {
    $user = User::where('gc_redirect_flow_id', $request->get('redirect_flow_id'))->first();
    $service = new GocardlessService;
    $flow = $service->completeRedirectFlows($user);
    flash(trans('auth/message.signup.success_and_require_login'), 'success');

    return Redirect::route("login");
  }

  public function webhook(Request $request)
  {
    $headers = getallheaders();
    $provided_signature = $headers["Webhook-Signature"];
    $service = new GocardlessService;
    $raw_payload = file_get_contents('php://input');
    $result = $service->handleEvent($raw_payload, $provided_signature);
    return response()->json($result);
  }
}
