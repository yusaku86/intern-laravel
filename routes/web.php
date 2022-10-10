<?php

use Illuminate\Support\Facades\Route;

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
// ログイン画面
Route::get('/login', [\App\Http\Controllers\AdminUser\LoginController::class, 'showLoginPage'])->name('login');

// ログイン認証
Route::post('/login', [\App\Http\Controllers\AdminUser\LoginController::class, 'login'])->name('login.execute');

// 以下ログイン済みのユーザーのみアクセス可能
Route::middleware('auth')->group(function () {

    // ログアウト
    Route::get('/logout', [\App\Http\Controllers\AdminUser\LoginController::class, 'logout'])->name('logout');

    // ホーム画面
    Route::get('/', \App\Http\Controllers\Home\IndexController::class)->name('home');

    // 診療時間関連
    Route::prefix('business_hour')->group(function () {
        // 診療時間画面表示
        Route::get('/', \App\Http\Controllers\BusinessHour\IndexController::class)->name('business_hour');
        Route::get('/{id}', \App\Http\Controllers\BusinessHour\IndexController::class)->name('business_hour.id');

        // 診療時間設定
        Route::post('/', [\App\Http\Controllers\BusinessHour\TimeController::class, 'executeQuery'])->name('business_hour.execute');
    });

    // 長期休暇関連
    Route::prefix('vacation')->group(function () {
        // 長期休暇画面表示
        Route::get('/', \App\Http\Controllers\Vacation\IndexController::class)->name('vacation');
        Route::get('/{id}', \App\Http\Controllers\Vacation\IndexController::class);

        // 長期休暇追加
        Route::post('/create', \App\Http\Controllers\Vacation\CreateController::class)->name('vacation.execute');

        // 長期休暇削除
        Route::get('/delete/{id}', \App\Http\Controllers\Vacation\DeleteController::class)->name('vacation.delete');
    });

    // ユーザー関連
    Route::prefix('account')->group(function () {
        // ユーザー一覧表示
        Route::get('/', \App\Http\Controllers\AdminUser\IndexController::class)->name('account.index');

        // ユーザー削除
        Route::get('/delete/{id}', \App\Http\Controllers\AdminUser\DeleteController::class)->name('account.delete');

        // ユーザーパスワード変更画面表示
        Route::get('/pass/{id}', [\App\Http\Controllers\AdminUser\UpdatePassController::class, 'showPage'])->name('account.change_pass');

        // ユーザーパスワード変更実行
        Route::post('/pass/{id}', [\App\Http\Controllers\AdminUser\UpdatePassController::class, 'updatePass'])->name('account.change_pass_execute');

        // ユーザー追加画面表示
        Route::get('/create', \App\Http\Controllers\AdminUser\IndexAddController::class)->name('account.create');

        // ユーザー追加を実行
        Route::post('/account/create', \App\Http\Controllers\AdminUser\CreateController::class)->name('account.create-execute');
    });

    // 病院登録関連
    Route::prefix('hospital')->group(function () {
        // 病院登録画面表示
        Route::get('/', \App\Http\Controllers\Hospital\IndexAddController::class)->name('hospital');

        // 病院登録実行
        Route::post('/', \App\Http\Controllers\Hospital\CreateController::class)->name('hospital.create');
    });
});
