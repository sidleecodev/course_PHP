---
aliases:
  - Laravel 易混淆觀念
  - PHP 問題複習
---

# PHP／Laravel 提問與易混淆觀念整理

> 整理範圍：原第 1～5 階段，加上追加三天的提問、除錯與整合測試，截至 2026-10-26。
> 以老師的命名與課堂流程為主；「容易混淆」是複習提醒，不代表目前仍不理解。
> 閱讀方式：先看粗體重點，再依問題查表。這份是觀念對照，不是完整程式碼。

## 先複習這 6 組差別

| 容易混在一起 | 要分開記住 |
| --- | --- |
| 網址／實體檔案路徑 | 瀏覽器請求網址；Laravel 再決定如何處理，不是直接找同名 Blade 檔 |
| 檔案／類別／物件／方法 | 檔案存程式，類別定義功能，new 建立物件，方法是可呼叫的功能 |
| 資料庫／資料表／Model | 資料庫放資料表；資料表存紀錄；Model 是操作資料的 PHP 類別 |
| 整份列表／單筆資料 | get() 的 $list 是集合；paginate() 的 $list 是分頁物件；foreach 的 $data 是目前這一筆 |
| 查詢／顯示 | PHP 查詢決定拿到哪些資料；Blade 決定顯示哪些資料 |
| 設定／實際動作 | $fillable、路由別名等是設定；不代表已新增資料或執行功能 |

## 1. 工具與學習目標

| 我提出的問題 | 正確理解 | 快速對照 |
| --- | --- | --- |
| PHP、Composer、Laravel 是什麼？ | PHP 是語言；Composer 管理 PHP 套件與依賴；Laravel 是用 PHP 寫成的網站框架 | 語言／套件管理／框架 |
| 只知道 MVC 架構就夠嗎？需要從零做後臺嗎？ | 現階段先能解釋、重做與小改；求職作品再練到能參考文件完成自己的小型功能 | 可以查資料，不是靠默寫全部語法 |
| MVC 是什麼？ | Model 處理資料與相關規則；View 顯示畫面；Controller 協調請求處理 | 課堂 Model 主要透過 Eloquent 操作資料庫 |
| 五天真的能學會嗎？ | 五階段目標是理解、完成課堂功能並練習修改，不等於熟練 PHP 或可安全上線 | 依理解前進，不必照日曆硬趕 |
| php artisan serve 是「開機」嗎？ | 它啟動 Laravel 本機開發伺服器 | 終端機需保持執行；Ctrl + C 停止 |
| 連結只在 VS Code 預覽，如何改成 Chrome？ | 取消 workbench.browser.openLocalhostLinks；可將 workbench.externalBrowser 設成 chrome | 終端機通常用 Ctrl + 點擊連結 |

## 2. 檔案用途與相互關係

| 檔案／位置 | 負責什麼 | 容易搞混的地方 |
| --- | --- | --- |
| routes/web.php | 設定網址、請求方式、控制器方法及 Middleware | 路由指定類別與方法，不是直接執行同名模板 |
| app/Http/Controllers/Admin/AdminController.php | 登入頁、接收登入表單、回傳首頁 | 控制器的預設位置包含 Controllers，不能漏掉這層 |
| app/Http/Controllers/Admin/ManagerController.php | 取得分頁與總筆數、回傳列表或新增表單畫面 | 與負責登入的 AdminController 分工不同 |
| app/Models/Admin/Manager.php | 定義資料表對應與資料操作 | 這是 PHP 檔案，不是資料庫檔案 |
| app/Http/Middleware/CheckManager.php | 進入受保護路由前，檢查 Session | Middleware 不是 Model，也不是資料表 |
| bootstrap/app.php | 啟動相關設定，例如路由檔、Middleware、例外處理 | 不是專門存放別名的檔案 |
| .env | 本機環境設定，例如資料庫連線與 Session 儲存方式 | 寫 DB_DATABASE 不會自動建立資料庫 |
| config/session.php | Session 功能的設定 | 設定儲存方式不等於已經登入 |
| app/Providers/AppServiceProvider.php | 服務與初始化設定，目前設定分頁模板 | 不需要為此修改 vendor |
| resources/views/admin/manager/create.blade.php | 新增帳號表單畫面 | 顯示表單不代表已經寫入資料庫 |
| resources/views | Blade 模板目錄 | 不是網站對外的公開根目錄 |
| public | 網站公開資源目錄 | 一般資源網址不用包含 public |

### 一次頁面請求的分工

| 情境 | 路由 | 控制器方法 | 回傳畫面 |
| --- | --- | --- | --- |
| 前臺首頁 | GET / | IndexController 的 index() | front/index.blade.php |
| 後臺登入頁 | GET /admin | AdminController 的 index() | admin/login.blade.php |
| 表單送出 | POST /admin/login | AdminController 的 login() | 課堂流程依結果轉址 |
| 後臺首頁 | GET /admin/home | AdminController 的 home() | admin/home.blade.php |
| 帳號列表 | GET /admin/manager/list | ManagerController 的 list() | admin/manager/list.blade.php |
| 新增帳號表單 | GET /admin/manager/create | ManagerController 的 create() | admin/manager/create.blade.php |
| 儲存新增帳號（已完成） | POST /admin/manager/store | store() | 寫入後轉址列表；完整後端驗證尚待補強 |

> 受保護的後臺路由會先經過 CheckManager；登入頁與登入提交不套用這個登入檢查。

## 3. 網址、斜線與資源路徑

以下以目前網址 `http://127.0.0.1:8000/admin/home` 為例。

