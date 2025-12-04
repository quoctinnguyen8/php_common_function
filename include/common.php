<?php

include("config.php");

/**
 * Hiển thị hộp thoại alert bằng JavaScript
 * 
 * @param string $text Nội dung cần hiển thị trong alert
 * @return void
 */
function js_alert(string $text)
{
	echo "<script>alert('" . addslashes($text) . "');</script>";
}

/**
 * Kiểm tra xem request hiện tại có phải là POST method hay không
 * 
 * @return bool True nếu là POST, false nếu không
 */
function is_post_method(): bool
{
	return $_SERVER['REQUEST_METHOD'] == 'POST';
}

/**
 * Kiểm tra xem request hiện tại có phải là GET method hay không
 * 
 * @return bool True nếu là GET, false nếu không
 */
function is_get_method(): bool
{
	return $_SERVER['REQUEST_METHOD'] == 'GET';
}

/**
 * Chuyển hướng đến trang khác trong hệ thống bằng HTTP header
 * 
 * Hỗ trợ 3 loại đường dẫn:
 * - Tương đối: Tự động thêm ROOT_PATH. VD: "admin/index.php" -> "/php_common_function/admin/index.php"
 * - Tuyệt đối: Bắt đầu bằng "~/" để bỏ qua ROOT_PATH. VD: "~/login.php" -> "/login.php"
 * - Trang chủ: Dùng "/" để về trang chủ (bao gồm ROOT_PATH nếu có)
 * 
 * @param string $page Đường dẫn đến trang cần chuyển hướng
 * @return void Script sẽ dừng sau khi gọi hàm này
 */
function redirect_to(string $page)
{
	// Nếu là "/" thì redirect về trang chủ (bao gồm ROOT_PATH)
	if ($page === '/') {
		$home_path = ROOT_PATH ? ROOT_PATH : '/';
		header("Location: $home_path");
		exit();
	}
	// Nếu bắt đầu bằng ~/ thì là đường dẫn tuyệt đối (absolute path)
	if (strpos($page, '~/') === 0) {
		$page = substr($page, 2); // Bỏ ký tự ~/
		$page = '/' . ltrim($page, '/');
		header("Location: $page");
		exit();
	}
	// Ngược lại là đường dẫn tương đối (relative path)
	$page = ltrim($page, '/');
	$full_path = ROOT_PATH . '/' . $page;
	$full_path = str_replace('//', '/', $full_path);
	$full_path = '/' . ltrim($full_path, '/');
	header("Location: $full_path");
	exit();
}

/**
 * Chuyển hướng đến trang khác trong hệ thống bằng JavaScript
 * 
 * Hỗ trợ 3 loại đường dẫn tương tự redirect_to()
 * Dùng khi cần thông báo (js_alert) trước khi chuyển hướng
 * 
 * @param string $page Đường dẫn đến trang cần chuyển hướng
 * @param bool $is_stop True (mặc định) để dừng script sau khi redirect, false để tiếp tục
 * @return void
 */
function js_redirect_to(string $page, bool $is_stop = true)
{
	// Nếu là "/" thì redirect về trang chủ (bao gồm ROOT_PATH)
	if ($page === '/') {
		$home_path = ROOT_PATH ? ROOT_PATH : '/';
		echo "<script>location.href = '$home_path';</script>";
		if ($is_stop) {
			die;
		}
		return;
	}
	// Nếu bắt đầu bằng ~/ thì là đường dẫn tuyệt đối (absolute path)
	if (strpos($page, '~/') === 0) {
		$page = substr($page, 2); // Bỏ ký tự ~/
		$page = '/' . ltrim($page, '/');
		echo "<script>location.href = '$page';</script>";
		if ($is_stop) {
			die;
		}
		return;
	}
	// Ngược lại là đường dẫn tương đối (relative path)
	$page = ltrim($page, '/');
	$full_path = ROOT_PATH . '/' . $page;
	$full_path = str_replace('//', '/', $full_path);
	$full_path = '/' . ltrim($full_path, '/');
	echo "<script>location.href = '$full_path';</script>";
	if ($is_stop) {
		die;
	}
}

/**
 * Trả về URL đầy đủ (protocol + host + path) của file trong thư mục asset
 * 
 * URL bao gồm timestamp (file modification time) để tự động cập nhật cache khi file thay đổi
 * VD output: http://localhost:8000/php_common_function/asset/css/style.css?1733270400
 * 
 * @param string|null $filename Tên file hoặc đường dẫn tương đối trong thư mục asset
 * @param bool $return True (mặc định) để return giá trị, false để in trực tiếp
 * @return string|null URL đầy đủ của file, hoặc null nếu $return = false
 */
