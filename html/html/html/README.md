# 共用 CSS 版靜態網頁

原本 14 個網頁各自在 `<style>` 裡重複寫了一份 CSS（而且導覽列、頁尾樣式各頁不一致）。
這個資料夾是整理後的版本：

- **所有頁面只引用一個 `css/style.css`**，頁面內不再有 `<style>` 區塊
- 共用 JavaScript（手機選單、回頂端、密碼顯示 / 強度、購物車徽章）集中在 `js/main.js`
- 每頁都使用相同的 Topbar / Navbar / Footer
- 消息列表、產品列表加上關鍵字搜尋框

原始檔案仍保留在上一層資料夾，未做任何修改。
Laravel 專案（`../laravel_project01`）使用的是同一份 `style.css`。

## 後台管理系統（admin/）

`admin/` 資料夾是後台的靜態版，共 28 頁：

- `login.html` 後台登入（示範帳號 admin / admin1234）、`index.html` 儀表板
- 13 個功能各兩頁：`xxx.html` 列表、`xxx-form.html` 新增 / 修改表單（網址加 `?id=編號` 即為修改，會自動帶入資料）

共用檔案：

- CSS：與前台同一份 `../css/style.css`（後台樣式在第 12 節）
- JavaScript：`../js/main.js`（共用工具）＋ `../js/admin.js`（後台：關鍵字即時查詢並標示關鍵字、下拉篩選、SweetAlert2 刪除確認、表單必填檢查、修改時帶入資料、圖片 / 圖示預覽、登入登出）
- SweetAlert2 由 CDN 載入

靜態版的查詢、新增、修改、刪除都是前端示範，不會真的存檔；實際存進資料庫的版本在 `../laravel_project01`。
