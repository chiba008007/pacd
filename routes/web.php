<?php

use App\Models\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 認証系
Auth::routes([
    'register' => false
]);
Route::get('/register', [\App\Http\Controllers\PagesController::class, 'view'])->name('register');
Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);

// 未ログインでアクセス可
Route::group(['prefix' => '/'], function () {
    // pages
    if (Schema::hasTable('pages')) {
        if ($pages = Page::where(['is_opened' => true, 'is_form' => false])->get()) {
            foreach ($pages as $page) {
                Route::get($page['uri'], [\App\Http\Controllers\PagesController::class, 'view'])->name($page['route_name']);
                // ※ 会員登録、参加者、講演者登録ページは固定ルーティング
            }
        }
    }
    //reikai
    Route::get('/reikai', [\App\Http\Controllers\PagesController::class, 'page'])->name('reikai.page');

    //最新の討論会のイベントページ
    Route::get('/touronkai/event', [\App\Http\Controllers\PagesController::class, 'page'])->name('touronkai.page');
    //管理画面から登録されるイベントページ
    Route::get('/touronkai', [\App\Http\Controllers\PagesController::class, 'view'])->name('touronkai');

    //Route::get('/touronkai', [\App\Http\Controllers\PagesController::class, 'page'])->name('touronkai.page');
    Route::get('/kosyukai', [\App\Http\Controllers\PagesController::class, 'page'])->name('kosyukai.page');

    // 協賛企業
    Route::get('/kyosan/', [\App\Http\Controllers\PagesController::class, 'view'])->name('kyosan');

    //fileダウンロード
    Route::get('/download/{id}', [\App\Http\Controllers\DownloadController::class, 'index'])->name('download');
    Route::get('/url/{id}', [\App\Http\Controllers\UrlController::class, 'index'])->name('urldownload');

    Route::get('/mail/', [\App\Http\Controllers\mailTestController::class, 'index'])->name('mail.test');

    Route::post('/passwordreset/', [\App\Http\Controllers\PasswordResetController::class, 'index'])->name('password.reset');

    //パスワードをブラウザ上に表示
    Route::get('/createuser/', [\App\Http\Controllers\CreateUserController::class, 'index'])->name('createuser');
    //お問い合わせフォーム
    Route::get('/inquire/', [\App\Http\Controllers\InquireController::class, 'index'])->name('inquire');
    Route::post('/inquire/', [\App\Http\Controllers\InquireController::class, 'send'])->name('inquire.send');
});

