# PHP／Laravel 提問與易混淆觀念 06

> 更新：2026-11-18。
> 範圍：[[06.產品]]。整理前一輪第 4 天保留的產品問答，以及這次的後臺分類列表與圖片、規格關聯練習。

## 1. 從網址追到畫面

| 問答 | 釐清 |
| --- | --- |
| `product.php` 有路由，但 `web.php` 漏了 `include`，會執行 `list()` 嗎？ | 不會。沒有其他路由接住時會得到 404。路由檔必須先被載入，Laravel 才知道這個網址要執行哪個方法；回傳哪個 Blade 是 Controller 決定的。 |
| 側欄寫對了，登出後直接貼網址能看到列表嗎？ | 不會。路由群組的 `manager` Middleware 先檢查登入；未登入會導回 `/admin`，不會執行 `list()`。側欄連結和高亮都不負責驗證權限。 |
| 列表正常，是否代表新增、修改、刪除都完成？ | 不代表。分類頁只執行 `ProductType::get()` 並顯示結果。按鈕、路由、對應的 Controller 方法都接好，該操作才有實作可以驗收。 |

## 2. 改共用名稱，還是改某一筆產品？

| 問答 | 釐清 |
| --- | --- |
| 兩筆產品的 `type_id` 都是 3，改類別 3 的 `title` 會怎樣？ | 兩筆產品看到的類別名稱都會跟著變，`type_id` 仍是 3。要還原名稱，只改回那筆類別的 `title`。 |
| 只想讓一個產品從 NEW 換成 HOT，要改哪裡？ | 改這筆產品的 `label_id`，改成 HOT 的編號。如果把標籤表的 NEW 改名，所有引用那筆標籤的產品都會受影響。 |

## 3. 圖片編號與產品編號

| 問答 | 釐清 |
| --- | --- |
| 替第 7 號產品加圖片，要在哪張表新增？ | 在 `product_photo` 新增一筆，`productId` 填 7。圖片自己的 `id` 辨識的是這一張圖。 |
| 產品有多張圖片，只想換其中一張，用 `productId` 或 `createTime` 可以嗎？ | `productId` 會找到同一產品的多張圖；建立時間可能重複，可輔助辨認。確認圖片後，用圖片自己的 `id` 鎖定那筆，再更新 `photo`。如果仍屬於同一產品，`productId` 保留。 |
| `hasMany()` 把第二個參數 `productId` 錯寫成 `id`，會比對哪裡？ | 第三個參數是產品表的 `id`，提供值 7；第二個參數才決定去圖片表查哪個欄位。正確查 `product_photo.productId = 7`；寫錯會查 `product_photo.id = 7`。 |
| 某張圖片 `id = 7`、`productId = 3`，寫錯後會怎樣？ | 資料庫裡它仍屬於第 3 號產品，但錯誤的關聯可能把它查成第 7 號產品的圖片。查錯不等於資料歸屬被改掉。 |
| 第 7 號產品應該有圖片，但 `$product->photos` 是空集合，先查哪裡？ | 先在 `product_photo` 查 `productId = 7`。若資料存在，再核對 `photos()` 的第二個參數是否為 `productId`。規格同理，改查 `product_spec`。 |

值得練熟的是兩條查找路徑：**頁面不通時，從網址追到路由、Middleware、Controller、Model 和 Blade；關聯查錯時，先確認手上的 ID 代表誰，再確認拿它去比對哪張表的哪個欄位。**


## 4. 分類、標籤 CRUD：這輪問答

| 問題或回答中的混淆 | 正確理解 |
| --- | --- |
| 輸入框改成 `name="category"`，Controller 還讀 `$req->title`？ | 接不到那個值作為 `title`。可以把 name 改回 title，或改後端讀 category；兩邊要一致，Model 寫入欄位仍是 title。 |
| update 裡把 find 改成 new，只是建立新物件嗎？ | 是，但後面仍用 `update()` 時，不會更新原資料，也不會因此自動新增。`new` 配 `save()` 才會嘗試新增。 |
| 刪分類前要查產品哪個欄位？ | 查 `product.type_id` 是否仍有該分類編號。產品不會自動轉到其他類別。 |
| `$label->label = $req->label` 的兩邊？ | 左邊是 Model 的 label 欄位；右邊讀表單 name 為 label 的值。`save()` 或 `update()` 才寫入資料庫。 |
| 修改頁錯用 `$label->title`，輸入框空白代表資料被清空嗎？ | 不代表。只是沒有正確帶入值，載入修改頁還沒更新資料。應改成 `$label->label`。 |
| doDelete 的 id 決定用哪個 Controller 嗎？ | 不是。第三個參數 url 經路由決定 Controller；id 指定該處理方法要找哪筆。分類與標籤可能都有 id=7。 |
| 標籤按鈕誤傳分類刪除網址？ | 會交給分類 Controller，嘗試刪除同編號的分類。必須核對網址與編號兩者。 |

## 5. get、with 與分頁

| 寫法 | 接收的名稱 | 結果 |
| --- | --- | --- |
| `Product::get(["product_no", "product_name"])` | product 表的欄位名 | 取產品編號與名稱 |
| `Product::with(["types", "labels", "photos"])->get()` | Product 裡的關聯方法名 | 取產品，預載分類、標籤、圖片 |
| `Product::with(["types", "labels", "photos"])->orderByDesc("id")->paginate(10)` | 關聯名、排序欄位與每頁筆數 | 產品 id 由大到小，每頁最多 10 筆 |

「為什麼不用 `Product::get(["types", "labels", "photos"])`？」因為這三個是關聯名稱，不是 product 表的欄位。with 預載關聯資料；get 或 paginate 才執行主查詢。老師最後使用 paginate，就不再接 get。

## 6. firstPhoto() 已取第一張，為什麼還要 ->photo？

假設目前產品 id=7，第一筆圖片資料如下：

| 圖片 id | productId | photo |
| --- | --- | --- |
| 20 | 7 | ball.jpg |

| 程式 | 取得什麼 |
| --- | --- |
| `$data` | 目前產品的 Model 物件 |
| `$data->firstPhoto()` | 圖片 20 的整筆 Model 物件 |
| `$data->firstPhoto()->photo` | 物件的 photo 欄位值：ball.jpg |
| `/product/S/ball.jpg` | 組成瀏覽器要載入的圖片網址 |

沒有圖片時，firstPhoto() 回傳 null；**取得 null 本身不是錯誤，接著讀取 ->photo 才會報錯**。所以模板先用 @if 判斷；無圖時改顯示預設圖示。

本輪已驗收分類／標籤 CRUD，以及產品列表的名稱、分類、標籤和有圖／無圖。產品列表尚未接好的項目與產品本身 CRUD 留待老師續課。
