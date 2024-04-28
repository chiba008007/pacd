<?php

namespace App\Http\Controllers;

use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class PresentationsController extends Controller
{
    private $presentation;
    private $event;

    public function __construct()
    {
        $current = Route::current();

        if ($current) {
            // 登録されていない発表番号の場合エラー
            if (!($this->presentation = Presentation::where('number', $current->number)->where('id',$current->presentation_id)->first())) {
                abort(404);
            }
            // 登録されていないファイルタイプの場合エラー
            if (!in_array($current->file_type, config('pacd.presentation_file.type'))) {
                abort(404);
            }

            $this->event = $this->presentation->presenter->attendee->event;
        }
    }

    public function get_file($number, $type, Request $request)
    {
        // ファイルが登録されてない場合エラー
        $path = $this->presentation->$type;
        if (!$path) {
            abort(404);
        }

        // 管理者以外の場合
        if (!Auth::guard('admin')->check()) {
            // 未ログインの場合ログインページにリダイレクト
            if (!Auth::check()) {
                return redirect()->guest('login');
            }

            // 支払い済みの参加者でない場合エラー
            /*
            $attendee = $this->event->attendees->where('user_id', Auth::id())->first();
            if (!$attendee || !$attendee->is_paid) {
                abort(404);
            }
            */
        }

        $fname = substr($path, strrpos($path, '/')+1);
        $disposition = ($request->download) ? 'attachment' : 'inline';
        return Storage::response($path, $fname, [['Content-Type' => Storage::mimeType($path)]], $disposition);
    }
}