function asset(?string $filename, bool $return = true)
{
	if (is_null($filename)) $filename = "";
	$path = trim($filename, "/");
	$full_path = ASSET_PATH . "/" . $path;

	// Tạo URL đầy đủ với protocol và host
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
	$host = $_SERVER['HTTP_HOST'];
	$full_url = $protocol . "://" . $host . $full_path;

	if (file_exists(DOCUMENT_ROOT_PATH . $full_path)) {
		// Xóa cache khi cập nhật file asset bằng cách thêm param lần ghi cuối cùng
		$full_url .= "?" . filemtime(DOCUMENT_ROOT_PATH . $full_path);
	}
	if ($return) return $full_url;
	echo $full_url;
}

/**
 * Trả về URL đầy đủ (protocol + host + path) của file trong thư mục upload
 * 
 * URL bao gồm timestamp (file modification time) để tự động cập nhật cache khi file thay đổi
 * VD output: http://localhost:8000/php_common_function/upload/image.jpg?1733270400
 * 
 * @param string|null $filename Tên file hoặc đường dẫn tương đối trong thư mục upload
 * @param bool $return True (mặc định) để return giá trị, false để in trực tiếp
 * @return string|null URL đầy đủ của file, hoặc null nếu $return = false
 */
function upload(?string $filename, bool $return = true)
{
	if (is_null($filename)) $filename = "";
	$path = trim($filename, "/");
	$full_path = UPLOAD_PATH . "/" . $path;

	// Tạo URL đầy đủ với protocol và host
	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
	$host = $_SERVER['HTTP_HOST'];
	$full_url = $protocol . "://" . $host . $full_path;

	if (file_exists(DOCUMENT_ROOT_PATH . $full_path)) {
		// Xóa cache khi cập nhật file asset bằng cách thêm param lần ghi cuối cùng
		$full_url .= "?" . filemtime(DOCUMENT_ROOT_PATH . $full_path);
	}
	if ($return) return $full_url;
	echo $full_url;
}

/**
 * Tải file lên server và lưu vào thư mục upload, trả về tên file đã lưu
 * 
 * Tên file sẽ được thêm timestamp để tránh trùng lặp
 * Hỗ trợ upload đơn và nhiều file cùng lúc
 * 
 * @param string $name Tên của input field trong form ($_FILES[$name])
 * @param string $sub_folder Thư mục con trong upload để lưu file (tự động tạo nếu chưa tồn tại)
 * @return string|array|null Tên file (string) nếu upload 1 file, mảng tên file nếu upload nhiều, null nếu thất bại
 */
function upload_and_return_filename(string $name, string $sub_folder = "")
{
	if (is_post_method() && isset($_FILES[$name])) {
		// Xóa dấu slash (gạch chéo) khỏi folder để tiện xử lý sau này
		$sub_folder = trim($sub_folder, "/\\");
		$upload_path = DOCUMENT_ROOT_PATH . UPLOAD_PATH;
		if (!empty($sub_folder)) {
			$upload_path = trim($upload_path, "/\\") . "/" . $sub_folder;
		}
		// Tạo thư mục nếu chưa có
		if (!file_exists($upload_path)) {
			mkdir($upload_path, 0777, true);
		}

		$filenames = $_FILES[$name]["name"];
		$tmp_file = $_FILES[$name]["tmp_name"];
		$file_size = $_FILES[$name]["size"];

		if ($file_size <= 0) return null;

		if (is_array($filenames)) {
			// Upload nhiều file
			$all_filenames = [];
			foreach ($filenames as $i => $fname) {
				if (!empty($fname)) {
					$path_info = pathinfo($fname);
					// Thêm thời gian vào tên file để tránh bị trùng
					$filename = $path_info['filename'] . '-' . time() . "." . $path_info["extension"];
					if ($file_size[$i] > 0) {
						move_uploaded_file($tmp_file[$i], $upload_path . "/" . $filename);
						if (!empty($sub_folder)) {
							$filename = $sub_folder . "/" . $filename;
						}
					} else {
						$filename = "";
					}
					$all_filenames[] = $filename;
				}
			}
			return $all_filenames;
		} else {
			// upload 1 file
			$path_info = pathinfo($filenames);
			// Thêm thời gian vào tên file để tránh bị trùng
			$filename = $path_info['filename'] . '-' . time() . "." . $path_info["extension"];

			if ($file_size > 0) {
				move_uploaded_file($tmp_file, $upload_path . "/" . $filename);
				if (!empty($sub_folder)) {
					$filename = $sub_folder . "/" . $filename;
				}
				return $filename;
			}
		}
	}
	return null;
}

