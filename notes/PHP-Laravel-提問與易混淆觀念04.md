---
aliases:
  - 大事記提問與易混淆觀念
  - Laravel 04 問答複習
---

# PHP／Laravel 提問與易混淆觀念 04

> 範圍：依 [[04.後臺-大事記]] 的四天學習，整理實際提問、回答與理解修正。
> 整理日期：2026-11-01。以老師原筆記為準，不納入額外工具、測試指令或筆記之外的關聯操作比較。
> 「容易混淆」是複習提醒，不代表目前仍不理解。本份是觀念整理，不取代完整實作筆記。

## 先看這次最重要的差別

| 容易混在一起 | 分開記住 |
| --- | --- |
| PHP 類別／大事記類別 | `MileStoneType` 是程式類別；「活動」是資料表裡的分類名稱 |
| 檔案／類別／資料表 | `MileStone.php` 存程式；`MileStone` 是 Model 類別；`milestone` 是資料表 |
| hasMany／belongsTo | 從類別找底下多筆事件／從事件找所屬類別 |
| 關聯／INNER JOIN | 關聯定義資料如何對應，不等於立即把兩張表 JOIN 起來 |
| label／title | 目前模板用 label 組 CSS class，用 title 顯示文字 |
| submit／action | 按鈕觸發送出／表單決定送往哪裡 |
| 暫存變數／固定欄位 | `$type` 可自取名；`label`、`title` 要對應表單與資料表 |
| flash／彈跳視窗 | Session 保存訊息；瀏覽器執行 Swal 顯示訊息 |

## 1. 第一天：Model、類別名稱與資料表

| 我提出的問題／回答 | 精準理解 | 課堂對照 |
| --- | --- | --- |
| 哪項設定決定 Model 操作哪張表？ | `$table` 指定對應表名，不建立資料表 | `protected $table = "milestone";` |
| `MileStone` 是 `MileStone.php` 裡的類別嗎？ | 是，指檔案內的 `class MileStone extends Model` | 檔名和類別名稱相關，但不是同一個概念 |
| `MileStone::class` 是資料表嗎？ | 不是，是取得 Model 的完整類別名稱字串 | `App\Models\Milestone\MileStone` |
| Laravel 怎麼知道該查哪張表？ | 先由指定的 Model 類別，再讀取它的 `$table` 設定 | MileStone → `$table = "milestone"` |

```php
// app/Models/Milestone/MileStone.php 內
class MileStone extends Model
{
    public $timestamps = false;
    protected $table = "milestone";
    protected $primaryKey = "id";
    protected $fillable = ["id", "typeId", "years", "months", "title", "content", "createTime"];
}
```

| 名稱 | 是什麼 |
| --- | --- |
| `MileStone.php` | PHP 檔案 |
| `MileStone` | 檔案中定義的 Model 類別 |
| `MileStone::class` | 類別的完整名稱字串，不是建立物件 |
| `milestone` | 實際資料表名稱 |

## 2. 第一天：hasMany、belongsTo 的方向與參數

### 2-1. 怎麼判斷用哪一個？

**先決定目前站在哪個 Model，再看要找的資料。**

| 站在哪一邊？ | 要問什麼？ | 老師使用的方法 |
| --- | --- | --- |
| `MileStoneType`：一個類別 | 這個類別底下有哪些大事記？ | `hasMany()` |
| `MileStone`：一筆大事記 | 這筆大事記屬於哪個類別？ | `belongsTo()` |

這是同一組一對多關係的兩個方向，不是兩套不同的欄位對應。

### 2-2. id 怎麼知道是哪張表？

由「目前的 Model」「指定的對方 Model」與「方法的參數位置」一起決定，不是靠欄位名稱猜。

```php
// 寫在 MileStoneType 裡：自己是類別，對方是大事記
return $this->hasMany(MileStone::class, "typeId", "id");
```