// ログイン中のみアクセス可
Route::group(['prefix' => '/', 'middlewere' => ['auth']], function () {
    Route::get('/mypage', [\App\Http\Controllers\Mypage\MypageController::class, 'index'])->name('mypage');
    Route::get('/mypage/profile/update', [\App\Http\Controllers\Mypage\UpdateProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::put('/mypage/profile/update', [\App\Http\Controllers\Mypage\UpdateProfileController::class, 'update'])->name('mypage.profile.update');
    Route::get('/mypage/profile/update/password', [\App\Http\Controllers\Mypage\UpdateProfileController::class, 'edit_password'])->name('mypage.profile.edit.password');
    Route::put('/mypage/profile/update/password', [\App\Http\Controllers\Mypage\UpdateProfileController::class, 'update_password'])->name('mypage.profile.update.password');

    Route::get('/mypage/memberlist', [\App\Http\Controllers\Mypage\MemberlistController::class, 'index'])->name('mypage.memberlist');
    Route::post('/mypage/memberlist', [\App\Http\Controllers\Mypage\MemberlistController::class, 'index'])->name('mypage.memberlist');

    //納入状況
    Route::get('/mypage/profile/payment', [\App\Http\Controllers\Mypage\UpdateProfileController::class, 'payment'])->name('mypage.profile.payment');

    // 例会
    Route::get('/mypage/reikai', [\App\Http\Controllers\Mypage\ReikaiController::class, 'index'])->name('mypage.reikai');
    Route::get('/mypage/reikai/documents', [\App\Http\Controllers\Mypage\ReikaiController::class, 'showDocuments'])->name('mypage.reikai.documents');

    // 討論会
    Route::get('/mypage/touronkai', [\App\Http\Controllers\Mypage\TouronkaiController::class, 'index'])->name('mypage.touronkai');
    Route::get('/mypage/touronkai/documents', [\App\Http\Controllers\Mypage\TouronkaiController::class, 'showDocuments'])->name('mypage.touronkai.documents');

    // 企業協賛
    Route::get('/mypage/kyosan', [\App\Http\Controllers\Mypage\KyosanController::class, 'index'])->name('mypage.kyosan');
    Route::get('/mypage/kyosan/documents', [\App\Http\Controllers\Mypage\KyosanController::class, 'showDocuments'])->name('mypage.kyosan.documents');

    // 講習会
    Route::get('/mypage/kosyukai', [\App\Http\Controllers\Mypage\KosyukaiController::class, 'index'])->name('mypage.kosyukai');
    Route::get('/mypage/kosyukai/documents', [\App\Http\Controllers\Mypage\KosyukaiController::class, 'showDocuments'])->name('mypage.kosyukai.documents');

    //領収書ファイル作成
    Route::get('/mypage/pdf/{id}', [\App\Http\Controllers\PDFController::class, 'index'])->name('member.pdf');

    //参加用領収書ファイル作成
    Route::get('/mypage/pdf/join/{id}/{type?}/{uid?}/{code?}', [\App\Http\Controllers\PDFController::class, 'join'])->name('member.join.pdf');

    //領収書請求書ダウンロード
    Route::get('/mypage/pdf/dispalyPdf/{filecode}', [\App\Http\Controllers\PDFController::class, 'dispalyPdf'])->name('member.join.dispalyPdf');

    // 領収書ダウンロード
    Route::get('/mypage/pdf/kyosanInvoice/{type}/{filecode}/{no}', [\App\Http\Controllers\PDFController::class, 'kyosanInvoice'])->name('member.kyosan.invoice');



    //一括ダウンロード
    Route::get('/zip/{event_id?}/{type?}/{date?}', [\App\Http\Controllers\Admin\ZipController::class, 'index'])->name('member.zip');

    // Webex
    Route::get('/event/{id}/webex/{date?}', [\App\Http\Controllers\WebexController::class, 'index'])->name('event.webex');
    // 参加証
    Route::get('/event/{id}/paperoutput', [\App\Http\Controllers\PaperOutputController::class, 'index'])->name('event.paper');

    // 参加者・講演者管理
    foreach (config('pacd.category', ['']) as $prefix => $category) {
        if ($prefix == 'register') continue;
        Route::get('/' . $prefix . '/{event_code}/attendee', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Attendee\RegisterController@create')->name($prefix . '_attendee');
        Route::post('/' . $prefix . '/{event_code}/attendee', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Attendee\RegisterController@store')->name($prefix . '_attendee.store');
        Route::get('/' . $prefix . '/attendee/{attendee_id}/edit', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Attendee\UpdateController@edit')->name($prefix . '_attendee.edit');
        Route::put('/' . $prefix . '/attendee/{attendee_id}', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Attendee\UpdateController@update')->name($prefix . '_attendee.update');
        Route::get('/' . $prefix . '/attendee/{attendee_id}/presenter', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\RegisterController@create')->name($prefix . '_presenter');
        Route::post('/' . $prefix . '/attendee/{attendee_id}/presenter', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\RegisterController@store')->name($prefix . '_presenter.store');
        Route::get('/' . $prefix . '/presenter/{presenter_id}/complete', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\UpdateController@complete')->name($prefix . '_presenter.complete');
        Route::get('/' . $prefix . '/presenter/{presenter_id}/edit', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\UpdateController@edit')->name($prefix . '_presenter.edit');
        Route::put('/' . $prefix . '/presenter/{presenter_id}', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\UpdateController@update')->name($prefix . '_presenter.update');
        Route::get('/' . $prefix . '/presenter/presentation/{presentation_id}/edit', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\UpdatePresentationController@edit')->name($prefix . '_presenter.edit.presentation');
        Route::put('/' . $prefix . '/presenter/presentation/{presentation_id}', '\App\Http\Controllers\\' . ucfirst($prefix) .  '\Presenter\UpdatePresentationController@update')->name($prefix . '_presenter.update.presentation');
    }

    // 講演資料（予稿原稿等）管理
    Route::get('/presentation/{number}/{file_type}/{presentation_id?}', [\App\Http\Controllers\PresentationsController::class, 'get_file'])->name('presentation.get.file');
});


/*
|--------------------------------------------------------------------------
| Admin Web Routes
|--------------------------------------------------------------------------
*/

// 未ログインでアクセス可
Route::group(['prefix' => config('admin.uri'), 'middleware' => 'guest:admin'], function () {

    // 管理者アカウント関連
    Route::get('/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);

    Route::get('/register', [\App\Http\Controllers\Admin\Auth\RegisterController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [\App\Http\Controllers\Admin\Auth\RegisterController::class, 'register']);
    Route::post('/password/email', [\App\Http\Controllers\Admin\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/password/reset', [\App\Http\Controllers\Admin\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('/password/reset', [\App\Http\Controllers\Admin\Auth\ResetPasswordController::class, 'reset'])->name('admin.password.update');
    Route::get('/password/reset/{token}', [\App\Http\Controllers\Admin\Auth\ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');
});



// ログイン中のみアクセス可
Route::group(['prefix' => config('admin.uri'), 'middleware' => 'auth:admin', 'as' => 'admin.'], function () {
    Route::get('/qrhome', [\App\Http\Controllers\Admin\QrHomeController::class, 'index'])->name('qrhome');

    // 参加受付
    Route::get('/event/{id}/joinstatus/{attend_id}/{event_id}/{user_id}', [\App\Http\Controllers\Admin\QrHomeController::class, 'joinstatus'])->name('event.joinstatus');

    // 各イベントパスワード
    Route::get('/event/update/password', [\App\Http\Controllers\Admin\Auth\UpdateProfileController::class, 'showEventUpdatePasswordForm'])->name('event.password');
    Route::post('/event/update/password', [\App\Http\Controllers\Admin\Auth\UpdateProfileController::class, 'postEventUpdatePasswordForm'])->name('eventupdate.password');

    // 管理者アカウント関連
    Route::get('/', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');
    Route::post('/logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    Route::get('/profile/update/email', [\App\Http\Controllers\Admin\Auth\UpdateProfileController::class, 'showUpdateEmailForm'])->name('update.email');
    Route::post('/profile/update/email', [\App\Http\Controllers\Admin\Auth\UpdateProfileController::class, 'updateEmail']);
    Route::get('/profile/update/password', [\App\Http\Controllers\Admin\Auth\UpdateProfileController::class, 'showUpdatePasswordForm'])->name('update.password');
    Route::post('/profile/update/password', [\App\Http\Controllers\Admin\Auth\UpdateProfileController::class, 'updatePassword']);


    Route::get('/pages/password/check', [\App\Http\Controllers\Admin\PagesController::class, 'passwordcheck'])->name('pages.password.check');
    Route::post('/pages/password/check', [\App\Http\Controllers\Admin\PagesController::class, 'passwordchecked'])->name('pages.password.checked');

    // 公開ページ管理
    Route::get('/pages', [\App\Http\Controllers\Admin\PagesController::class, 'index'])->name('pages.index');
    Route::post('/pages/open', [\App\Http\Controllers\Admin\PagesController::class, 'open'])->name('pages.open');
    Route::get('/pages/edit/{id}', [\App\Http\Controllers\Admin\PagesController::class, 'edit'])->name('pages.edit');
    Route::post('/pages/edit/{id}', [\App\Http\Controllers\Admin\PagesController::class, 'editOrder'])->name('pages.edit');
    Route::post('/pages/update/{id}', [\App\Http\Controllers\Admin\PagesController::class, 'updateAjax'])->name('pages.update.ajax');
    Route::post('/pages/add/{id}', [\App\Http\Controllers\Admin\PagesController::class, 'addAjax'])->name('pages.add.ajax');
    Route::get('/pages/bannar/', [\App\Http\Controllers\Admin\PagesController::class, 'bannar'])->name('pages.banner');
    Route::post('/pages/bannar/', [\App\Http\Controllers\Admin\PagesController::class, 'bannarSetting'])->name('pages.banner.set');
    Route::get('/pages/bannar/new/{id?}', [\App\Http\Controllers\Admin\PagesController::class, 'bannarnew'])->name('pages.banner.new');
    Route::post('/pages/bannar/new', [\App\Http\Controllers\Admin\PagesController::class, 'bannarpost'])->name('pages.banner.post');
    Route::get('/pages/bannar/del/{id?}', [\App\Http\Controllers\Admin\PagesController::class, 'bannardel'])->name('pages.banner.del');

    Route::get('/pages/url/', [\App\Http\Controllers\Admin\PagesController::class, 'url'])->name('pages.url');
    Route::post('/pages/url/setting', [\App\Http\Controllers\Admin\PagesController::class, 'urlsetting'])->name('pages.url.setting');
    Route::get('/pages/url/delete/{id?}', [\App\Http\Controllers\Admin\PagesController::class, 'delete'])->name('pages.url.delete');

    Route::get('/pages/kyosan/', [\App\Http\Controllers\Admin\PagesController::class, 'kyosan'])->name('pages.kyosan');
    Route::post('/pages/kyosan/', [\App\Http\Controllers\Admin\PagesController::class, 'kyosanSet'])->name('pages.kyosan.post');


    Route::get('/pages/inquire', [\App\Http\Controllers\Admin\PagesController::class, 'inquire'])->name('pages.inquire');
    Route::post('/pages/inquire', [\App\Http\Controllers\Admin\PagesController::class, 'inquireset'])->name('pages.inquire.post');
    Route::get('/pages/inquire/{id?}', [\App\Http\Controllers\Admin\PagesController::class, 'inquiredel'])->name('pages.inquire.delete');


    // フォーム管理
    Route::get('/{category_prefix}/form/{form_prefix}', [\App\Http\Controllers\Admin\FormsController::class, 'index'])->name('form.index');
    Route::get('/{category_prefix}/form/{form_prefix}/create', [\App\Http\Controllers\Admin\FormsController::class, 'create'])->name('form.create');
    Route::post('/{category_prefix}/form/{form_prefix}', [\App\Http\Controllers\Admin\FormsController::class, 'store'])->name('form.store');
    Route::get('/{category_prefix}/form/{form_prefix}/{id}/edit', [\App\Http\Controllers\Admin\FormsController::class, 'edit'])->name('form.edit');
    Route::put('/{category_prefix}/form/{form_prefix}/{id}', [\App\Http\Controllers\Admin\FormsController::class, 'update'])->name('form.update');
    Route::delete('/{category_prefix}/form/{form_prefix}/{id}', [\App\Http\Controllers\Admin\FormsController::class, 'destroy'])->name('form.destroy');
    Route::get('/{category_prefix}/form/{form_prefix}/preview', [\App\Http\Controllers\Admin\FormsController::class, 'preview'])->name('form.preview');
    Route::post('/{category_prefix}/form/{form_prefix}/back', [\App\Http\Controllers\Admin\FormsController::class, 'back'])->name('form.back');

    // 会員管理
    Route::resource('/members', \App\Http\Controllers\Admin\MembersController::class, ['except' => ['show']]);
    
    Route::get('/members/password/check', [\App\Http\Controllers\Admin\MembersController::class, 'passwordcheck'])->name('members.password.check');
    Route::post('/members/password/check', [\App\Http\Controllers\Admin\MembersController::class, 'passwordchecked'])->name('members.password.checked');

    Route::get('/members/{id}/password', [\App\Http\Controllers\Admin\MembersController::class, 'edit_password'])->name('members.edit.password');
    Route::put('/members/{id}/password', [\App\Http\Controllers\Admin\MembersController::class, 'update_password'])->name('members.update.password');
    Route::get('/members/{id}/payment', [\App\Http\Controllers\Admin\MembersController::class, 'payment'])->name('members.payment');
    Route::post('/members/{id}/payment/paymentupdate', [\App\Http\Controllers\Admin\MembersController::class, 'paymentupdateAjax'])->name('members.update.payment');
    Route::post('/members/{id}/payment/addyear', [\App\Http\Controllers\Admin\MembersController::class, 'addyear'])->name('members.update.payment.addyear');
    
    Route::get('/members/{id}/payment/paymentupdate', [\App\Http\Controllers\Admin\MembersController::class, 'paymentupdateAjax'])->name('members.update.payment');

    Route::post('/members/invoiceDownloadAjax', [\App\Http\Controllers\Admin\MembersController::class, 'invoiceDownloadAjax'])->name('members.invoiceDownloadAjax');
    Route::post('/members/docDownloadUpdateAjax', [\App\Http\Controllers\Admin\MembersController::class, 'docDownloadUpdateAjax'])->name('members.docDownloadUpdateAjax');
    Route::post('/members/docAllCheck', [\App\Http\Controllers\Admin\MembersController::class, 'docAllCheck'])->name('members.docAllCheck');
    Route::get('/members/recipeStatusAjax/{status}', [\App\Http\Controllers\Admin\MembersController::class, 'recipeStatusAjax'])->name('members.recipeStatusAjax');

    //csv出力
    Route::get('/members/csv', [\App\Http\Controllers\Admin\MembersController::class, 'csv'])->name('members.csv');
    Route::get('/members/csv/parts/{id?}', [\App\Http\Controllers\Admin\MembersController::class, 'csv_parts'])->name('members.csv.parts');

    //領収書ファイル作成
    Route::get('/members/pdf/{id}/{uid?}/{type?}', [\App\Http\Controllers\PDFController::class, 'index'])->name('members.pdf');
    Route::get('/members/pdf/{id}/{uid?}/{type?}', [\App\Http\Controllers\PDFController::class, 'index'])->name('members.pdf');


    //会員一覧アップロード
    Route::get(
        '/members/upload/',
        [\App\Http\Controllers\Admin\MembersController::class, 'upload']
    )->name('members.upload');
    Route::post(
        '/members/upload/',
        [\App\Http\Controllers\Admin\MembersController::class, 'upload']
    )->name('members.upload');
    Route::get(
        '/members/template/',
        [\App\Http\Controllers\Admin\MembersController::class, 'template']
    )->name('members.template');
    //年会費請求書情報
    Route::get(
        '/members/payment/',
        [\App\Http\Controllers\Admin\MembersController::class, 'yearPayment']
    )->name('members.yearPayment');
    Route::post(
        '/members/payment/',
        [\App\Http\Controllers\Admin\MembersController::class, 'yearPaymentRegist']
    )->name('members.yearPaymentRegist');


    //請求書・領収書PDF一覧
    Route::get(
        '/members/storage/{page?}',
        [\App\Http\Controllers\Admin\StoragesController::class, 'list']
    )->name('members.storages');
    Route::post(
        '/members/storage/',
        [\App\Http\Controllers\Admin\StoragesController::class, 'setsession']
    )->name('members.storages.post');
    Route::get(
        '/members/storage/down/csv',
        [\App\Http\Controllers\Admin\StoragesController::class, 'setdown']
    )->name('members.storages.down');
    Route::get(
        '/members/storage/delete/{id}/',
        [\App\Http\Controllers\Admin\StoragesController::class, 'delete']
    )->name('members.storagesDelete');
    Route::get(
        '/members/storage/download/{id}/',
        [\App\Http\Controllers\Admin\StoragesController::class, 'download']
    )->name('members.storagesDownload');


    // 参加者管理
    Route::get('/{category_prefix}/attendees', [\App\Http\Controllers\Admin\AttendeesController::class, 'index'])->name('attendees.index');
    Route::get('/{category_prefix}/attendees/create', [\App\Http\Controllers\Admin\AttendeesController::class, 'create'])->name('attendees.create');
    Route::post('/{category_prefix}/attendees', [\App\Http\Controllers\Admin\AttendeesController::class, 'store'])->name('attendees.store');
    Route::get('/{category_prefix}/attendees/{attendee_id}/edit', [\App\Http\Controllers\Admin\AttendeesController::class, 'edit'])->name('attendees.edit');
    Route::put('/{category_prefix}/attendees/{attendee_id}', [\App\Http\Controllers\Admin\AttendeesController::class, 'update'])->name('attendees.update');
    Route::delete('/{category_prefix}/attendees/{attendee_id}', [\App\Http\Controllers\Admin\AttendeesController::class, 'destroy'])->name('attendees.destroy');
    Route::post('/attendees/get/{login_id}/{event_id?}', [\App\Http\Controllers\Admin\AttendeesController::class, 'get_user_attendee'])->name('attendees.get.user_attendee.ajax');

    Route::post('/{category_prefix}/attendees/recipedateAjax', [\App\Http\Controllers\Admin\AttendeesController::class, 'recipedateAjax'])->name('reikai.recipedateAjax');
    Route::get('/{category_prefix}/attendees/csv', [\App\Http\Controllers\Admin\AttendeesController::class, 'csvdownload'])->name('attendees.csv');


    Route::get('/{category_prefix}/attendees/invoiceStatusDownload/{sts?}/{code?}', [\App\Http\Controllers\Admin\AttendeesController::class, 'invoiceStatusDownload'])->name('attendees.invoiceStatusDownload');


    // 講演者管理
    Route::get('/{category_prefix}/presenters', [\App\Http\Controllers\Admin\PresentersController::class, 'index'])->name('presenters.index');
    Route::post('/{category_prefix}/presenters/checked', [\App\Http\Controllers\Admin\PresentersController::class, 'checked'])->name('presenters.checked');
    Route::get('/{category_prefix}/presenters/create', [\App\Http\Controllers\Admin\PresentersController::class, 'create'])->name('presenters.create');
    Route::post('/{category_prefix}/presenters', [\App\Http\Controllers\Admin\PresentersController::class, 'store'])->name('presenters.store');
    Route::get('/{category_prefix}/presenters/{presenter_id}/edit', [\App\Http\Controllers\Admin\PresentersController::class, 'edit'])->name('presenters.edit');
    Route::put('/{category_prefix}/presenters/{presenter_id}', [\App\Http\Controllers\Admin\PresentersController::class, 'update'])->name('presenters.update');
    Route::delete('/{category_prefix}/presenters/{presenter_id}', [\App\Http\Controllers\Admin\PresentersController::class, 'destroy'])->name('presenters.destroy');

    Route::get('/{category_prefix}/presenters/csv', [\App\Http\Controllers\Admin\PresentersController::class, 'csvdownload'])->name('presenters.csv');
    Route::post('{category_prefix}/presenters/zip', [\App\Http\Controllers\Admin\PresentersController::class, 'filedownload'])->name('presenters.file');


    // 講演管理
    Route::post('/presentations/get/{presentation_id}', [\App\Http\Controllers\Admin\PresentationsController::class, 'get'])->name('presentations.get');
    Route::post('/presentations/get/numbers/{event_id?}', [\App\Http\Controllers\Admin\PresentationsController::class, 'get_numbers'])->name('presentations.get.numbers');
    Route::get('/presentations/get/numbers/{event_id?}', [\App\Http\Controllers\Admin\PresentationsController::class, 'get_numbers'])->name('presentations.get.numbers2');

    Route::get('/reikai/password/check', [\App\Http\Controllers\Admin\ReikaiController::class, 'passwordcheck'])->name('reikai.password.check');
    Route::post('/reikai/password/check', [\App\Http\Controllers\Admin\ReikaiController::class, 'passwordchecked'])->name('reikai.password.checked');


    // 例会管理
    Route::get('/reikai/event/list', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_list'])->name('reikai.event.list');
    Route::get('/reikai/event/regist/{id?}', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_regist'])->name('reikai.event.register');
    Route::post('/reikai/event/regist/add', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_add'])->name('reikai.event.add');
    Route::get('/reikai/event/delete/{id}', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_delete'])->name('reikai.event.delete');
    Route::get('/reikai/event/program/{id?}', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_program'])->name('reikai.event.program.register');
    Route::post('/reikai/event/program/add', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_program_add'])->name('reikai.event.program.add');
    Route::post('/reikai/event/program/add/date', [\App\Http\Controllers\Admin\ReikaiController::class, 'dateAjax'])->name('reikai.event.program.register.date');
    Route::post('/reikai/event/program/add/get', [\App\Http\Controllers\Admin\ReikaiController::class, 'getAjax'])->name('reikai.event.get.ajax');
    Route::post('/reikai/event/list/enabled', [\App\Http\Controllers\Admin\ReikaiController::class, 'enableAjax'])->name('reikai.event.enable.ajax');
    Route::get('/reikai/event/upload/{id}', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_upload'])->name('reikai.event.upload');
    Route::post('/reikai/event/upload/add', [\App\Http\Controllers\Admin\ReikaiController::class, 'event_upload_add'])->name('reikai.event.upload.add');

    //一括ダウンロード
    Route::get('/zip/{event_id?}/{type?}/{date?}', [\App\Http\Controllers\Admin\ZipController::class, 'index'])->name('zipdownload');
    Route::get('/zip/{event_id?}/{type?}/{date?}/{flag?}', [\App\Http\Controllers\Admin\ZipController::class, 'index'])->name('zipdownloadtype');

    //支払い切り替え
    Route::post('/reikai/attendees/paymentupdate', [\App\Http\Controllers\Admin\MembersController::class, 'paymentupdateAtendeeAjax'])->name('reikai.payment');
    Route::post('/reikai/attendees/invoiceDownloadAjax', [\App\Http\Controllers\Admin\MembersController::class, 'invoiceJoinDownloadAjax'])->name('reikai.invoiceDownloadAjax');

    Route::post('/reikai/attendees/recipeStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'recipeStatusUpdateAjax'])->name('reikai.recipeStatusUpdate');
    Route::post('/reikai/attendees/joinStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'joinStatusUpdateAjax'])->name('reikai.joinStatusUpdate');


    //メール配信
    Route::get('/{category_prefix}/reikai/maillist', [\App\Http\Controllers\Admin\MailController::class, 'list'])->name('reikai.mail.list');
    Route::get('/{category_prefix}/reikai/mail/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'index'])->name('reikai.mail');
    Route::post('/{category_prefix}/reikai/mail/send', [\App\Http\Controllers\Admin\MailController::class, 'send'])->name('reikai.mail.send');
    Route::get('/{category_prefix}/reikai/mail/delete/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'delete'])->name('reikai.mail.delete');
    Route::get('/{category_prefix}/reikai/mail/mailsend/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'mailsend'])->name('reikai.mail.mailsend');


    Route::get('/touronkai/password/check', [\App\Http\Controllers\Admin\TouronkaiController::class, 'passwordcheck'])->name('touronkai.password.check');
    Route::post('/touronkai/password/check', [\App\Http\Controllers\Admin\TouronkaiController::class, 'passwordchecked'])->name('touronkai.password.checked');


    // 討論会管理
    Route::get('/touronkai/event/list', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_list'])->name('touronkai.event.list');
    Route::get('/touronkai/event/regist/{id?}', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_regist'])->name('touronkai.event.register');
    Route::post('/touronkai/event/regist/add', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_add'])->name('touronkai.event.add');
    Route::get('/touronkai/event/delete/{id}', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_delete'])->name('touronkai.event.delete');
    Route::get('/touronkai/event/program/{id?}', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_program'])->name('touronkai.event.program.register');
    Route::post('/touronkai/event/program/add', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_program_add'])->name('touronkai.event.program.add');
    Route::post('/touronkai/event/program/add/date', [\App\Http\Controllers\Admin\TouronkaiController::class, 'dateAjax'])->name('touronkai.event.program.register.date');
    Route::post('/touronkai/event/program/add/get', [\App\Http\Controllers\Admin\TouronkaiController::class, 'getAjax'])->name('touronkai.event.get.ajax');
    Route::post('/touronkai/event/list/enabled', [\App\Http\Controllers\Admin\TouronkaiController::class, 'enableAjax'])->name('touronkai.event.enable.ajax');
    Route::get('/touronkai/event/upload/{id}', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_upload'])->name('touronkai.event.upload');
    Route::post('/touronkai/event/upload/add', [\App\Http\Controllers\Admin\TouronkaiController::class, 'event_upload_add'])->name('touronkai.event.upload.add');

    //支払い切り替え
    Route::post('/touronkai/attendees/paymentupdate', [\App\Http\Controllers\Admin\MembersController::class, 'paymentupdateAtendeeAjax'])->name('touronkai.payment');

    Route::post('/touronkai/attendees/recipeStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'recipeStatusUpdateAjax'])->name('touronkai.recipeStatusUpdate');
    Route::post('/touronkai/attendees/joinStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'joinStatusUpdateAjax'])->name('touronkai.joinStatusUpdate');

    Route::get('/kyosan/password/check', [\App\Http\Controllers\Admin\KyosanController::class, 'passwordcheck'])->name('kyosan.password.check');
    Route::post('/kyosan/password/check', [\App\Http\Controllers\Admin\KyosanController::class, 'passwordchecked'])->name('kyosan.password.checked');

    // 企業協賛
    Route::get('/kyosan/event/list', [\App\Http\Controllers\Admin\KyosanController::class, 'event_list'])->name('kyosan.event.list');
    Route::get('/kyosan/event/regist/{id?}', [\App\Http\Controllers\Admin\KyosanController::class, 'event_regist'])->name('kyosan.event.register');
    Route::post('/kyosan/event/regist/add', [\App\Http\Controllers\Admin\KyosanController::class, 'event_add'])->name('kyosan.event.add');
    Route::get('/kyosan/event/delete/{id}', [\App\Http\Controllers\Admin\KyosanController::class, 'event_delete'])->name('kyosan.event.delete');


    Route::get('/kyosan/event/program/{id?}', [\App\Http\Controllers\Admin\KyosanController::class, 'event_program'])->name('kyosan.event.program.register');
    Route::post('/kyosan/event/program/add', [\App\Http\Controllers\Admin\KyosanController::class, 'event_program_add'])->name('kyosan.event.program.add');
    Route::post('/kyosan/event/program/add/date', [\App\Http\Controllers\Admin\KyosanController::class, 'dateAjax'])->name('kyosan.event.program.register.date');
    Route::post('/kyosan/event/program/add/get', [\App\Http\Controllers\Admin\KyosanController::class, 'getAjax'])->name('kyosan.event.get.ajax');
    Route::post('/kyosan/event/list/enabled', [\App\Http\Controllers\Admin\KyosanController::class, 'enableAjax'])->name('kyosan.event.enable.ajax');
    Route::get('/kyosan/event/upload/{id}', [\App\Http\Controllers\Admin\KyosanController::class, 'event_upload'])->name('kyosan.event.upload');
    Route::post('/kyosan/event/upload/add', [\App\Http\Controllers\Admin\KyosanController::class, 'event_upload_add'])->name('kyosan.event.upload.add');



    //支払い切り替え
    Route::post('/kyosan/attendees/paymentupdate', [\App\Http\Controllers\Admin\MembersController::class, 'paymentupdateAtendeeAjax'])->name('kyosan.payment');

    Route::post('/kyosan/attendees/recipeStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'recipeStatusUpdateAjax'])->name('kyosan.recipeStatusUpdate');
    Route::post('/kyosan/attendees/joinStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'joinStatusUpdateAjax'])->name('kyosan.joinStatusUpdate');



    Route::get('/kosyukai/password/check', [\App\Http\Controllers\Admin\KosyukaiController::class, 'passwordcheck'])->name('kosyukai.password.check');
    Route::post('/kosyukai/password/check', [\App\Http\Controllers\Admin\KosyukaiController::class, 'passwordchecked'])->name('kosyukai.password.checked');

    //技術講習会
    Route::get('/kosyukai/event/list', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_list'])->name('kosyukai.event.list');
    Route::get('/kosyukai/event/regist/{id?}', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_regist'])->name('kosyukai.event.register');
    Route::post('/kosyukai/event/regist/add', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_add'])->name('kosyukai.event.add');
    Route::get('/kosyukai/event/delete/{id}', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_delete'])->name('kosyukai.event.delete');
    Route::get('/kosyukai/event/program/{id?}', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_program'])->name('kosyukai.event.program.register');
    Route::post('/kosyukai/event/program/add', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_program_add'])->name('kosyukai.event.program.add');
    Route::post('/kosyukai/event/program/add/date', [\App\Http\Controllers\Admin\KosyukaiController::class, 'dateAjax'])->name('kosyukai.event.program.register.date');
    Route::post('/kosyukai/event/program/add/get', [\App\Http\Controllers\Admin\KosyukaiController::class, 'getAjax'])->name('kosyukai.event.get.ajax');
    Route::post('/kosyukai/event/list/enabled', [\App\Http\Controllers\Admin\KosyukaiController::class, 'enableAjax'])->name('kosyukai.event.enable.ajax');
    Route::get('/kosyukai/event/upload/{id}', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_upload'])->name('kosyukai.event.upload');
    Route::post('/kosyukai/event/upload/add', [\App\Http\Controllers\Admin\KosyukaiController::class, 'event_upload_add'])->name('kosyukai.event.upload.add');

    //支払い切り替え
    Route::post('/kosyukai/attendees/paymentupdate', [\App\Http\Controllers\Admin\MembersController::class, 'paymentupdateAtendeeAjax'])->name('kosyukai.payment');

    Route::post('/kosyukai/attendees/recipeStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'recipeStatusUpdateAjax'])->name('kosyukai.recipeStatusUpdate');
    Route::post('/kosyukai/attendees/joinStatusUpdate', [\App\Http\Controllers\Admin\MembersController::class, 'joinStatusUpdateAjax'])->name('kosyukai.joinStatusUpdate');


    //メール配信
    Route::get('/{category_prefix}/kosyukai/maillist', [\App\Http\Controllers\Admin\MailController::class, 'list'])->name('kosyukai.mail.list');
    Route::get('/{category_prefix}/kosyukai/mail/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'index'])->name('kosyukai.mail');
    Route::post('/{category_prefix}/kosyukai/mail/send', [\App\Http\Controllers\Admin\MailController::class, 'send'])->name('kosyukai.mail.send');
    Route::get('/{category_prefix}/kosyukai/mail/delete/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'delete'])->name('kosyukai.mail.delete');
    Route::get('/{category_prefix}/kosyukai/mail/mailsend/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'mailsend'])->name('kosyukai.mail.mailsend');


    //メール内容編集
    Route::get('{category_prefix}/mailform/{id?}', [\App\Http\Controllers\Admin\MailFormController::class, 'index'])->name('mailform.index');
    Route::post('{category_prefix}/mailform/edit/{id?}', [\App\Http\Controllers\Admin\MailFormController::class, 'edit'])->name('mailform.edit');
    Route::post('{category_prefix}/mailform/{id}/get', [\App\Http\Controllers\Admin\MailFormController::class, 'getAjax'])->name('mailform.getAjax');


    //メール配信
    Route::get('/{category_prefix}/toronkai/maillist', [\App\Http\Controllers\Admin\MailController::class, 'list'])->name('touronkai.mail.list');
    Route::get('/{category_prefix}/toronkai/mail/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'index'])->name('touronkai.mail');
    Route::post('/{category_prefix}/toronkai/mail/send', [\App\Http\Controllers\Admin\MailController::class, 'send'])->name('touronkai.mail.send');
    Route::get('/{category_prefix}/toronkai/mail/delete/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'delete'])->name('touronkai.mail.delete');
    Route::get('/{category_prefix}/toronkai/mail/mailsend/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'mailsend'])->name('touronkai.mail.mailsend');

    //メール配信
    Route::get('/{category_prefix}/kyosan/maillist', [\App\Http\Controllers\Admin\MailController::class, 'list'])->name('kyosan.mail.list');
    Route::get('/{category_prefix}/kyosan/mail/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'index'])->name('kyosan.mail');
    Route::post('/{category_prefix}/kyosan/mail/send', [\App\Http\Controllers\Admin\MailController::class, 'send'])->name('kyosan.mail.send');
    Route::get('/{category_prefix}/kyosan/mail/delete/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'delete'])->name('kyosan.mail.delete');
    Route::get('/{category_prefix}/kyosan/mail/mailsend/{id?}', [\App\Http\Controllers\Admin\MailController::class, 'mailsend'])->name('kyosan.mail.mailsend');

    // 状態切替
    Route::post('/{category_prefix}/attendees/changeFlag', [\App\Http\Controllers\Admin\AttendeesController::class, 'changeFlag'])->name('attendee.changeFlag');
});
