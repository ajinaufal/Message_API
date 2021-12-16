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
            $from = auth()->user()->id;
            $to = $request->to;
            $message = $request->message;
            if ($request->reply) {
                $reply = $request->reply;
            } else {
                $reply = null;
            }

            $conversations = conversations::where([['one', $from], ['two', $to]])->orwhere([['one', $to], ['two', $from]])->get();
            $conv_side_one = conversations::where('one', $from)->get();
            $conv_side_two = conversations::where('two', $from)->get();

            if ($conversations->count() == 0) {
                $create_conversation = conversations::create([
                    'one' => $from,
                    'two' => $to,
                ]);
                $seen = 'one';
            } else {
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
                    'To'      => $to,
                    'Message' => $create_message->message,
                    'reply'   => $request->reply,
                    'Time'    => $create_message->created_at,
                )
            ], 200);
        }
    }

    public function ConvListTwo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required',
        ]);

        $errors = $validator->errors();
        $message = [
            'to' => $errors->first('to'),
        ];
        $form_error = [
            'to' => $errors->has('to'),
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

            $conversations = conversations::where([['one', $from], ['two', $to]])->orwhere([['one', $to], ['two', $from]])->get();
            $conv_side_one = conversations::where('one', $from)->get();
            $conv_side_two = conversations::where('two', $from)->get();

            if ($conv_side_one->count() != 0) {
                foreach ($conv_side_one->first()->get_message as $key => $value) {
                    if ($value->seen == 'one') {
                        messages::find($value->id)->update([
                            'seen' => 'both',
                        ]);
                    }
                }
            } elseif ($conv_side_two->count() != 0) {
                foreach ($conv_side_two->first()->get_message as $key => $value) {
                    if ($value->seen == 'two') {
                        messages::find($value->id)->update([
                            'seen' => 'both',
                        ]);
                    }
                }
            }

            if ($conversations->count() != 0) {
                foreach ($conversations->first()->get_message as $key => $message) {
                    $chat[$key] = [
                        'user' => $message->from_id,
                        'message' => $message->message,
                        'time' => $message->created_at,
                    ];
                }
            }
        }
        if ($conversations->count() == 0) {
            return response()->json([
                'message'       => 'error',
                'check_data'    => $conversations,
            ], 500);
        }
        return response()->json([
            'message' => 'successful',
            'data' => $chat,
        ], 200);
    }

    public function ConvListAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
        ]);

        $errors = $validator->errors();
        $message = [
        ];
        $form_error = [
        ];
        
        if ($validator->fails()) {
            return response()->json([
                'message'           => "error validation",
                'check_validation'  => $message,
                'form'              => $form_error,
            ], 400);
        } else {
            $from = auth()->user()->id;
            $conversations = conversations::where('one', $from)->orwhere('two', $from)->get();
            foreach ($conversations as $key => $value) {
                if ($value->one == $from) {
                    $user_chat = $value->two;
                }elseif ($value->two == $from) {
                    $user_chat = $value->one;
                }
                $data[$key] = [
                    'from' => $user_chat,
                    'last message' => $value->get_message->last()->message,
                    'last message time' => $value->get_message->last()->created_at
                ];
            }
            
            return response()->json([
                'message' => 'successful',
                'data' => $data,
            ], 200);
        }
    }
}
