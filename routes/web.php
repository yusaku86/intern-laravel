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
Route::get('/login', [\App\Http\Controllers\AdminUserController::class, 'indexLoginPage'])->name('login');

// ログイン認証
Route::post('/login', [\App\Http\Controllers\AdminUserController::class, 'login'])->name('login.execute');

// 以下ログイン済みのユーザーのみアクセス可能
Route::middleware('auth')->group(function () {

    // ログアウト
    Route::get('/logout', [\App\Http\Controllers\AdminUserController::class, 'logout'])->name('logout');

    // ホーム画面
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // 診療時間関連
    Route::prefix('business_hour')->group(function () {
        // 診療時間画面表示
        Route::get('/', [\App\Http\Controllers\BusinessHourController::class, 'index'])->name('business_hour');
        Route::get('/{id}', [\App\Http\Controllers\BusinessHourController::class, 'index'])->name('business_hour.id');

        // 診療時間設定
        Route::post('/', [\App\Http\Controllers\BusinessHourController::class, 'executeQuery'])->name('business_hour.execute');
    });

    // 長期休暇関連
    Route::prefix('vacation')->group(function () {
        // 長期休暇画面表示
        Route::get('/', [\App\Http\Controllers\VacationController::class, 'index'])->name('vacation');
        Route::get('/{id}', [\App\Http\Controllers\VacationController::class, 'index']);

        // 長期休暇追加
        Route::post('/create', [\App\Http\Controllers\VacationController::class, 'create'])->name('vacation.execute');

        // 長期休暇削除
        Route::get('/delete/{id}', [\App\Http\Controllers\VacationController::class, 'delete'])->name('vacation.delete');
    });

    // ユーザー関連
    Route::prefix('account')->group(function () {
        // ユーザー一覧表示
        Route::get('/', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('account.index');

        // ユーザー削除
        Route::get('/delete/{id}', [\App\Http\Controllers\AdminUserController::class, 'delete'])->name('account.delete');

        // ユーザーパスワード変更画面表示
        Route::get('/pass/{id}', [\App\Http\Controllers\AdminUserController::class, 'indexChangePass'])->name('account.change_pass');

        // ユーザーパスワード変更実行
        Route::post('/pass/{id}', [\App\Http\Controllers\AdminUserController::class, 'updatePass'])->name('account.change_pass_execute');

        // ユーザー追加画面表示
        Route::get('/create', [\App\Http\Controllers\AdminUserController::class, 'indexAddPage'])->name('account.create');

        // ユーザー追加を実行
        Route::post('/account/create', [\App\Http\Controllers\AdminUserController::class, 'create'])->name('account.create-execute');
    });

    // 病院登録関連
    Route::prefix('hospital')->group(function () {
        // 病院登録画面表示
        Route::get('/', [\App\Http\Controllers\HospitalController::class, 'index'])->name('hospital');

        // 病院登録実行
        Route::post('/', [\App\Http\Controllers\HospitalController::class, 'create'])->name('hospital.create');
    });

    // CSVダウンロード
    Route::prefix('download')->group(function () {
        // CSVダウンロード画面表示
        Route::get('/', [\App\Http\Controllers\DownloadController::class, 'index'])->name('download.index');
        // 診療時間CSVダウンロード
        Route::get('/business_hour/{id}', [\App\Http\Controllers\DownloadController::class, 'downloadBusinessHourCsv'])->name('downlaod.business_hour');
        // 長期休暇ダウンロード
        Route::get('/vacation/{id}', [\App\Http\Controllers\DownloadController::class, 'downloadVacationCsv'])->name('download.vacation');
    });
});