| 我提出的問題 | 正確理解 | 例子／結果 |
| --- | --- | --- |
| 路由裡的 / 指哪裡？ | 網站首頁路徑，不是電腦的資料夾 | / → http://127.0.0.1:8000/ |
| href 裡的開頭 / 是目前檔案位置嗎？ | 不是；表示從目前網站根路徑開始 | /css/style.css → /css/style.css |
| 沒有開頭 / 呢？ | 瀏覽器依目前頁面網址計算相對位置，不看 Blade 存放位置 | css/style.css → /admin/css/style.css |
| public/css/style.css 如何被請求？ | 正常 Laravel 公開目錄設定下，用 /css/style.css | 不用寫 /public/css/style.css |
| 為什麼登入頁正常，後臺首頁卻跑版？ | 相對資源路徑可能在不同網址深度解析成不同位置 | /admin 與 /admin/home 的相對路徑結果可能不同 |
| 要建立 public/admin/css 嗎？ | 目前已加上 /css/... 並正常，不需要為此複製一份資源 | 保留 public/css、public/js 即可 |
| 後臺模板為什麼有 href="/"？ | 按鈕用途是讓管理員開啟前臺；點擊才跳轉 | 不是模板自動離開後臺 |
| href="/admin/logout" 就完成登出了嗎？ | 沒有；還要有接收路由與清除登入狀態的程式 | 目前已完成完整登出流程，見第 19 節 |
| Request::is 為什麼寫 admin/home 而不是 /admin/home？ | 它比對的是 Laravel 整理後、不含前導斜線的請求路徑 | Request::is('admin/home') |
| 表單 action="/admin/login" 是另一個網站嗎？ | 是同一網站內的處理網址，不是另一個網站或必須存在的 HTML 檔 | 用 POST 把帳密交給後端 |
| prefix 群組怎麼組成網址？ | 外層與內層前綴依序相加 | admin + manager + list → /admin/manager/list |

> **不是每個含路徑的地方都套同一規則。**href 是瀏覽器連結；Request::is 是框架的路徑比對；view('admin.home') 是模板名稱。

## 4. 類別、檔案、use、物件與方法

| 我提出的問題 | 正確理解 | 例子 |
| --- | --- | --- |
| AdminController 不就是 AdminController.php 嗎？ | 前者是類別名稱；後者是存放程式碼的檔案名稱 | class AdminController 寫在 AdminController.php 裡 |
| 完整類別名稱看起來是路徑，為什麼不是檔案？ | 它由命名空間與類別名稱組成；Composer 的對應規則讓它能被換算成檔案位置 | App\\Http\\Controllers\\Admin\\AdminController |
| use 是引入 PHP 檔案嗎？ | use 建立名稱對應，讓目前檔案能使用簡短類別名稱；它本身不等於載入檔案 | use App\\Models\\Admin\\Manager; |
| 實際檔案怎麼找到？ | 自動載入機制依專案規則找到類別檔案；Laravel 常見設定將 App 命名空間映射到 app/ | App\\Models\\Admin\\Manager → app/Models/Admin/Manager.php |
| 方法是什麼？ | 類別裡定義的一個功能 | public function index() |
| public 是什麼？ | 允許類別外部呼叫這個方法或存取這個屬性 | 路由可呼叫公開的 index() |
| protected 是什麼？ | 限制直接存取的範圍：類別內及繼承關係中的程式使用 | 是 PHP 存取權限，不是資料庫權限 |
| :: 兩邊都是同一種操作嗎？ | 同為 PHP 範圍解析語法，但用途依後面內容而異 | Route::get(...) 呼叫路由功能；Manager::class 取得類別名稱 |
| self 是 manager 資料表嗎？ | self 指定義這段程式的類別，這裡是 Manager；$table 再決定操作哪張表 | self::where(...) |
| 為什麼要 new Manager()？ | 老師的 getManager() 是物件方法，先建立 Manager 物件才能這樣呼叫 | $model = new Manager(); |
| new 會新增帳號嗎？ | 不會；建立 PHP 物件與寫入資料庫是兩回事 | new Manager() 不等於新增一筆資料 |
| -> 是什麼？ | 用來存取物件的屬性或呼叫方法 | $req->userId、$model->getManager(...) |
| Manager::where 為何又不用先 new？ | Eloquent 對查詢有特殊轉送機制，不能推論所有物件方法都能用類別::方法呼叫 | 自訂 getManager() 先依老師做法建立物件 |

### 將原本一行拆開看

```php
// 建立負責查詢的 Model 物件
$model = new Manager();

// 呼叫查詢方法，接住回傳的結果
$manager = $model->getManager($userId, $pwd);
```

等同於課堂這行：

```php
$manager = (new Manager())->getManager($userId, $pwd);
```

> **負責查詢的 $model 與查詢結果 $manager 是不同角色。**成功時結果也是一個 Manager 物件，但它代表查到的那一筆紀錄。

## 5. 資料庫、Model 設定與查詢

| 我提出的問題 | 正確理解 | 對照／例子 |
| --- | --- | --- |
| Model 是資料庫嗎？固定要用資料庫嗎？ | Model 不是資料庫；MVC 的 Model 可處理不同來源資料，課堂 Eloquent Model 主要對應資料表 | Manager 類別操作 manager 表 |
| 資料庫與資料表有什麼不同？ | 資料庫容納資料表；表內有欄位與紀錄 | project1151013 裡的 manager |
| phpMyAdmin 建資料庫等於 migrate 嗎？ | 建立空資料庫與依 migration 建立／修改資料表，是不同動作 | 目前手動建表，沒有要求額外 migrate |
| make:model 會建立資料表嗎？ | 目前使用的不帶其他選項指令，只產生 Model 檔案 | php artisan make:model Admin/Manager |
| 空資料表也有欄位嗎？ | 有；空表只是沒有資料列，仍可有欄位結構 | id、userId、pwd |
| Model 中 $table 等是資料表自帶的欄位嗎？ | 不是；它們是 Laravel 會讀取的 PHP 類別屬性 | $table 指向表名，$primaryKey 指向主鍵名 |
| protected $table = "manager" | 指定表名，不建立表；左邊屬性名稱按框架規則，右邊對應實際表名 | manager 資料表 |
| protected $primaryKey = "id" | 告訴 Model 主鍵欄位名稱，不會替資料庫建立主鍵或自動遞增 | PRIMARY 與 A_I 要在資料表設定 |
| $timestamps = false | 停用自動維護 created_at、updated_at，不建立或刪除欄位 | 課堂表沒有這兩欄 |
| $fillable 到底做什麼？ | 列出允許透過 create／fill 等批次賦值方式填入的欄位 | 是允許清單，不是寫入動作，也不是查詢欄位清單 |
| 新增帳號一定在 phpMyAdmin 做嗎？ | 可手動新增，也可之後由 PHP 接收表單後寫入；資料表要先存在 | 目前先手動加入測試帳號 |
| id、userId、pwd 是欄位嗎？ | 是，它們是這張 manager 表的實際欄位名稱 | 不是每張 Laravel 資料表都固定有這三欄 |
| string $userId 只是消除提示嗎？ | 不是；它是參數型別宣告，參與型別檢查／轉換規則 | 表示參數使用字串型別 |
| where("userId", $userId) 左右分別是什麼？ | 左邊字串指定資料表欄位；右邊變數提供這次要比對的值 | userId 欄位等於輸入帳號 |
| 兩個 where 是任一符合就好嗎？ | 此寫法是 AND，帳號與密碼必須同時符合 | where(...)->where(...) |
| 為什麼取 first()？帳號應該只有一筆吧？ | first() 決定只取得單一結果或 null；帳號唯一性則需另外保障 | 即使帳號唯一，get() 仍回傳集合 |
| 會有兩筆一樣的帳密嗎？ | 只限制 id 唯一時，其他欄位仍可能重複；可對 userId 設 UNIQUE 防止重複帳號 | first() 不會阻止或修復重複資料 |
| 直接改 get() 有差嗎？ | 有；查不到時 get() 回傳空集合物件，不是 null，不能沿用同樣的 empty 判斷 | 集合要用適當的判斷，例如 isEmpty() |
| $manager 是 true／false 嗎？ | 不是；first() 的結果是單筆 Model 或 null；!== null 的比較才產生布林值 | dd($manager !== null) |
| 密碼沒顯示，是不是沒查回來？ | Manager::get() 未指定欄位時預設查所有欄位；Blade 可只顯示部分欄位 | 有查到 pwd，不代表要顯示 pwd |