/**
 * Xóa file trong thư mục upload
 * 
 * @param string $filename Tên file hoặc đường dẫn tương đối trong thư mục upload
 * @return bool True nếu xóa thành công, false nếu file không tồn tại hoặc xóa thất bại
 */
function remove_file(string $filename): bool
{
	$path = trim($filename, "/");
	$full_path = DOCUMENT_ROOT_PATH . UPLOAD_PATH . "/" . $path;
	if (file_exists($full_path)) {
		return unlink($full_path);
	}
	return false;
}

/**
 * Tạo chuỗi type specifier cho prepared statement
 * 
 * Tất cả tham số được coi là string ("s")
 * 
 * @param array|null $data Mảng dữ liệu cần bind vào query
 * @return string Chuỗi type specifier (VD: "sss" cho 3 tham số)
 */
function get_sql_prepared(array $data = null)
{
	if (empty($data)) return "";
	$len = count($data);
	if ($len > 0) {
		return str_pad("", $len, "s");
	}
	return "";
}

/**
 * Ghi log lỗi vào file với format yyyy_MM_dd.log
 * 
 * @param string $message Nội dung lỗi cần ghi log
 * @param string $sql Câu lệnh SQL (nếu có)
 * @param array|null $data Dữ liệu parameters (nếu có)
 * @return void
 */
function log_error(string $message, string $sql = "", $data = null)
{
	// Tạo thư mục logs nếu chưa tồn tại
	$log_dir = DOCUMENT_ROOT_PATH . LOG_PATH;
	if (!file_exists($log_dir)) {
		mkdir($log_dir, 0777, true);
	}
	
	// Tên file theo format yyyy_MM_dd.log
	$log_file = $log_dir . "/" . date('Y_m_d') . ".log";
	
	// Nội dung log
	$log_content = "[" . date('Y-m-d H:i:s') . "] ERROR: " . $message . "\n";
	if (!empty($sql)) {
		$log_content .= "SQL: " . $sql . "\n";
	}
	if (!empty($data)) {
		$log_content .= "Data: " . print_r($data, true) . "\n";
	}
	$log_content .= "---\n";
	
	// Ghi vào file
	file_put_contents($log_file, $log_content, FILE_APPEND);
}

/**
 * Hiển thị lỗi dựa trên môi trường (development/production)
 * 
 * @param Exception $ex Exception object
 * @param string $sql Câu lệnh SQL (nếu có)
 * @param array|null $data Dữ liệu parameters (nếu có)
 * @param int $status_code Mã lỗi HTTP
 * @return void
 */
function handle_error(Exception $ex, string $sql = "", $data = null, int $status_code = 500)
{
	if (ENVIRONMENT === 'production') {
		// Môi trường production: log lỗi và hiển thị thông báo chung
		log_error($ex->getMessage() . "\n" . $ex->getTraceAsString(), $sql, $data);
		dd("Đã xảy ra lỗi khi thực hiện yêu cầu này ($status_code)");
	} else {
		// Môi trường development: hiển thị chi tiết lỗi
		dd($ex->getMessage(), "Query: $sql", $ex->getTraceAsString(), $data);
	}
}

/**
 * Tạo kết nối MySQL từ thông tin trong config.php
 * 
 * Tự động bật error reporting và set charset UTF8
 * 
 * @return mysqli Kết nối database
 */
function get_mysqli()
{
	global $database;
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$conn = new mysqli($database["host"], $database["username"], $database["password"], $database["db"]);
	mysqli_set_charset($conn, 'UTF8');
	return $conn;
}

/**
 * Thực thi prepared statement và trả về connection và query object
 * 
 * Hàm nội bộ được sử dụng bởi db_select() và db_execute()
 * 
 * @param string $sql Câu lệnh SQL với prepared statement (dùng ? cho placeholder)
 * @param array|null $data Mảng tham số để bind vào query
 * @return array Mảng chứa ['conn' => mysqli, 'query' => mysqli_stmt]
 */
function execute_query($sql, $data)
{
	$conn = get_mysqli();
	$type = get_sql_prepared($data);
	$query = $conn->prepare($sql);
	if (!empty($data)) {
		foreach ($data as $i => $x) {
			$data[$i] = $x;
		}
		$query->bind_param($type, ...$data);
	}
	$query->execute();
	return ['conn' => $conn, 'query' => $query];
}