```php
// 寫在 MileStone 裡：自己是大事記，對方是類別
return $this->belongsTo(MileStoneType::class, "typeId", "id");
```

| 方法 | 第 1 個參數 | 第 2 個參數 `"typeId"` | 第 3 個參數 `"id"` |
| --- | --- | --- | --- |
| `hasMany()` | 對方 Model：MileStone | 對方 `milestone.typeId` | 自己 `milestone_type.id` |
| `belongsTo()` | 對方 Model：MileStoneType | 自己 `milestone.typeId` | 對方 `milestone_type.id` |

**參數順序沒有交換，指的實際欄位也沒變；變的是出發點，以及「自己／對方」的角色。**

```text
milestone.typeId = milestone_type.id
```

### 2-3. 適合自己的記法

你提出的 belongsTo 記法，修正成「指向」會更精準：

| 方法 | 記法 |
| --- | --- |
| `belongsTo()` | 自己的 typeId，指向對方的 id：我記錄自己屬於誰 |
| `hasMany()` | 對方有多筆資料，用 typeId 指向自己的 id：它們都記錄屬於我 |

例如「活動」類別的 id 是 2：

| 大事記 | typeId |
| --- | --- |
| 周年活動 | 2 |
| 公益活動 | 2 |
| 聖誕活動 | 2 |

站在「活動類別」看：找所有 typeId 等於我 id 的事件。站在「周年活動」看：用我自己的 typeId，找對應的類別 id。

### 2-4. hasMany 是 INNER JOIN 函式嗎？

**不是。老師筆記的 JOIN 比喻不能當成實際執行方式。**

| 比較 | 關聯方法 `hasMany()`／`belongsTo()` | SQL 的 INNER JOIN |
| --- | --- | --- |
| 負責什麼 | 定義如何取得相關資料 | 在查詢中連接兩張表 |
| 寫下定義時 | 不會因此立即查詢全部關聯資料 | 執行該查詢時才取得結果 |
| 結果理解 | 依定義取得相關 Model 資料 | 得到兩表欄位組合的查詢結果 |

兩者都用到欄位對應，但不是同一個操作；本階段先掌握老師兩個 Model 的關聯定義，不另外延伸查詢技巧。

### 2-5. 同一類別有三筆事件，要重複建類別嗎？

你的回答核心正確：**三筆大事記填入相同 typeId，不必建立三筆相同類別。**

補充修正：對應關係由資料表內的欄位值決定；`milestone()`、`types()` 讓 PHP 可以依該關係查詢。方法不會自動替你填入 typeId。

## 3. 第二天：名稱歧義、資料流與列表顯示

### 3-1. 「類別名稱」到底指什麼？

| 說法 | 本次指的內容 |
| --- | --- |
| PHP 的類別名稱 | 程式中的 `MileStoneType` |
| 大事記的類別名稱 | 資料中的「活動」「獲獎」等，存在 `milestone_type.title` |

問「活動如何顯示到畫面」時，指的是資料庫中的 title，不是在問 PHP 類別如何命名。

### 3-2. Route → Controller → Model → View

你的回答已掌握主要流程，下面是精準整理：

| 角色 | 課堂程式 | 做什麼 |
| --- | --- | --- |
| Route | 類別列表路由指定 `MileStoneTypeController::class`、`"list"` | 比對請求後交給指定方法 |
| Controller | `$list = MileStoneType::get();` | 透過 Model 查詢，接住資料集合 |
| Model | `$table = "milestone_type"` | 指定這次操作的資料表 |
| Controller | `return view("admin.milestone.types.list", compact("list"));` | 把資料傳給模板，回傳產生的畫面 |
| View | `@foreach($list as $data)` | 逐筆取出類別 |
| View | `{{ $data->title }}` | 輸出這一筆的「活動」等名稱 |

**不是取得資料後「下一行直接 return 資料」；這裡回傳的是使用資料產生的 View。**

