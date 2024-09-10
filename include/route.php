<?php

/*
 * Hệ thống đường dẫn trong trang web
 * Đường dẫn phải bắt đầu bằng dấu "/"
 * Cấu trúc:
 *      /đường-dẫn => [tên-đường-dẫn, tên-file]
 *  hoặc
 *      /đường-dẫn => tên-file
 *     
 * Trong đó:
 *      @string     /đường-dẫn:         URL mong muốn
 *      @string     tên-đường-dẫn:      tên không trùng dùng để điều hướng trong hệ thống
 *      @string     tên-file:           tên file sẽ load khi truy cập /đường-dẫn
 */

return [
    "/"                     => ["trangchu", "web/index.php"],
    "/lien-he"              => ["lienhe", "web/contact.php"],
    "/contact"              => "web/contact.php"
];