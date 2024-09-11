<?php

include("config.php");

/*
 * Hiển thị alert
 */
function js_alert(string $text)
{
	echo "<script>alert('" . addslashes($text) . "');</script>";
}

function is_post_method(): bool
{
	return $_SERVER['REQUEST_METHOD'] == 'POST';
}
function is_get_method(): bool
{
	return $_SERVER['REQUEST_METHOD'] == 'GET';
}

/*
 * Chuyển hướng đến trang khác trong hệ thống
 */
function redirect_to(string $page)
{
	$page = ltrim($page, "\\/ ");
	if (!empty($page)) {
		header("Location: /$page");
		exit();
	}
}

/*
 * Chuyển hướng đến trang khác trong hệ thống (dùng JS)
 */
function js_redirect_to(string $page, bool $is_stop = true)
{
	$page = ltrim($page, "\\/ ");
	if (!empty($page)) {
		echo "<script>location.href = '/$page';</script>";
	}
	if ($is_stop) {
		die;
	}
}

/*
 * In ra đường dẫn đến file ở thư mục asset
 */
function asset(?string $filename, bool $return = true)
{
	if (is_null($filename)) $filename = "";
	$path = trim($filename, "/");
	$full_path = ASSET_PATH . "/" . $path;

	if (file_exists(DOCUMENT_ROOT_PATH . $full_path)) {
		// Xóa cache khi cập nhật file asset bằng cách thêm param lần ghi cuối cùng
		$full_path .= "?" . filemtime(DOCUMENT_ROOT_PATH . $full_path);
	}
	if ($return) return $full_path;
	echo $full_path;
}

/*
 * In ra đường dẫn đến file ở thư mục upload
 */
function upload(?string $filename, bool $return = true)
{
	if (is_null($filename)) $filename = "";
	$path = trim($filename, "/");
	$full_path = UPLOAD_PATH . "/" . $path;

	if (file_exists(DOCUMENT_ROOT_PATH . $full_path)) {
		// Xóa cache khi cập nhật file asset bằng cách thêm param lần ghi cuối cùng
		$full_path .= "?" . filemtime(DOCUMENT_ROOT_PATH . $full_path);
	}
	if ($return) return $full_path;
	echo $full_path;
}

/*
 * Tải file lên và lưu vào thư mục upload, đồng thời trả về tên file
 * Nếu upload nhiều file thì trả về mảng các tên file
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

/*
 * Xóa file trong thư mục upload, return false nếu xóa thất bại
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

/*
 * database common function
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

function get_mysqli()
{
	global $database;
	mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
	$conn = new mysqli($database["host"], $database["username"], $database["password"], $database["db"]);
	mysqli_set_charset($conn, 'UTF8');
	return $conn;
}

function execute_query(&$conn, $sql, $data)
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
	return $query;
}

function db_select(string $sql, array $data = null): array
{
	try {
		$query = execute_query($conn, $sql, $data);
		$result = $query->get_result();
		$arr_result = [];
		if ($result->num_rows > 0) {
			while ($record = $result->fetch_assoc()) {
				array_push($arr_result, $record);	// hoặc $arr_result[] = $record;
			}
		}
		$conn->close();
		return $arr_result;
	} catch (Exception $ex) {
		dd($ex->getMessage(), "Query: $sql", $ex->getTraceAsString(), $data);
	}
}

function db_execute(string $sql, array $data = null): bool
{
	try {
		$query = execute_query($conn, $sql, $data);
		$affected = $query->affected_rows;
		$conn->close();
		return $affected > 0;
	} catch (Exception $ex) {
		dd($ex->getMessage(), "Query: $sql", $ex->getTraceAsString(), $data);
	}
}

/*
 * Dùng để tạo thẻ option (trong thẻ select) từ database
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

/*
 * Dump data
 */
function dd(...$data)
{
	$textArr = ["Dump data..."];
	foreach ($data as $x) {
		$textArr[] = print_r($x, true);
	}
	$text = implode("\n\n", $textArr);
	echo "<pre style='background-color: #efefef; padding: 15px; box-sizing: border-box'>$text</pre>";
	die;
}

/*
 * Xử lý liên quan đến điều hướng đường dẫn hệ thống, file route.php
 */
function handle_request($notFoundCallback = null)
{
	global $web_routes;
	$url = str_replace(RELATIVE_ROOT_PATH, "", $_SERVER["REQUEST_URI"]);
	$url = strtok($url, '?');

	$path = "";
	if (array_key_exists($url, $web_routes)) {
		if (is_array($web_routes[$url]) && count($web_routes[$url]) == 2) {
			$path = $web_routes[$url][1];
		} else if (is_string($web_routes[$url])) {
			$path = $web_routes[$url];
		}
	}
	$path = ltrim($path, " /\\");
	if (file_exists($path)) {
		include $path;
	} else {
		if ($notFoundCallback != null) {
			$notFoundCallback();
		} else {
			echo "Không tìm thấy trang!";
		}
	}
}

function route(string $name, array $params = null)
{
	global $web_routes;
	$query_string = "";
	if ($params != null){
		$query_string = "?" . http_build_query($params);
	}
	foreach ($web_routes as $path => $path_info) {
		if (is_array($path_info) && count($path_info) == 2 && $path_info[0] === $name) {
			return RELATIVE_ROOT_PATH . $path . $query_string;
		} else if (is_string($path_info) && $path_info == $name) {
			return RELATIVE_ROOT_PATH . $path . $query_string;
		}
	}
	// Lỗi nếu không tìm thấy router
	dd("\"'> Không tìm thấy [$name] trong hệ thống đường dẫn của trang web");
}