## 6. 表單、錯誤訊息、Session 與轉址

| 我提出的問題 | 正確理解 | 課堂例子 |
| --- | --- | --- |
| Request $req 是什麼？ | Laravel 提供目前請求物件，$req 是自取的參數名稱 | login(Request $req) |
| $req->userId 對應 id 還是 name？ | 對應送出欄位的 name；此處是讀取表單輸入 | name="userId" |
| id 有什麼用？ | 識別網頁元素，供 label、CSS、JS 使用；不決定表單資料名稱 | label 的 for、CSS 的 #account |
| id 與 name 能不同嗎？ | 可以；例如 id="account"、name="userId"，後端仍讀 $req->userId | 網頁定位與傳送名稱分工不同 |
| 登入畫面的 msg 還沒建立嗎？ | msg 是自訂錯誤訊息鍵；先寫顯示條件沒問題，有訊息才顯示 | withErrors(["msg" => "..."]) |
| $errors 是自己建立的嗎？ | 標準 web 流程會由 Laravel 提供錯誤袋；msg 則是自己取名 | has("msg") 檢查、first("msg") 取第一則 |
| back／withInput／withErrors 的順序固定嗎？ | 此寫法先 back() 得到轉址回應；後兩個方法在這個情境可以交換 | 不是所有方法鏈都能任意換順序 |
| 為何登入失敗後帳號沒保留？ | 後端要暫存輸入，模板也要用 old() 回填 | withInput() + value="{{ old('userId') }}" |
| 密碼框空白就表示沒暫存密碼嗎？ | 不一定；沒用 old('pwd') 只是畫面不回填，自訂密碼欄位仍要明確排除暫存 | 課堂 withInput() 不是正式安全版本 |
| Session 的用途？ | 讓不同請求能延續目前使用者的狀態 | 登入後記住帳號 |
| put("userId", $userId) 左右是什麼？ | 第一個是 Session 的鍵；第二個是要存的值，不是指定資料表欄位 | get("userId") 用相同的鍵讀取 |
| redirect("/admin/home") 是跳到 home.blade.php 嗎？ | 先請瀏覽器前往網址，再由路由呼叫 home()，最後由 view 選模板 | redirect 用網址，view 用模板名 |
| dd() 為什麼後面不執行？ | 顯示資料後停止執行；正式流程測試前要移除 | 移除 dd 後才能進入 if／else |

## 7. Middleware 與別名

| 我提出的問題 | 正確理解 | 對照 |
| --- | --- | --- |
| 只做登入頁就能保護後臺嗎？ | 不能；使用者仍可直接輸入網址，受保護路由要做登入檢查 | CheckManager 檢查 Session |
| manager 別名是在註冊會員嗎？ | 不是；是註冊 Middleware 的別名 | manager → CheckManager::class |
| 為什麼要註冊別名？ | 讓 Laravel 知道路由中的簡短字串指哪個 Middleware | ->middleware('manager') |
| 別名一定要用嗎？ | 不一定，也能直接指定 Middleware 類別 | 別名是方便閱讀與重用 |
| bootstrap/app.php 只存別名嗎？ | 不是；它包含路由、Middleware、例外處理等啟動設定 | 別名放在 withMiddleware 裡 |
| 同樣叫 manager，都是一個東西嗎？ | 不同位置代表不同角色，見下表 | 不能只靠名稱判斷 |
| return $next($request) 是什麼？ | 放行請求，繼續後續處理 | 沒登入則先 redirect 並結束方法 |
| 為什麼用無痕視窗測？ | 避免原本已登入的 Session 干擾測試 | 未登入應被擋，登入後可放行 |

| 寫法 | 角色 |
| --- | --- |
| Manager | PHP Model 類別 |
| manager（資料庫中的表） | 資料表名稱 |
| CheckManager | Middleware 類別 |
| middleware('manager') | Middleware 別名 |
| prefix('manager') | 網址前綴 |

## 8. Blade 版型、符號與選單判斷

