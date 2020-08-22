<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class xapiController extends Controller
{
    //
    
    public function edgesync(Request $request)
    {
        $email = $request->get('email');
        $room0 = $request->get('room0');
        $room1 = $request->get('room1');
        $room2 = $request->get('room2');
        $room3 = $request->get('room3');
        $room4 = $request->get('room4');
        $room5 = $request->get('room5');
        $room6 = $request->get('room6');
        $room7 = $request->get('room7');
        $power_usage = $request->get('usage');
        ///////////////////////////////////////////////////////////////TODO 
        // Comment it later
        if($power_usage == null) $power_usage = 10;

        $roomArr = array($room0,$room1,$room2,$room3,$room4,$room5,$room6,$room7);
        $user = DB::table('users')->where('email', '=', $email)->first();
        if ($user == null) {
            return "invalid ";
        }
        if ($request->get('token') === $user->token) {
            $uid = $user->id;
            $rooms = DB::table('rooms')->where('user_id', '=', $uid)->get();
            $rooms_str = "";

                
            $date = now();
            $time = strtotime($date);
            $ago_time = $time - (545 * 60);   
            $date = date("Y-m-d H:i:s", $ago_time);

            $today_time = $time - ($time % 86400) - 18000;   // one day = 86400  => 24 hrs
            $today_date = date("Y-m-d", $today_time);

            $time_slot = (int)(($time - $today_time) / 14400);  // one slot = 14400   => 4 hrs

            //dump($today_date);
            //dump($time_slot);
            $power_log = DB::table('power_log')->where([['user_id', '=', $uid],['date', '=', $today_date]])->first();

            //dump($power_log);

            $slot_usage = 0;
            $slot_count = 0;
            if($power_log == null){        
                DB::insert('insert into power_log (user_id, date) values (?, ?)', [$uid, $today_date]);
            }
            else{ 
                switch ($time_slot) {
                    case 0:
                        $slot_usage = $power_log->log_0;
                        $slot_count = $power_log->log0_count;
                        break;
                    case 1:
                        $slot_usage = $power_log->log_1;
                        $slot_count = $power_log->log1_count;
                        break;
                    case 2:
                        $slot_usage = $power_log->log_2;
                        $slot_count = $power_log->log2_count;
                        break;
                    case 3:
                        $slot_usage = $power_log->log_3;
                        $slot_count = $power_log->log3_count;
                        break;
                    case 4:
                        $slot_usage = $power_log->log_4;
                        $slot_count = $power_log->log4_count;
                        break;
                    case 5:
                        $slot_usage = $power_log->log_5;
                        $slot_count = $power_log->log5_count;
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }

            $slot_usage = (($slot_usage * $slot_count) + $power_usage)/($slot_count + 1);
            $slot_count = $slot_count + 1;

            switch ($time_slot) {
                case 0:
                    DB::update('update power_log set log_0 = ?, log0_count = ? where user_id = ? AND date = ?',
                    [$slot_usage, $slot_count, $uid, $today_date]);
                    break;
                case 1:
                    DB::update('update power_log set log_1 = ?, log1_count = ? where user_id = ? AND date = ?',
                    [$slot_usage, $slot_count, $uid, $today_date]);
                    break;
                case 2:
                    DB::update('update power_log set log_2 = ?, log2_count = ? where user_id = ? AND date = ?',
                    [$slot_usage, $slot_count, $uid, $today_date]);
                    break;
                case 3:
                    DB::update('update power_log set log_3 = ?, log3_count = ? where user_id = ? AND date = ?',
                    [$slot_usage, $slot_count, $uid, $today_date]);
                    break;
                case 4:
                    DB::update('update power_log set log_4 = ?, log4_count = ? where user_id = ? AND date = ?',
                    [$slot_usage, $slot_count, $uid, $today_date]);
                    break;
                case 5:
                    DB::update('update power_log set log_5 = ?, log5_count = ? where user_id = ? AND date = ?',
                    [$slot_usage, $slot_count, $uid, $today_date]);
                    break;
                
                default:
                    # code...
                    break;
            }

            //dump($slot_usage);
            //dump($slot_count);

            foreach ($rooms as $key => $value) {
                //dump($value->changed_at );
                //dump($date );
                //dump($roomArr[$key]);
                if ($value->changed_at > $date && $value->priority == true){
                    $rooms_str .= sprintf(":%03d", $value->state); 
                    DB::update('update rooms set priority = ? where user_id = ? AND room_index = ?',
                    [false, $uid, $key]);

                }
                else{
                    $rooms_str .= ":500"; // Just rusbbish/invalid room state
                    if( $roomArr[$key] != null){
                                        
                        DB::update('update rooms set priority = ?, state = ? where user_id = ? AND room_index = ?',
                        [false, $roomArr[$key], $uid, $key]);
                    }
                }
            }
            //dump($rooms_str);
            //dd($rooms);
            return 'ok' . $rooms_str;
        }
        return "invalid";
    }


}