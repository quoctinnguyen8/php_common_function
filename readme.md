# Các hàm hỗ trợ triển khai nhanh cho code PHP thuần

Cách dùng:
1. Copy file `common.php` và `config.php` vào project của các bạn
2. **Thay đổi thông tin ở file `config.php`** cho phù hợp với project hiện tại
3. Dùng lệnh `include("include/common.php")` vào file cần sử dụng các hàm hỗ trợ bên dưới. (Chú ý đường dẫn file)

## Cấu hình môi trường

Trong file `config.php`, có thể cấu hình biến `ENVIRONMENT`:
- **`development`**: Hiển thị chi tiết lỗi khi có exception (dùng cho môi trường phát triển)
- **`production`**: Ẩn chi tiết lỗi, chỉ hiển thị thông báo chung và tự động ghi log vào thư mục `logs/` với format file `yyyy_MM_dd.log`

```php
define('ENVIRONMENT', 'development'); // Hoặc 'production'
```

## Danh sách các function được hỗ trợ, hoạt động tốt nhất với cấu hình mặc định của XAMPP

### 1. `dd(...$data)`

Dump data ra màn hình *và dừng xử lý*. Tự động xóa toàn bộ output buffer trước khi hiển thị để đảm bảo nội dung không bị ẩn trong các thẻ HTML.

```php
dd($variable);                    // Dump 1 biến
dd($var1, $var2, $var3);         // Dump nhiều biến
dd($array, "Debug info", $object); // Dump các kiểu dữ liệu khác nhau
```

> Data có thể là bất cứ thứ gì: string, array, object, số...

### 2. `js_alert(string $text)`

Hiển thị alert của JS

### 3. `redirect_to(string $page)` và `js_redirect_to(string $page, bool $is_stop = true)`

Chuyển hướng đến trang trong hệ thống với 3 chế độ:
- **Đường dẫn tương đối**: Tự động thêm ROOT_PATH. VD: `redirect_to("admin/abc.php")` → `/php_common_function/admin/abc.php`
- **Đường dẫn tuyệt đối**: Bắt đầu bằng `~/` để bỏ qua ROOT_PATH. VD: `redirect_to("~/admin/abc.php")` → `/admin/abc.php`
- **Trang chủ**: Dùng `redirect_to("/")` để về trang chủ

```php
// Chuyển hướng tương đối (bao gồm ROOT_PATH)
redirect_to("admin/index.php");

// Chuyển hướng tuyệt đối (bỏ qua ROOT_PATH)
redirect_to("~/login.php");

// Về trang chủ
redirect_to("/");

// JavaScript redirect (có thể tắt auto-stop)
js_redirect_to("admin/index.php", false);
```

> Hàm `js_redirect_to()` sử dụng JavaScript để chuyển hướng, dùng khi muốn thông báo (`js_alert()`) trước khi chuyển hướng. Tham số `$is_stop` mặc định là `true` (dừng xử lý sau khi redirect)

### 4. Hàm hỗ trợ kiểm tra HttpMethod `is_post_method(): bool` và `is_get_method(): bool`

Như tên hàm, cho phép kiểm tra request là POST hay GET

```php
if (is_get_method()) {
	// Load data ...
}
```

### 5. `asset(?string $filename, bool $return = true): null|string`

Trả về URL đầy đủ (bao gồm protocol và host) của file CSS/JS/ảnh từ thư mục asset. URL bao gồm timestamp để cập nhật cache ngay lập tức khi file có thay đổi.

**Ví dụ output**: `http://localhost:8000/php_common_function/asset/css/style.css?1733270400`

> Thư mục asset có thể được cấu hình ở file `config.php`

```php
// CSS
<link rel="stylesheet" href="<?= asset("css/style.css"); ?>">

// In trực tiếp (không return)
<link rel="stylesheet" href="<?php asset("css/style.css", false); ?>">

// JS
<script src="<?= asset("js/script.js"); ?>"></script>
```

### 6. `upload(?string $filename, bool $return = true): null|string`

Trả về URL đầy đủ (bao gồm protocol và host) của file được upload bởi user. URL bao gồm timestamp để cập nhật cache ngay lập tức khi file có thay đổi.

**Ví dụ output**: `http://localhost:8000/php_common_function/upload/abc.jpg?1733270400`

> Thư mục upload có thể được cấu hình ở file `config.php`

```php
// Hiển thị ảnh được upload bởi user
<img src="<?= upload("abc.jpg"); ?>" />

// Tải file
<a href="<?= upload("document.pdf"); ?>">Download</a>
```

### 7. `upload_and_return_filename(string $name, string $sub_folder = ""): string|array`

Cho phép upload file của user lên web và nhận lại tên file đó. Trường hợp upload nhiều file thì return mảng các tên file.

> Thư mục upload có thể được cấu hình ở file `config.php`
> 
> Xem thêm ví dụ ở file `index.php`

```php
<?php
// Upload file tải lên vào thư mục files (trong thư mục upload)
$filename = upload_and_return_filename("my_file", "files");

// Các thao tác khác

```

### 8. `remove_file(string $filename): bool`

Xóa file từ thư mục upload, cần chỉ định tên file. Return `true` nếu xóa thành công, ngược lại là `false`

---
# Các hàm hỗ trợ thao tác với database

### 9. `db_select(string $sql, array $data = null): array`

Thực thi **câu lệnh select**, dữ liệu trả về luôn là associative array

Trường hợp không có dữ liệu sẽ return mảng rỗng

```php
/*
 * Query không có điều kiện
 */
$data = db_select("select * from tbl_student");

/*
 * Query có điều kiện
 */
$id = "MSV1604";				// mã sinh viên
$class_id = "DH19TIN01";		// mã lớp

// Dùng param cho câu sql để đảm bảo an toàn
$sql = "select * from tbl_student where id = ? and class_id = ?";

// Tạo mảng để đưa dữ liệu vào theo thứ tự, nhận kết quả select vào biến $data
$data = db_select($sql, [$id, $class_id]);

```

### 10. `db_execute(string $sql, array $data = null): bool`

Thực thi các câu lệnh **khác select** (insert, update, delete), return true nếu thực thi thành công.

```php
/*
 * Insert vào bảng tbl_student
 */
$id = "MSV1604";				// mã sinh viên
$class_id = "DH19TIN01";		// mã lớp
$name = "Nguyễn Văn A";			// tên sinh viên

// Dùng param cho câu sql để đảm bảo an toàn
$sql = "insert into tbl_student(id, class_id, name) values(?, ?, ?)";

// Tạo mảng để đưa dữ liệu vào theo thứ tự
db_execute($sql, [$id, $class_id, $name]);

```

### 11. `gen_option_ele($table, $col_value, $col_text, $selected_value)`

Tạo thẻ `option` với dữ liệu từ database

`$table`: tên bảng cần lấy dữ liệu

`$col_value`: tên cột sử dụng cho value, mặc định là `id`

`$col_text`: tên cột sử dụng để hiển thị, mặc định là `name`

`$selected_value`: giá trị ban đầu cho thẻ select, mặc định là rỗng

```php
<select name="class_id" id="class_id">
	<option value="">--Chọn một giá trị</option>
	<?php gen_option_ele("tbl_class", "class_id", "class_name"); ?>
</select>
```
