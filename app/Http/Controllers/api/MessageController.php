<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\conversations;
use App\Models\messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            $seen = null;
            $from = $request->from;
            $to = $request->to;
            $message = $request->message;
            if ($request->reply) {
                $reply = $request->reply;
            } else {
                $reply = null;
            }

            $conversations = conversations::where([['one', $request->from], ['two', $request->to]])->orwhere([['one', $request->to], ['two', $request->from]])->get();
            $conv_side_one = conversations::where('one', $request->from)->get();
            $conv_side_two = conversations::where('two', $request->from)->get();

            if ($conversations->count() == 0) {
                $create_conversation = conversations::create([
                    'one' => $from,
                    'two' => $to,
                ]);
                $seen = 'one';
            } else{
                $create_conversation = $conversations->first();
                if ($conv_side_one->count() != 0) {
                    $seen = 'one';
                } elseif ($conv_side_two->count() != 0) {
                    $seen = 'two';
                }
            }

            $create_message = messages::create([
                'from_id'           => $from,
                'conversation_id'   => $create_conversation->id,
                'reply_id'          => $reply,
                'message'           => $request->message,
                'seen'              => $seen,
            ]);

            if (!$create_message || !$create_conversation) {
                return response()->json([
                    'message'                   => 'error',
                    'check_create_message'      => $create_message,
                    'check_create_conversation' => $create_conversation,
                ], 500);
            }
            return response()->json([
                'message' => 'successful',
                'data' => array(
                    'From'    => $create_message->get_user->name,
                    'To'      => $request->to,
                    'Message' => $create_message->message,
                    'reply'   => $request->reply,
                    'Time'    => $create_message->created_at,
                )], 200);
        }
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
