<?php


namespace App\Http\Controllers;

use App\Message;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
