<?php

use App\Http\Controllers\Front\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', [IndexController::class, "index"]);
// ::表示class為靜態(static)
// get - 超連結／網址，會有網址列
// '/' - 根目錄，即首頁，網址
// [] - 執行什麼程式
// IndexController::class, "index" - 執行IndexController這個程式，當中的index這個方法