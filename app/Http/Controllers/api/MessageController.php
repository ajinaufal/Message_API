<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\conversations;
use App\Models\messages;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function SendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required',
            'message' => 'required',
        ]);

        $errors = $validator->errors();
        $message = [
            'to' => $errors->first('to'),
            'message' => $errors->first('message'),
        ];
        $form_error = [
            'to' => $errors->has('to'),
            'message' => $errors->has('message'),
        ];

        if ($validator->fails()) {
            return response()->json([
                'message'           => "error validation",
                'check_validation'  => $message,
                'form'              => $form_error,
            ], 400);
        } else {

            $from = auth()->user()->id;
            $to = $request->to;
            $message = $request->message;
            if ($request->reply) {
            $reply = $request->reply;
            }else {
                $reply = null;
            }

            $create_conversation = conversations::create([
                'from' => $from,
                'to' => $to,
            ]);

            $create_message = messages::create([
                'user_id'           => $from,
                'conversation_id'   => $create_conversation->id,
                'reply_id'          => $reply,
                'message'           => $request->message,
                'seen'              => 'one',
            ]);

            if (!$create_message || $create_conversation) {
                return response()->json([
                    'message'               => 'error',
                    'check_create_message'   => $create_message,
                    'check_create_conversation'   => $create_conversation,
                ], 500);
            }
            return response()->json([
                'message' => 'successful',
                'message' => $create_message,
            ], 200);
        }
    }

    public function ReplyMessage(Request $request)
    {
        # code...
    }

    public function ConvListTwo(Request $request)
    {
        # code...
    }
    
    public function ConvListAll(Request $request)
    {
        # code...
    }
}