```php
public function list(){
    $list = MileStoneType::get();
    return view("admin.milestone.types.list", compact("list"));
}
```

### 3-3. 為何標籤與標題一起變成「活動測試」？

老師模板兩處文字都使用 title：

```blade
<!-- 標籤欄 -->
<span class="badge badge-{{ $data->label }}">{{ $data->title }}</span>

<!-- 標題欄 -->
{{ $data->title }}
```

| 資料 | 目前用在哪裡？ | 改動效果 |
| --- | --- | --- |
| `label`，例如 gold | 組成 `badge-gold` 這個 CSS class | 若 CSS 有定義，改變標籤外觀 |
| `title`，例如活動 | 標籤內文字，以及標題欄文字 | 兩處文字一起改變 |

這是**同一份文字的兩種呈現**，不是兩個獨立的文字欄位；也不是資料庫查詢錯誤。本次已決定沿用老師寫法，不新增資料欄位。

## 4. 第三天：表單送出與變數命名

### 4-1. 儲存按鈕沒寫路徑，為什麼能送資料？

```html
<form action="/admin/milestone/types/store" method="post">
```

```html
<button type="submit">儲存</button>
```

| 設定 | 用途 |
| --- | --- |
| 按鈕 `type="submit"` | 觸發它所屬的表單送出 |
| 表單 `action` | 指定送往哪個網址 |
| 表單 `method="post"` | 指定使用 POST |
| 輸入框 `name` | 決定傳給後端的資料名稱 |
| `@csrf` | 產生表單請求驗證所需權杖 |

**按鈕不用另外寫網址，它使用表單的 action、method。**後端仍必須有對應路由與 store()，才會處理及儲存資料。

### 4-2. $types 是固定用法嗎？改成 $type 為什麼不行？

不是固定用法。兩者都可以；當時沒有提供改名後的完整方法，所以不能斷定是哪一行錯誤。最先檢查同一方法內是否一致：

```php
$type = new MileStoneType();
$type->label = $req->label;
$type->title = $req->title;
$type->save();
```

若第一行是 `$type`，後面仍寫 `$types`，就不是在使用剛才建立的變數。

| 名稱 | 能否自行更改？ | 注意事項 |
| --- | --- | --- |
| `$type`／`$types` | 可以 | 同一方法內一致 |
| `$req->label`／`$req->title` | 不能只改讀取名稱 | 本例對應表單 name |
| `$types->label`／`$types->title` | 不能只改屬性名稱 | 本例對應資料表欄位 |
| 網址中的 `/types/` | 可以設計，但需同步相關網址 | 不會因 PHP 變數改名而自動改變 |

如果是在 edit() 改變要傳給 View 的變數，還要同步 `compact()` 與模板變數；store() 裡沒有把這個物件直接傳給 View，所以不需要修改列表變數。

## 5. 第三天：Session::flash 與成功提示

### 5-1. 轉址後，列表為何仍能讀到 message？

你的回答正確：先透過 Session 暫存，再讓接續的列表請求讀取。

```php
Session::flash("message", "已新增");
return redirect("/admin/milestone/types/list");
```

| 部分 | 意思 |
| --- | --- |
| `message` | 自訂的 Session 鍵，不是資料表欄位 |
| `已新增` | 要顯示的訊息內容 |
| `flash()` | 保留資料供目前與下一次請求使用，之後清除 |
| `redirect()` | 要求瀏覽器另發請求前往列表 |

精準說法是**保留到下一次請求**，不只是「帶到下一個頁面」；也不是讀取一次就立刻刪除。

### 5-2. 暫存訊息與彈出視窗，分別由誰負責？

| 順序 | 程式／角色 | 做什麼 |
| --- | --- | --- |
| 1 | 瀏覽器表單 | POST 傳 label、title 與權杖 |
| 2 | store()／Model | save() 新增資料 |
| 3 | store() | flash() 記錄訊息，redirect() 回應轉址 |
| 4 | 瀏覽器 | 再 GET 請求類別列表 |
| 5 | list()／View | 查資料，檢查並讀取 message，產生頁面 |
| 6 | 瀏覽器 JavaScript | 執行 Swal.fire()，顯示提示 |

