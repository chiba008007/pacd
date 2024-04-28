<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class UrlController extends Controller
{
    //
    public function index($id){

        $url = Url::find($id);
        return Storage::download('public/files/'.$url->filename, $url->filename);
    }
}
