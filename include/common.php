<?php

include("db_common.php");

function js_alert($text)
{
	echo "<script>alert('" . addslashes($text) . "');</script>";
}

function is_method_post()
{
	return $_SERVER['REQUEST_METHOD'] == 'POST';
}
function is_method_get()
{
	return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function redirect_to($page)
{
	$page = ROOT_PATH . "/$page";
	$page = str_replace("//", "/", $page);
	if ($page[0] == "/") {
		$page = substr($page, 1);
	}
	if (!empty($page)) {
		header("Location: /$page");
	}
}

function asset($path)
{
	$path = trim($path, "/");
	$full_path = ASSET_PATH . "/" . $path;

	if (file_exists(DOCUMENT_ROOT_PATH . $full_path)) {
		// Xóa cache khi cập nhật file asset bằng cách thêm param lần ghi cuối cùng
		$full_path .= "?" . filemtime(DOCUMENT_ROOT_PATH . $full_path);
	}
	echo $full_path;
}
