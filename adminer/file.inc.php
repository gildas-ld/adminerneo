<?php
// caching headers added in compile.php

if ($_GET["file"] == "favicon.ico") {
    header("Content-Type: image/x-icon");
    echo bzdecompress(compile_file('../adminer/static/favicon.ico', 'bzcompress'));
} elseif ($_GET["file"] == "default.css") {
    header("Content-Type: text/css; charset=utf-8");
    echo bzdecompress(compile_file('../adminer/static/default.css;../building-tools/jush/jush.css', 'minify_css'));
} elseif ($_GET["file"] == "functions.js") {
    header("Content-Type: text/javascript; charset=utf-8");
    echo bzdecompress(compile_file('../adminer/static/functions.js;static/editing.js', 'minify_js'));
} elseif ($_GET["file"] == "jush.js") {
    header("Content-Type: text/javascript; charset=utf-8");
    echo bzdecompress(compile_file('../building-tools/jush/modules/jush.js;../building-tools/jush/modules/jush-textarea.js;../building-tools/jush/modules/jush-txt.js;../building-tools/jush/modules/jush-js.js;../building-tools/jush/modules/jush-sql.js;../building-tools/jush/modules/jush-pgsql.js;../building-tools/jush/modules/jush-sqlite.js;../building-tools/jush/modules/jush-mssql.js;../building-tools/jush/modules/jush-oracle.js;../building-tools/jush/modules/jush-simpledb.js', 'minify_js'));
} else {
    header("Content-Type: image/gif");
    switch ($_GET["file"]) {
        case "plus.gif":
            echo compile_file('../adminer/static/plus.gif');
            break;
        case "cross.gif":
            echo compile_file('../adminer/static/cross.gif');
            break;
        case "up.gif":
            echo compile_file('../adminer/static/up.gif');
            break;
        case "down.gif":
            echo compile_file('../adminer/static/down.gif');
            break;
        case "arrow.gif":
            echo compile_file('../adminer/static/arrow.gif');
            break;
    }
}
exit;
