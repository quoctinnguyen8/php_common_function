<?php

/*
 * Thay "php_common_function" bằng tên thư mục của bạn ở htdocs
 */

// Thư mục gốc ở htdocs (đối với XAMPP), để trống nếu không dùng htdocs của XAMPP
define('ROOT_PATH', "/php_common_function");

// Thư mục chứa file asset (css/js/img)
define('ASSET_PATH', ROOT_PATH . "/asset");

// Thư mục chứa file upload bởi user
define('UPLOAD_PATH', ROOT_PATH . "/upload");

// Đường dẫn đầy đủ đến thư mục hiện tại, không cần chỉnh sửa nếu dùng XAMPP
define('DOCUMENT_ROOT_PATH', $_SERVER["DOCUMENT_ROOT"]);

// Thông tin đăng nhập database
$database = [
	"host" => "localhost",
	"db" => "test",
	"username" => "root",
	"password" => "",
];