/**
 * Thực thi câu lệnh SELECT và trả về kết quả dưới dạng mảng associative
 * 
 * Sử dụng prepared statement để bảo mật (phòng chống SQL injection)
 * 
 * @param string $sql Câu lệnh SELECT với prepared statement (dùng ? cho placeholder)
 * @param array|null $data Mảng tham số để bind vào query theo thứ tự
 * @return array Mảng kết quả (associative array), mảng rỗng nếu không có dữ liệu
 */
function db_select(string $sql, array $data = null): array
{
	try {
		$result = execute_query($sql, $data);
		$conn = $result['conn'];
		$query = $result['query'];
		$query_result = $query->get_result();
		$arr_result = [];
		if ($query_result->num_rows > 0) {
			while ($record = $query_result->fetch_assoc()) {
				array_push($arr_result, $record);	// hoặc $arr_result[] = $record;
			}
		}
		$conn->close();
		return $arr_result;
	} catch (Exception $ex) {
		handle_error($ex, $sql, $data, 500);
		return [];
	}
}

/**
 * Thực thi các câu lệnh INSERT, UPDATE, DELETE
 * 
 * Sử dụng prepared statement để bảo mật (phòng chống SQL injection)
 * 
 * @param string $sql Câu lệnh SQL với prepared statement (dùng ? cho placeholder)
 * @param array|null $data Mảng tham số để bind vào query theo thứ tự
 * @param int|null $insert_id Biến reference để nhận ID của record vừa insert (nếu có)
 * @return bool True nếu có ít nhất 1 row bị ảnh hưởng, false nếu không
 */
function db_execute(string $sql, array $data = null, &$insert_id = null): bool
{
	try {
		$result = execute_query($sql, $data);
		$conn = $result['conn'];
		$query = $result['query'];
		$affected = $query->affected_rows;
		$insert_id = $conn->insert_id;
		$conn->close();
		return $affected > 0;
	} catch (Exception $ex) {
		handle_error($ex, $sql, $data, 500);
		return false;
	}
}

/**
 * Tạo các thẻ <option> cho thẻ <select> từ dữ liệu database
 * 
 * Tự động thêm thuộc tính 'selected' cho option khớp với $selected_value
 * 
 * @param string $table Tên bảng trong database
 * @param string $col_value Tên cột dùng cho giá trị (value attribute), mặc định "id"
 * @param string $col_text Tên cột dùng cho text hiển thị, mặc định "name"
 * @param mixed $selected_value Giá trị được chọn mặc định (option sẽ có thuộc tính selected)
 * @return void In trực tiếp các thẻ option ra màn hình
 */
function gen_option_ele($table, $col_value = "`id`", $col_text = "`name`", $selected_value = "")
{
	$sql = "select $col_value, $col_text from $table order by $col_value desc";
	$data = db_select($sql);

	$tmp = explode(" as ", $col_value);
	$col_value = end($tmp);
	$col_value = str_replace("`", "", $col_value);
	$tmp = explode(" as ", $col_text);
	$col_text = end($tmp);
	$col_text = str_replace("`", "", $col_text);
	$str = "";
	foreach ($data as $item) {
		$text = htmlentities($item[$col_text]);
		if ($item[$col_value] == $selected_value) {
			$str .= "<option value='$item[$col_value]' selected>$text</option>";
		} else {
			$str .= "<option value='$item[$col_value]'>$text</option>";
		}
	}
	echo $str;
}

/**
 * Dump dữ liệu ra màn hình và dừng thực thi script (Die and Dump)
 * 
 * Tự động xóa toàn bộ output buffer trước khi hiển thị để đảm bảo
 * nội dung debug không bị ẩn trong các thẻ HTML
 * 
 * @param mixed ...$data Một hoặc nhiều biến cần dump (có thể là bất kỳ kiểu dữ liệu nào)
 * @return void Script sẽ dừng ngay sau khi hiển thị
 */
function dd(...$data)
{
	// Xóa hết nội dung output buffer để đảm bảo nội dung hiển thị không bị ẩn
	while (ob_get_level()) {
		ob_end_clean();
	}
	$textArr = ["Dump data..."];
	foreach ($data as $x) {
		$textArr[] = print_r($x, true);
	}
	$text = implode("\n\n", $textArr);
	echo "<pre style='background-color: #efefef; padding: 15px; box-sizing: border-box'>$text</pre>";
	die;
}
