<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Event;
use Illuminate\Support\Facades\Route;
use App\Models\Page;

class ProgramListsController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }
    public function index($code=""){
        $set=[];
        $set['title'] = "プログラム一覧";
        $currentRoute = Route::current()->getName();
        $set[ 'route_name' ] = $currentRoute;

        $set[ 'is_program_lists'   ] = true;

        //$set = Page::getContents(['route_name' => $currentRoute]);


        if(Event::checkCode($code)){


        }else{

        }
        return view('pages', $set);

    }
}
