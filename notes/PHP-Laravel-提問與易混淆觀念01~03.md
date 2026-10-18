---
aliases:
  - Laravel 易混淆觀念
  - PHP 問題複習
---

# PHP／Laravel 提問與易混淆觀念整理

> 整理範圍：本次對話第 1～4 階段的提問與除錯紀錄，截至 2026-10-18。
> 以老師的命名與課堂流程為主；「容易混淆」是複習提醒，不代表目前仍不理解。
> 閱讀方式：先看粗體重點，再依問題查表。這份是觀念對照，不是完整程式碼。

## 先複習這 6 組差別

| 容易混在一起 | 要分開記住 |
| --- | --- |
| 網址／實體檔案路徑 | 瀏覽器請求網址；Laravel 再決定如何處理，不是直接找同名 Blade 檔 |
| 檔案／類別／物件／方法 | 檔案存程式，類別定義功能，new 建立物件，方法是可呼叫的功能 |
| 資料庫／資料表／Model | 資料庫放資料表；資料表存紀錄；Model 是操作資料的 PHP 類別 |
| 整份列表／單筆資料 | $list 是集合；foreach 的 $data 是目前這一筆 |
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
| app/Http/Controllers/Admin/ManagerController.php | 取得列表資料、回傳帳號管理畫面 | 與負責登入的 AdminController 分工不同 |
| app/Models/Admin/Manager.php | 定義資料表對應與資料操作 | 這是 PHP 檔案，不是資料庫檔案 |
| app/Http/Middleware/CheckManager.php | 進入受保護路由前，檢查 Session | Middleware 不是 Model，也不是資料表 |
| bootstrap/app.php | 啟動相關設定，例如路由檔、Middleware、例外處理 | 不是專門存放別名的檔案 |
| .env | 本機環境設定，例如資料庫連線與 Session 儲存方式 | 寫 DB_DATABASE 不會自動建立資料庫 |
| config/session.php | Session 功能的設定 | 設定儲存方式不等於已經登入 |
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
| href="/admin/logout" 就完成登出了嗎？ | 沒有；還要有接收路由與清除登入狀態的程式 | 目前只是連結，功能尚未完成 |
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
| $list 與 $data 有什麼不同？ | $list 是集合；$data 是迴圈這一輪取出的單筆 Model | foreach($list as $data) |
| 為何要特別取名 $data，不能只用 $list？ | 要有名稱指向目前單筆資料；不是把整份列表重新命名 | $list->userId 不是「逐筆取帳號」 |
| 一定要叫 $data 嗎？ | 不一定，可改 $manager 等名稱，但迴圈內要一致 | 名稱自取，角色固定 |
| 為什麼要把整個 tr 包在 foreach 裡？ | 每一輪產生完整一列，讓一筆資料對應一列 | 3 筆資料 → 3 個 tr |
| 為何原例只有 id、userId，沒有 pwd？ | 查回資料與畫面選擇顯示是兩件事 | 沒寫 $data->pwd 就不會輸出它 |
| 修改連結用 $list->id 還是 $data->id？ | 用目前這筆的 $data->id | /admin/manager/edit/2 |
| 新增帳號連結是第幾天？ | 第 5 階段：連結、create 路由、方法、表單頁 | 尚未包含 store 寫入功能 |

### 同一份資料的傳遞

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

## 10. 實際遇過的錯誤與排查位置

| 錯誤／現象 | 當時的重點 | 優先檢查 |
| --- | --- | --- |
| Driver [Session] not supported | Session 儲存方式讀到不支援的 Session 名稱 | .env 的 SESSION_DRIVER=file；config/session.php 的 driver；必要時 config:clear |
| Target class [manager] does not exist | 路由使用了尚未註冊的 Middleware 別名 | bootstrap/app.php 的 use、alias；類別是否已建立；不要誤改 Model |
| 419 PAGE EXPIRED | 通常是 CSRF 權杖與 Session 不符合；不代表必然同一原因 | 重新開登入頁再送出；@csrf；Session 設定；主機名稱保持一致 |
| 登入頁正常、layout 跑版 | 本機 CSS／JS 相對網址解析錯誤 | 使用 /css/style.css、/js/main.js、/js/admin.js |
| 點帳號管理出現 404 | 請求網址可能沒有對應路由 | href、list／lsit 拼字、prefix 是否重複、route:list |
| 登入失敗沒有回填帳號 | 暫存輸入與畫面回填必須配合 | withInput()、old('userId')、name 是否一致 |
| 登入成功但首頁沒顯示帳號 | 老師 HTML 不會自動插入 Session 值 | 在 Blade 加入 {{ session()->get("userId") }} |
| Collection::links does not exist | 使用 get() 的集合沒有分頁按鈕方法 | 下一階段使用 paginate() 後才呼叫 links() |
| 錯誤顯示 vendor/... | 框架拋出錯誤的位置，不一定是問題源頭 | 優先找自己的路由、設定、控制器、View，不修改 vendor |

### 常用檢查指令

| 指令 | 用途 |
| --- | --- |
| `php artisan route:list --path=admin` | 查看後臺相關路由是否存在 |
| `php artisan route:clear` | 清除路由快取 |
| `php artisan config:clear` | 清除設定快取 |
| `php artisan serve` | 啟動本機開發伺服器 |

> 查錯先看「錯誤名稱、請求網址、自己的程式位置」，不用先讀完全部 Stack Trace。分享訊息時不必附上 Cookie 或整份 .env。

## 11. 截至本次整理的學習進度

| 階段 | 完成內容 | 狀態 |
| --- | --- | --- |
| 第 1 階段 | 環境建置、啟動 Laravel | 已完成 |
| 第 2 階段 | 登入頁、路由、控制器、表單 | 已完成 |
| 第 3 階段 | 帳密查詢、失敗訊息、Session、轉址、Middleware | 已完成並測試 |
| 第 4 階段 | 共用版型、列表資料、選單高亮、老師版型整合 | 已完成並測試 |
| 第 5 階段 | 分頁、總筆數、新增帳號連結與表單 | 尚未開始 |

**目前尚未完成的功能：**實際新增寫入、修改、刪除、登出，以及正式專案需要的安全補強。畫面上有按鈕或連結，不代表功能已完成。

## 12. 下次複習的順序

- [ ] 說出「網址、模板名稱、實體檔案路徑」的差別。
- [ ] 說出「類別、物件、方法」各指程式哪一部分。
- [ ] 用自己的話讀懂 where("userId", $userId)。
- [ ] 分清單筆結果、null、集合與布林值。
- [ ] 說明 compact 如何把控制器的資料交給 View。
- [ ] 說明 foreach 每輪的 $data 與整份 $list 的差別。
- [ ] 分清 @include、@extends、@yield、@section。
- [ ] 能從錯誤訊息選出應先檢查的檔案。

> 不必背全部內容。每次挑一個問題，在自己的專案做一次小修改並預測結果，再用瀏覽器驗證。

