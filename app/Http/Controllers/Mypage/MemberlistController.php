<?php

namespace App\Http\Controllers\Mypage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MemberlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    //
    public function index(Request $request){
        $user = Auth::user();
        //会員一覧取得
        $type_number = $user->type_number;
        $member = User::where('deleted_at',null)
            ->where('type_number',$type_number)
            ->where('id', '!=', $user->id);
        if($request->name){
            $member = $member->where('sei','LIKE','%'.$request->name.'%');
            $member = $member->orwhere('mei','LIKE','%'.$request->name.'%');
            $member = $member->orwhere('email','LIKE','%'.$request->name.'%');
        }
        $member = $member->paginate(15);

        $set[ 'member' ] = $member;
        $set['title'] = '会員一覧マイページ';
        $set['user'] = $user;
        $set['name'] = $request->name;

        $set[ 'params' ]['name'] = $request->name;
        return view('mypage.memberlist.index', $set);
    }
}
