# Các hàm hỗ trợ triển khai nhanh cho code PHP thuần

Cách dùng:
1. Copy file `common.php` và `config.php` vào project của các bạn
2. **Thay đổi thông tin ở file `config.php`** cho phù hợp với project hiện tại
3. Dùng lệnh `include("include/common.php")` vào file cần sử dụng các hàm hỗ trợ bên dưới. (Chú ý đường dẫn file)

## Danh sách các function được hỗ trợ, hoạt động tốt nhất với cấu hình mặc định của XAMPP

### 1. `dd($data1, $data2, ...)`

Dump data ra màn hình *và dừng xử lý*
> data có thể là bất cứ thứ gì, có thể dump bao nhiêu biến tùy thích

### 2. `js_alert(string $text)`

Hiển thị alert của JS

### 3. `redirect_to(string $page)`

Chuyển hướng đến trang trong hệ thống, không bao gồm thư mục gốc.
Ví dụ: nếu website đặt ở `htdocs/test` và bạn muốn chuyển hướng đến `test/admin/abc.php` thì chỉ cần viết
`redirect_to("admin/abc.php")`
> Lưu ý rằng hàm này chỉ cho phép chuyển hướng dựa trên đường dẫn tuyệt đối, chưa hỗ trợ chuyển hướng bằng đường dẫn tương đối

### 4. Hàm hỗ trợ kiểm tra HttpMethod `is_method_post(): bool` và `is_method_get(): bool`

Như tên hàm, cho phép kiểm tra request là POST hay GET

### 5. `asset(string $filename)`

Cho phép lấy đường dẫn file CSS/JS/ảnh từ thư mục asset và in ra trình duyệt, đường dẫn bao gồm param để cập nhật ngay lập tức khi file có thay đổi

> Thư mục asset có thể được cấu hình ở file `config.php`

```php
// CSS
<link rel="stylesheet" href="<?php asset("css/style.css"); ?>">

// JS
<script src="<?php asset("js/script.js"); ?>"></script>
```

### 6. `upload(string $filename)`

Tương tự như hàm `asset` nhưng dùng cho file upload bởi user (file upload được đặt ở thư mục riêng)
> Thư mục upload cũng có thể được cấu hình ở file `config.php`

```php
// Hiển thị ảnh được upload bởi user
<img src="<?php upload("abc.jpg"); ?>" />
```

### 7. `upload_and_return_filename(string $name, string $sub_folder = ""): string|array`

Cho phép upload file của user lên web và return tên file đó. Trường hợp upload nhiều file thì return mảng các tên file.

> Thư mục upload cũng có thể được cấu hình ở file `config.php`
> 
> Xem thêm ví dụ ở file `index.php`

```php
<?php
// Upload file tải lên vào thư mục files (trong thư mục upload)
upload_and_return_filename("my_file", "files");
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

### 11. `function gen_option_ele($table, $col_value, $col_text, $selected_value)`

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
