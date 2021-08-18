<?php


namespace App\Http\Controllers;

use App\Message;
use Illuminate\Support\Facades\DB;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\Pusher;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $users = DB::table('users')->where('id', '!=', Auth::id())->get();
        
        return view('home',['users'=> $users]);
    }

    public function getMessage($user_id)
   {
    
     //getting from current user logged to the selected user
    $my_id = Auth::id();
    
    $messages = DB::table('messages')->where(function ($query) use ($user_id, $my_id){
     $query->where('from', $my_id)->where('to', $user_id);
    })->orWhere(function ($query) use ($user_id, $my_id){
        $query->where('from', $user_id)->where('to', $my_id);
    }) ->get();

    return view('messages.index', ['messages'=>$messages]);

   }

   public function sendMessage(Request $request){
       $user = Auth::user();
       
       $message = $user->messages()->create([ 
           'from'=>Auth::id(), 
           'to'=>$request->input('receiver_id'), 
           'message'=>$request->input('message'), 
           'is_read'=>0
        ]);

        $from=Auth::id();
        $to=$request->input('receiver_id');

       $options = array(
           'cluster'=> 'mt1',
           'useTLS' => true  
       );

       $pusher = new Pusher(
           env('PUSHER_APP_KEY'),
           env('PUSHER_APP_SECRET'),
           env('PUSHER_APP_ID'),
           $options
       );

       $data =  ['from'=>$from, 'to'=>$to];
       $pusher->trigger('my-channel', 'my-event', $data);

   }
}
