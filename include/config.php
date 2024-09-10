<?php

/*
 * Thay "php_common_function" bằng tên thư mục của bạn ở htdocs
 */

// Thư mục gốc ở htdocs (đối với XAMPP)
define('RELATIVE_ROOT_PATH', "/php_common_function");

// Thư mục chứa file asset (css/js/img)
define('ASSET_PATH', RELATIVE_ROOT_PATH . "/asset");

// Thư mục chứa file upload bởi user
define('UPLOAD_PATH', RELATIVE_ROOT_PATH . "/upload");

// Đường dẫn đầy đủ đến thư mục hiện tại, không cần chỉnh sửa nếu dùng XAMPP
define('DOCUMENT_ROOT_PATH', $_SERVER["DOCUMENT_ROOT"]);

// Hệ thống router trong trang web
$web_routes = require "route.php";

// Thông tin đăng nhập database
$database = [
	"host" => "localhost",
	"db" => "test",
	"username" => "root",
	"password" => "",
];