| 我提出的問題 | 正確理解 | 例子 |
| --- | --- | --- |
| 所有頁面都用 layout，所以側欄也改 layout 嗎？ | 外框引入了 menu；側欄內容要改 menu，頂部列與頁尾改 layout | @include('admin.menu') |
| 共用版型代表只更新右邊、不載入整頁嗎？ | 不代表；目前仍是一般網址切頁，共用模板由伺服器重新組合 | 不是已完成 AJAX／SPA |
| extends／include 有何不同？ | extends 套用外框；include 在指定位置引入小模板 | home extends layout；layout include menu |
| yield／section 有何不同？ | yield 預留插槽；section 提供內容；名稱要一致 | title、content |
| title 改哪裡？ | 要分瀏覽器分頁標題、頂部列標題、內容標題，並不是同一個位置 | 目前 yield('title') 在頂部列；head 的 title 仍可能寫死 |
| PHP { } 是什麼？ | 包住 class、方法、if 等程式區塊 | function home() { ... } |
| Blade {{ }} 是什麼？ | 輸出資料，預設做 HTML 跳脫 | {{ $data->userId }} |
| Blade {!! !!} 是什麼？ | 不跳脫，直接輸出內容；不可任意用於不可信輸入 | 固定的 class 屬性字串 |
| Blade {{-- --}} 是什麼？ | Blade 註解，不顯示在回應 HTML | 不是一般 PHP 的 /* */ |
| @if／@foreach 要加 { } 嗎？ | Blade 使用自己的結束指令 | @endif、@endforeach |
| 條件 ? A : B 是什麼？ | 三元運算子：成立採 A，不成立採 B | 目前路徑符合才加 active |
| 不成立的 '' 是什麼？ | 空字串，什麼也不輸出；不是空白，也不是 null | 不輸出 class 屬性 |
| 空字串不能用 "" 嗎？ | 可以，'' 與 "" 都能表示空字串 | 但非空內容的兩種引號仍有其他 PHP 語法差異 |
| '' 等於 class="" 嗎？ | 不等於；前者完全沒內容，後者有一個值為空的 HTML 屬性 | 沒加 class 與加上空 class 要分清 |
| admin/manager/* 是什麼？ | 以萬用字元匹配管理員功能的後續路徑 | list、create、edit/1 都可符合 |
| 加上 active 就會自動變色嗎？ | class 只標記狀態，實際外觀需要 CSS 配合 | 老師 style.css 定義 active 樣式 |

### 空字串的差異

```blade
{!! Request::is('admin/home') ? "class='active'" : '' !!}
```

| 條件 | 實際產生的連結示意 |
| --- | --- |
| 成立 | `<a href="/admin/home" class='active'>Home</a>` |
| 不成立 | `<a href="/admin/home">Home</a>` |
| 若不成立分支改成 'class=""' | `<a href="/admin/home" class="">Home</a>` |

## 9. 傳資料與 foreach 迴圈

| 我提出的問題 | 正確理解 | 對照 |
| --- | --- | --- |
| 控制器裡有 $list，View 就會自動有嗎？ | 不會；要把要用的資料傳給 View | view(..., ["list" => $list]) |
| ["list" => $list] 左右是什麼？ | 左邊決定模板收到的變數名稱；右邊是實際要傳的內容 | 模板裡使用 $list |
| 改成 ["accounts" => $list] 呢？ | 資料內容不變，模板變數改成 $accounts | foreach 也要用 $accounts |
| compact("list") 是什麼？ | 找到當前作用域的 $list，建立同名鍵值陣列 | 相當於 ["list" => $list] |
| compact 是查資料或渲染嗎？ | 都不是；它是 PHP 建立陣列的函式 | 查詢是 Manager::get；畫面交給 view |
| $list 與 $data 有什麼不同？ | get() 時 $list 是集合；現在 paginate() 時是分頁物件；$data 仍是這一輪的單筆 Model | foreach($list as $data) |
| 為何要特別取名 $data，不能只用 $list？ | 要有名稱指向目前單筆資料；不是把整份列表重新命名 | $list->userId 不是「逐筆取帳號」 |
| 一定要叫 $data 嗎？ | 不一定，可改 $manager 等名稱，但迴圈內要一致 | 名稱自取，角色固定 |
| 為什麼要把整個 tr 包在 foreach 裡？ | 每一輪產生完整一列，讓一筆資料對應一列 | 3 筆資料 → 3 個 tr |
| 為何原例只有 id、userId，沒有 pwd？ | 查回資料與畫面選擇顯示是兩件事 | 沒寫 $data->pwd 就不會輸出它 |
| 修改連結用 $list->id 還是 $data->id？ | 用目前這筆的 $data->id | /admin/manager/edit/2 |
| 新增帳號連結是第幾天？ | 第 5 階段：連結、create 路由、方法、表單頁 | 尚未包含 store 寫入功能 |

### 第四階段的資料傳遞範例（當時使用 get）

```php
// 控制器
$list = Manager::get();

return view("admin.manager.list", ["list" => $list]);
// 等同於 return view("admin.manager.list", compact("list"));
```

```blade
{{-- View --}}
@foreach($list as $data)
    <tr>
        <td>{{ $data->userId }}</td>
    </tr>
@endforeach
```


## 10. 第五階段：分頁、links 與總筆數

| 我提出的問題／測試結果 | 正確理解 | 對照／例子 |
| --- | --- | --- |
| $list->links() 裡的 $list 是什麼？ | 它是控制器透過 paginate() 取得、再傳入模板的分頁物件 | $list = Manager::paginate(10); |
| 原本 $list 是集合，怎麼又是物件？ | 變數名稱不決定內容型別；集合本身也是物件，但與分頁物件是不同類別 | get() 回傳集合；paginate() 回傳含資料與分頁資訊的物件 |
| ->links() 是產生切頁連結嗎？ | 是，呼叫分頁物件的方法，產生分頁連結的 HTML／可渲染內容 | 不會單靠這個方法完成資料庫查詢 |
| {{ $list->links() }} 為什麼能顯示按鈕？ | links() 回傳的是框架可渲染的 HTML 內容，因此可用這種標準寫法輸出 | 不要把它理解成一般使用者字串都不會跳脫 |
| 改 paginate() 後 foreach 要改嗎？ | 不用；分頁物件可以被迴圈逐筆讀取，取出的是目前頁面的資料 | $data 仍是當前一筆 Manager |
| paginate(10) 的 10 與 $total 有何不同？ | 10 是每頁上限；課堂 $total = Manager::count() 是整張表總筆數 | 23 筆可分為 10、10、3，總數都是 23 |
| 第二頁怎麼被查出來？ | 點連結發出新請求，Laravel 預設讀 page 參數，paginate 查該頁資料 | /admin/manager/list?page=2 |
| 第一頁 2 筆、第二頁 1 筆是否錯誤？ | 若 paginate(2) 且總數 3，這是正常頁面分配 | 兩頁的「共幾筆」都應為 3 |
| 如果 paginate(10)，總共只有 3 筆呢？ | 第一頁顯示 3 筆，通常不會有第二頁分頁按鈕 | 每頁最多 10，不要求一定湊滿 10 |
| 看不到分頁是不是壞了？ | 可能資料不足一頁，可暫時縮小每頁筆數測試 | 3 筆搭配 paginate(2) |
| 分頁樣式是在哪裡設定？ | app/Providers/AppServiceProvider.php 的 boot() | 引入 Paginator 後呼叫 Paginator::useBootstrap() |
| useBootstrap() 會自動安裝 CSS 嗎？ | 不會；它選擇分頁模板，外觀仍需相容 CSS | 此處沿用老師的版型設定 |
| 為什麼共幾筆也變成 2、1？ | 這次是 data-total 對應的前端 JS 改寫畫面數字；後端 $total 原本正確 | 移除 data-total 後兩頁皆為 3，證實此案例 |
| data-total 是 Laravel 計算總數的指令嗎？ | 不是，是 HTML 自訂 data 屬性，可供 JS 選取元素；它本身不會計算 | JS 選取此元素後才可能改文字 |
| 移除 data-total 會改變資料庫或 PHP 計數嗎？ | 不會；移除的是前端標記，這次避免 JS 再覆寫這個數字 | 保留 {{ $total }} 作為總數來源 |
| 既然 paginate 已有總數，為什麼老師還 count？ | 是課堂目前的寫法，先理解兩個變數；paginate 結果本身也提供 total() | 現階段保留老師的 Manager::count()，不要混用兩種計數概念 |

### 同一個 $list 的兩個用途

| 使用位置 | 操作什麼 |
| --- | --- |
| @foreach($list as $data) | 取出目前頁面的每筆資料 |
| {{ $list->links() }} | 產生切頁按鈕 |
| {{ $total }} | 顯示另外傳入的總筆數，不是 foreach 目前迴圈次數 |

```php
// 第五階段：老師的控制器寫法
$list = Manager::paginate(10);
$total = Manager::count();

return view("admin.manager.list", compact("list", "total"));
```

```blade
{{-- 保留原本表格與 foreach；在表格下方顯示總數與分頁 --}}
<div class="admin-table-info">
    <span>共 <b>{{ $total }}</b> 筆</span>
    <nav class="pagination">
        {{ $list->links() }}
    </nav>
</div>
```

> **本次已確認的修正：移除總數元素的 data-total，避免老師版型 JS 用目前頁面列數改寫總數。**不是所有 data-* 屬性都要移除，也不是 PHP 的 count() 算錯。

## 11. 第五階段當時：新增表單與尚待實作的寫入

> 本節保留當時的疑問；追加三天已完成 store，參見第 17 節。

| 我提出的問題／理解確認 | 正確理解 | 對照／例子 |
| --- | --- | --- |
| 新增帳號的連結是第五天嗎？ | 是；目前已完成連結、路由、控制器方法與表單 View | 帳號列表 → 新增帳號頁 |
| create() 是控制器裡的方法嗎？ | 是，與 list() 並列，放在 ManagerController 類別內 | 不要把 create() 寫進 list() |
| create() 是導向 View 嗎？ | 可理解成選擇畫面，但精確說法是「回傳新增表單畫面」，不是 redirect 轉址 | return view("admin.manager.create"); |
| 方法叫 create()，就會建立一筆資料嗎？ | 不會，實際執行什麼由方法內程式決定；目前只有回傳 View | 不會因方法命名自動 INSERT |
| 新增頁路由如何找到 View？ | GET create 路由呼叫 create()，由方法回傳對應模板 | resources/views/admin/manager/create.blade.php |
| 表單 action="/admin/manager/store" 是什麼？ | 指定未來提交資料的位置；網址出現在 HTML 不代表後端已完成 | 必須另外建立 POST 路由、store 方法與儲存邏輯 |
| create 與 store 差在哪裡？ | create 顯示填寫畫面；store 預計接收資料、驗證並寫入 | 顯示表單與處理送出是兩次請求 |
| 新增頁還需要 Middleware 嗎？ | 需要，放在目前 manager 登入檢查群組裡 | 不能只靠側欄不顯示連結來保護 |
| 新增頁為什麼帳號管理選單還亮著？ | Request::is('admin/manager/*') 也符合 create 路徑 | 不需要另寫一個相同選單 |
| 表單能顯示，就代表完成新增帳號了嗎？ | 只完成新增畫面；這次五階段尚未寫入資料庫 | 不把尚未建立 store 的送出錯誤當作本段故障 |
| 模板 JS 彈出新增成功，算寫入成功嗎？ | 不一定；要確認後端確實寫入資料庫，不能只看提示視窗 | 目前尚未實作儲存 |

### 三種 create 不要混在一起

| 寫法 | 是什麼 | 是否自動寫入資料庫 |
| --- | --- | --- |
| 網址 /admin/manager/create | 開啟新增頁的路徑 | 否 |
| 控制器 public function create() | 我們定義的方法，目前回傳 View | 否 |
| Eloquent 的 Manager::create([...]) | Model 提供的新增資料方法，需搭配允許欄位等設定 | 正確執行時會寫入；目前課堂尚未使用 |

### view 與 redirect 再對照

| 寫法 | 接收的內容 | 實際用途 |
| --- | --- | --- |
| view("admin.manager.create") | 模板名稱 | 產生新增頁畫面，不另外要求瀏覽器轉址 |
| redirect("/admin/manager/create") | 網址路徑 | 要求瀏覽器前往該網址，再由路由處理 |


## 12. 實際遇過的錯誤與排查位置

| 錯誤／現象 | 當時的重點 | 優先檢查 |
| --- | --- | --- |
| Driver [Session] not supported | Session 儲存方式讀到不支援的 Session 名稱 | .env 的 SESSION_DRIVER=file；config/session.php 的 driver；必要時 config:clear |
| Target class [manager] does not exist | 路由使用了尚未註冊的 Middleware 別名 | bootstrap/app.php 的 use、alias；類別是否已建立；不要誤改 Model |
| 419 PAGE EXPIRED | 通常是 CSRF 權杖與 Session 不符合；不代表必然同一原因 | 重新開登入頁再送出；@csrf；Session 設定；主機名稱保持一致 |
| 登入頁正常、layout 跑版 | 本機 CSS／JS 相對網址解析錯誤 | 使用 /css/style.css、/js/main.js、/js/admin.js |
| 點帳號管理出現 404 | 請求網址可能沒有對應路由 | href、list／lsit 拼字、prefix 是否重複、route:list |
| 登入失敗沒有回填帳號 | 暫存輸入與畫面回填必須配合 | withInput()、old('userId')、name 是否一致 |
| 登入成功但首頁沒顯示帳號 | 老師 HTML 不會自動插入 Session 值 | 在 Blade 加入 {{ session()->get("userId") }} |
| Collection::links does not exist | 使用 get() 的集合沒有分頁按鈕方法 | 第五階段已改用 paginate()，確認 $list 仍是分頁物件再呼叫 links() |
| 共幾筆隨頁面變成 2／1，但資料庫有 3 筆 | 前端 JS 可能把後端總數改成目前頁面列數；移除 data-total 後兩頁都為 3，已定位此案例 | 目前保留 <b>{{ $total }}</b>；不要只看 PHP，也要檢查 JS 對畫面的改寫 |
| helpers.php 黃色波浪線、PHP0443 警告 | PHP Tools 的靜態診斷，不等於實際執行錯誤或檔案修改 | 不更改 vendor 原碼；確認網站功能，依診斷來源處理 |
| helpers.php 檔名旁顯示 8 | 截圖中的數字表示警告數量，不是 8 次修改 | 檢查問題面板、分頁未儲存圓點與 Git 狀態，三者要分清 |
| 錯誤顯示 vendor/... | 框架拋出錯誤的位置，不一定是問題源頭 | 優先找自己的路由、設定、控制器、View，不修改 vendor |


### 補充：vendor 檔案警告，不等於被修改

| 我提出的問題／截圖現象 | 正確理解 | 處理方式 |
| --- | --- | --- |
| 沒動 helpers.php，為什麼突然出現問題？ | 開啟檔案後，擴充套件可能對套件原始碼顯示診斷；不代表檔案內容被改動 | 先確認是檢查提示、未儲存編輯，還是 Git 差異 |
| Too many arguments to function callable() 是什麼？ | 工具認為呼叫接受 0 個參數，程式卻傳入 1 個；這次提示來源為 PHP Tools | 動態呼叫可能讓靜態分析誤判，不能憑此斷定網站壞掉 |
| 截圖的 PHP(PHP0443) 是什麼？ | DEVSENSE PHP Tools 的診斷代碼，對應參數過多 | 是編輯器分析，不是瀏覽器回傳的 Laravel 錯誤頁 |
| helpers.php 旁的 8 代表異動嗎？ | 截圖中的 8 是警告數量，不是修改次數 | 檔案實際是否改變，要另外比較檔案或 Git 差異 |
| 黃色波浪線、藍色提示、分頁圓點、M 有何不同？ | 黃線是警告；藍色是資訊或建議；圓點通常是未儲存編輯；Git M 表示已追蹤檔案有差異 | 不把問題數量當作檔案修改 |
| 要修改 vendor 中的程式消除警告嗎？ | 不要為此改框架原碼；先確認自己的網站執行正常 | 關閉套件檔案、重載 VS Code；需要時只隱藏 vendor 的診斷 |

必要時在 VS Code 的 Workspace Settings (JSON) 合併加入以下設定；**不是寫在 PHP 檔案裡，也不要覆蓋其他設定**：

```json
{
    "php.problems.exclude": {
        "**/vendor/**": true
    }
}
```

這是 PHP Tools 的設定，只隱藏符合路徑的診斷，不會修復或改寫套件，也不會關閉你自己的控制器、Model 等檔案的檢查。

參考：[PHP Tools 診斷排除設定](https://docs.devsense.com/vscode/problems/suppressing-diagnostics/)。


### 常用檢查指令

| 指令 | 用途 |
| --- | --- |
| `php artisan route:list --path=admin` | 查看後臺相關路由是否存在 |
| `php artisan route:clear` | 清除路由快取 |
| `php artisan config:clear` | 清除設定快取 |
| `php artisan serve` | 啟動本機開發伺服器 |

> 查錯先看「錯誤名稱、請求網址、自己的程式位置」，不用先讀完全部 Stack Trace。分享訊息時不必附上 Cookie 或整份 .env。

## 13. 截至本次整理的學習進度

| 階段 | 完成內容 | 狀態 |
| --- | --- | --- |
| 第 1 階段 | 環境建置、啟動 Laravel | 已完成 |
| 第 2 階段 | 登入頁、路由、控制器、表單 | 已完成 |
| 第 3 階段 | 帳密查詢、失敗訊息、Session、轉址、Middleware | 已完成並測試 |
| 第 4 階段 | 共用版型、列表資料、選單高亮、老師版型整合 | 已完成並測試 |
| 第 5 階段 | 分頁、總筆數、新增帳號連結與表單 | 已完成並測試 |

**五階段目標已完成並通過操作測試；這不等於已完成整套正式後臺。**

**更新：追加三天已完成查重、實際新增、修改、刪除、登出與整合測試。**尚未完成的是正式專案所需的後端驗證、唯一性、權限、雜湊等補強。原第 11 節描述的是第五階段當時的進度，後續結果以第 15～21 節為準。

## 14. 下次複習的順序

- [ ] 說出「網址、模板名稱、實體檔案路徑」的差別。
- [ ] 說出「類別、物件、方法」各指程式哪一部分。
- [ ] 用自己的話讀懂 where("userId", $userId)。
- [ ] 分清單筆結果、null、集合與布林值。
- [ ] 說明 compact 如何把控制器的資料交給 View。
- [ ] 說明 foreach 每輪的 $data 與整份 $list 的差別。
- [ ] 分清 @include、@extends、@yield、@section。
- [ ] 能從錯誤訊息選出應先檢查的檔案。
- [ ] 分清 paginate 的每頁筆數、當頁列數與資料總筆數。
- [ ] 說明 $list->links() 使用什麼資訊，點擊後如何查下一頁。
- [ ] 畫面數字不符時，知道 JS 也可能在載入後改寫內容。
- [ ] 分清 create() 回傳表單、store() 處理儲存，以及 Model 的 create([...])。
- [ ] 分清 VS Code 靜態警告與實際檔案變更。

> 不必背全部內容。每次挑一個問題，在自己的專案做一次小修改並預測結果，再用瀏覽器驗證。


## 15. 追加三天：AJAX、JSON 與帳號查重

| 我提出的問題／現象 | 精準理解 | 本次處理 |
| --- | --- | --- |
| AJAX 是網頁與資料的橋梁嗎？ | 可作初步理解；它讓 JS 向伺服器發出請求、取得回應，不必重新載入整頁 | AJAX 不直接操作資料庫，仍經 Route、Controller、Model |
| 不能直接查詢後台嗎？ | 目前就是請後端查詢；AJAX 是傳送請求的方式 | 不用 AJAX 也能在表單送出後檢查，但回饋時機不同 |
| AJAX 與 JSON 是同一個嗎？ | AJAX 是通訊方式，JSON 是資料格式 | 這次回應是純文字 exist，不是 JSON |
| 離開欄位與按儲存是一回事嗎？ | 是兩次不同請求 | onblur → checkAccount；submit → store |
| success 表示帳號可以使用嗎？ | 表示請求成功收到回應，業務結果要再判斷 | msg.trim() == "exist" 才是老師約定的重複結果 |
| JS、Controller、Model 都叫 checkAccount，會自動相連嗎？ | 不會，它們位於不同語言或類別 | JS url → Route 指定 Controller → Controller 呼叫 Model |
| 檢查帳號需要比對密碼嗎？ | 此功能只確認 userId 是否存在 | 與登入的帳密查詢不同 |
| Console 顯示中文字，但輸入值是 undefined | 表示程式有執行，但未取得預期值；本次先檢查選取器與 id | id="userId" 對應 $("#userId")，大小寫一致 |
| 只有 name="userId" 不行嗎？ | 後端依 name 接收，這段前端 JS 則依 id 選取 | id、name 分工不同 |
| 點密碼框，游標一直跳回帳號 | onblur 送查重，收到 exist 後 .focus() 移回帳號，再離開又檢查 | 既有帳號是老師預期效果，換不存在帳號測試 |
| 游標跳回但沒有提示 | HTML 缺少 id="msg" 的顯示位置 | 補 span，查詢前清空舊提示 |
| AJAX 檢查過就保證不重複嗎？ | 不保證；使用者可繞過前端，亦可能有同時送出的請求 | 正式版本要在儲存後端檢查並搭配資料庫 UNIQUE |

## 16. 模板 class、攔截表單與 undefined 轉址

| 我提出的問題／現象 | 原因或正確理解 | 已驗證處理 |
| --- | --- | --- |
| 為什麼 class 叫 admin-form？ | 模板作者自行命名，供 CSS／JS 選取，不是 Laravel 指令 | 不會因名稱有 admin 自動連到後台 |
| class、action、method 差在哪？ | class 用於樣式或 JS；action 決定送往哪裡；method 決定 HTTP 方法 | 三者各有用途 |
| 出現「已新增」，資料庫卻沒有資料 | 模板直接顯示 Swal 成功，沒有將表單送往 Laravel | 成功提示不等於資料庫寫入成功 |
| 儲存後到 /admin/manager/undefined | JS 阻止表單送出，再讀取缺少的 form.dataset.back 轉址 | 排查 submit、preventDefault、location.href |
| 加 data-back 就可以嗎？ | 只修正轉址，仍沒有送出 POST | 不能當成新增成功的解法 |
| 不想改 JS，有其他方法嗎？ | 本檔使用 form.admin-form 選取表單，可以調整 HTML，讓表單不匹配 | 將 class 移到外層 div，form 移除該 class |
| 移動後仍失敗 | 外層誤打 admin-from，且 form 的 admin-form 未移除 | 正確拼字，不能兩邊都加 |
| class 只是外觀嗎？ | 也能被 JS 選取並綁定行為 | 本次移除 form 上的標記，才恢復正常 POST |
| 會影響 CSS 嗎？ | 視選擇器而定，限定 form 的規則可能受影響 | 保留外層 class，但不保證所有模板都適用同一修法 |

```html
<!-- 本次成功的結構；欄位與 @csrf 仍保留在 form 裡 -->
<div class="admin-card-body admin-form">
    <form action="/admin/manager/store" method="post">
        <!-- 原表單內容 -->
    </form>
</div>
```

修改頁同理。不要為修正新增表單而移除整份 admin.js，也不要任意刪除所有 data-* 屬性。

## 17. 新增與修改：物件不等於資料庫紀錄

| 我提出的問題／回答 | 精準理解 | 對照 |
| --- | --- | --- |
| new Manager() 會多一筆嗎？ | 建立物件本身不會寫入 | 對新物件呼叫 save() 才新增 |
| 哪一行寫入資料庫？ | 本次 store() 的 $manager->save() | 設定 userId、pwd 時仍在物件中 |
| $manager->userId = $req->userId | 右邊是請求傳入值；左邊是 Model 上對應欄位的屬性 | 不是直接讓 Request 變成資料表 |
| 逐欄指定後 save() 受 fillable 限制嗎？ | 這種直接指定屬性的方式不受大量賦值允許清單限制 | fillable 也不負責驗證輸入 |
| 修改為什麼帶 id？ | 唯一主鍵辨識原紀錄；就算帳號唯一，也可能被修改 | 不靠新帳號或密碼猜原本是哪筆 |
| 帳號一定可重複嗎？ | 依資料庫限制決定；正式帳號通常需唯一，密碼可相同 | 不要由需要 id 推論帳號應允許重複 |
| 修改時沒有要新增物件？ | find() 也會取得代表既有紀錄的 Model 物件 | 要區分新紀錄物件與既有紀錄物件 |
| 為何不用 new Manager() 修改？ | new 準備新紀錄；find 取得既有紀錄 | 新物件 save 會新增；既有物件保存會更新 |
| update() 怎麼知道哪筆？ | 先用送來的 id find，再對該物件設定欄位與 update | 保留同一 id，總筆數不增加 |
| hidden id 安全嗎？ | hidden 只是畫面不顯示，仍可被竄改 | 目前是識別用途，正式後端需驗證權限 |

| 階段 | 請求 | id 來源 | 結果 |
| --- | --- | --- | --- |
| 開啟修改頁 | GET /admin/manager/edit/3 | 網址 {id} | find → view，回填既有資料 |
| 儲存修改 | POST /admin/manager/update | hidden name="id" | find → 更新 → redirect |

## 18. 刪除：destroy 與 find 後 delete

| 我提出的問題／現象 | 精準理解 | 對照 |
| --- | --- | --- |
| {{ $data->id }} 讓資料庫找到那筆嗎？ | 先由 Blade 把目前那筆 id 放入網址，再由後端接收與刪除 | /admin/manager/delete/5 |
| destroy(id) 與 find(id)->delete() 都可以嗎？ | 是；destroy 也會在內部取出 Model 再刪除 | 不代表 destroy 完全不查詢資料 |
| find 後 delete 有何注意？ | find 查不到為 null，不能直接呼叫 delete() | 先判斷非空，或後續學 findOrFail |
| destroy 找不到 id 呢？ | 不會因對 null 呼叫方法而報同樣錯誤，沒有紀錄可刪 | 仍可能有其他資料庫錯誤，不能當成永不失敗 |
| data-delete 把列移除就是刪資料嗎？ | 本次模板只操作畫面 | 改用帶 id 的後端刪除連結，並確認資料庫 |
| 刪除需要 delete.blade.php 嗎？ | 本次不用，刪完直接回列表 | 方法名稱不代表一定有同名 View |
| 老師用 GET，正式專案也照做嗎？ | 目前沿用課堂；正式改用含 CSRF 配套的 POST／DELETE | 前端連結與路由要一起調整 |

```php
// 老師版本
Manager::destroy($req->id);

// 另一種理解方式：先找到，再刪除
$manager = Manager::find($req->id);
if (!empty($manager)) {
    $manager->delete();
}
```

## 19. 登出：Session 與 session()

| 我提出的問題／測試 | 精準理解 | 課堂對照 |
| --- | --- | --- |
| Session::forget 與 session()->forget 為何相同？ | 不同入口操作同一套 Session 功能 | 本例都移除 userId 鍵 |
| Session 是什麼？ | Facade 類別入口，用 :: 呼叫並交給背後服務 | 先 use Illuminate\Support\Facades\Session; |
| session() 是什麼？ | Laravel 輔助函式，無參數時取得 Session 管理物件 | session()->forget("userId")，不需引入 Facade |
| 差在大小寫嗎？ | 不是；要看類別與函式、:: 與 -> 的使用情境 | 不可只把 Session 改成小寫而省略括號 |
| forget 與 flush 相同嗎？ | forget 移除指定鍵，flush 清除全部 Session 資料 | 本次只移除登入用 userId |
| 直接前往 login.html 就登出了嗎？ | 沒有執行清除 Session 的後端流程 | JS 確認後先到 /admin/logout |
| 登出會刪除帳號嗎？ | 不會，刪除登入狀態與刪資料庫紀錄不同 | 資料庫帳號仍在，已測試 |
| 為什麼登出後直接開 home 也進不去？ | CheckManager 檢查不到登入 Session，因此轉回登入頁 | 不是單靠隱藏按鈕保護 |

時間順序：點擊連結 → JS 確認 → GET /admin/logout → Route → logout() 清除 Session → redirect /admin → 瀏覽器再次 GET /admin → index() 回傳登入 View。

## 20. MVC 與 redirect：最後一次回答的修正

| 原本的理解 | 修正後 |
| --- | --- |
| Route 引導網頁去指定網址 | Route 比對 HTTP 方法與網址路徑，選擇處理者；redirect 才要求瀏覽器前往另一網址 |
| Controller 執行 function | 還負責協調流程：取輸入、呼叫 Model、決定回傳 View 或轉址 |
| Model 查詢資料庫並回傳 | 本次還負責新增、修改、刪除；MVC Model 更廣義是資料與相關邏輯，不固定只能是資料庫 |
| View 產生顯示頁面 | 正確；使用 Controller 傳入的資料產生 HTML，不自行決定資料庫操作 |

修改功能至少包含：GET edit 顯示表單 → POST update 儲存 → redirect 後瀏覽器再 GET list。不是同一次請求直接執行三個頁面。

## 21. 追加三天完成紀錄與複習

| 天數 | 內容 | 結果 |
| --- | --- | --- |
| 第 1 天 | onblur、欄位取值、AJAX、帳號查重 | 成功 |
| 第 2 天 | 新增、模板攔截除錯、edit 回填、update | 成功，id 不變、筆數不增加 |
| 第 3 天 | 刪除、登出、Middleware、整合測試 | 全部通過 |

另外已確認：列表只顯示 2 筆，是先前 paginate 的每頁數量設定；已自行改成 paginate(10)。不要與 data-total 改寫總數的舊問題混為一談。

整合測試：新增練習帳號 → 查重提示 → 修改密碼 → 新密碼登入 → 換回管理員 → 刪除練習帳號 → 被刪帳號無法登入，全部成功。登出取消、確認、未登入直接開 home 與資料庫帳號保留也全部符合預期。

- [ ] 能說明 AJAX 檢查與表單儲存是兩次請求。
- [ ] 能分清 class、id、name、action、method。
- [ ] 看到成功提示，仍知道要確認資料庫與實際 HTTP 請求。
- [ ] 能說明 new／find 與 save／update／destroy 的差別。
- [ ] 能沿著連結 → 路由 → Controller → Model → 回應定位問題。
- [ ] 能說明登出移除 Session，刪除帳號才改變資料庫紀錄。

下一步是拿到小需求，先自行列出要改的檔案、請求方式、網址與方法，再動手寫；不必靠默背全部語法。