Session 不會自己彈視窗；它只保存訊息。資料庫是否新增成功，也不能只靠看到視窗判斷。

### 5-3. 為何重新整理後不再顯示？

flash 資料會依請求生命週期清除；本次 POST 之後的列表請求讀到訊息，再重新整理通常就沒有它了。你已確認新增提示與重新整理行為都正確。

## 6. 第四天：修改頁、單筆資料與方法責任

### 6-1. 為什麼修改用 find，而不是列表的 get？

你的回答正確：**修改要找指定 id 的那一筆，不是列出整張表的所有資料。**

| 寫法 | 本次用途 | 結果 |
| --- | --- | --- |
| `MileStoneType::get()` | 類別列表，沒有加篩選條件 | 所有類別的集合 |
| `MileStoneType::find($req->id)` | 開啟某筆的修改頁 | 一個 Model 物件；找不到為 null |

get() 也可以搭配篩選條件使用；不要把它記成「任何情況都只能查全部」。本次依老師原碼理解即可。

```php
public function edit(Request $req){
    $types = MileStoneType::find($req->id);
    return view("admin.milestone.types.edit", compact("types"));
}
```

| 容易混淆處 | 正確理解 |
| --- | --- |
| `$types` 有 s，所以是多筆？ | 不是，變數名稱不決定型別；這裡 find() 得到單筆 Model |
| 點修改就已更新資料？ | 沒有，edit() 只是查詢並回傳修改畫面 |
| 複製 create View 就完成修改？ | 沒有，原筆記尚未完成更新表單與後端 update |

### 6-2. 改成功提示，為什麼不用改 Model？

你的回答找到正確位置：修改 `MileStoneTypeController` 的 store()：

```php
Session::flash("message", "類別建立成功");
```

原因要記成：**這次只調整操作後的提示訊息，不需要改變資料處理邏輯。**

不是「Model 只負責查詢，所以不用改」。Model 也用於新增、修改、刪除；本次只是沒有要更動這些操作。

## 7. 四天範圍與完成狀態

| 天數 | 老師筆記內的學習內容 | 已確認 |
| --- | --- | --- |
| 第 1 天 | 兩張資料表、兩個 Model、hasMany／belongsTo 的方向與欄位 | 已建立並理解主要對應 |
| 第 2 天 | Route、Controller、類別查詢、列表與側欄 | 畫面可讀取資料庫類別 |
| 第 3 天 | create、store、表單送出、save、flash、SweetAlert | 新增成功，提示不重複出現 |
| 第 4 天 | edit 路由、find、傳入修改頁、回顧資料流 | 可載入指定類別；更新儲存待續 |

原筆記尚未完成的部分：類別更新儲存、類別刪除，以及大事記內容的完整 CRUD。不要把「看得到按鈕或表單」當成後端功能已完成。

## 複習重點

- [ ] 分清 PHP 類別名稱與資料中的分類名稱。
- [ ] 能從目前 Model 判斷 hasMany／belongsTo 的方向。
- [ ] 知道兩個關聯裡的 typeId、id 指向哪些資料表。
- [ ] 能說出「活動」如何從資料庫經 Controller、View 顯示出來。
- [ ] 知道 label 控制樣式、title 控制這個模板的顯示文字。
- [ ] 分清 submit、action、method 與 name。
- [ ] 改變數名稱時，知道哪些位置需要同步。
- [ ] 能說明 flash 為何能跨轉址保留提示，及為何重新整理不再顯示。
- [ ] 分清列表 get()、修改 find()，以及 edit 與真正更新的差別。

> 後續複習與考核以老師筆記及已教的內容為範圍；額外補充不列入考題。
