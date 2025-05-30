# SkillSwap 安裝說明

## 環境需求
- XAMPP (包含 Apache + MySQL + PHP)
- PHP 7.4 或以上版本
- MySQL 5.7 或以上版本

## 安裝步驟

### 1. 準備環境
1. 下載並安裝 XAMPP：https://www.apachefriends.org/
2. 啟動 XAMPP Control Panel
3. 啟動 Apache 和 MySQL 服務

### 2. 部署專案
1. 將 `termproject` 資料夾複製到 XAMPP 的 `htdocs` 目錄下
   - Windows: `C:\xampp\htdocs\`
   - macOS: `/Applications/XAMPP/xamppfiles/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

### 3. 建立資料庫
1. 開啟瀏覽器，前往 http://localhost/phpmyadmin/
2. 建立新資料庫，名稱為 `skillswap`
3. 選擇剛建立的資料庫
4. 點選「匯入」頁籤
5. 選擇 `sql/schema.sql` 檔案並執行
6. （可選）匯入 `sql/sample_data.sql` 來新增示範資料

### 4. 設定資料庫連線
編輯 `src/db.php` 檔案，確認資料庫連線設定：
```php
$host = 'localhost';
$dbname = 'skillswap';
$username = 'root';
$password = '';  // XAMPP 預設密碼為空
```

### 5. 存取網站
開啟瀏覽器，前往：http://localhost/termproject/public/

## 示範帳號（如果有匯入示範資料）
- Email: alice@example.com | 密碼: password123
- Email: bob@example.com | 密碼: password123
- Email: carol@example.com | 密碼: password123

## 功能說明
- **會員註冊/登入**：新用戶註冊後可獲得 100 點起始點數
- **技能服務**：發布、瀏覽、編輯、刪除技能服務
- **預約系統**：預約他人服務、管理預約狀態
- **點數管理**：查看點數餘額、轉讓點數、交易紀錄
- **留言討論**：在服務頁面留言互動

## 疑難排解

### 資料庫連線錯誤
確認：
1. MySQL 服務是否啟動
2. 資料庫名稱是否正確
3. 帳號密碼是否正確

### 頁面顯示錯誤
1. 檢查 PHP 錯誤訊息
2. 確認所有檔案都已正確上傳
3. 檢查檔案權限

### 無法註冊/登入
1. 確認資料表是否正確建立
2. 檢查表單資料是否完整
3. 查看瀏覽器開發者工具的錯誤訊息
