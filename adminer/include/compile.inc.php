<?php

namespace Adminer;

include __DIR__ . "/../../vendor/autoload.php";


function read_compiled_file(string $filename): string
{
    $file_path = "compiled/$filename";

    if (!file_exists($file_path)) {
        http_response_code(404);
        exit;
    }

    return file_get_contents($file_path);
}

function link_files(string $name, array $file_paths): string
{
    static $links = [];

    if (isset($links[$name])) {
        return $links[$name];
    }

    $linked_filename = linked_filename($name, $file_paths);

    if (!file_exists("compiled/$linked_filename")) {
        // Delete old compiled files.
        $name_pattern = preg_replace('~\.[^.]+$~', "-*$0", $name);
        foreach (glob("compiled/$name_pattern") as $filename) {
            unlink($filename);
        }

        // Compile and save the file.
        $file = compile_file($name, $file_paths);
        file_put_contents("compiled/$linked_filename", $file);
    }

    return $links[$name] = BASE_URL . "?file=" . urldecode($linked_filename);
}

function linked_filename(string $name, array $file_paths): string
{
    $version = "";
    foreach ($file_paths as $file_path) {
        $version .= $file_path . filemtime(__DIR__ . "/../$file_path");
    }
    $version = substr(md5($version), 0, 8);

    return preg_replace('~\.[^.]+$~', "-$version$0", $name);
}

function compile_file(string $name, array $file_paths): string
{
    switch (pathinfo($name, PATHINFO_EXTENSION)) {
        case "css":
            $shrink_function = "Adminer\\minify_css";
            break;
        case "js":
            $shrink_function = "Adminer\\minify_js";
            break;
        default:
            $shrink_function = null;
            break;
    }

    $file = "";
    foreach ($file_paths as $file_path) {
        $file .= file_get_contents(__DIR__ . "/../$file_path");
    }

    if ($shrink_function) {
        $file = call_user_func($shrink_function, $file);
    }

    return base64_encode(bzcompress($file), 8);
}

function minify_css(string $file): string
{
    return preg_replace('~\s*([:;{},])\s*~', '\1', preg_replace('~/\*.*\*/~sU', '', $file));
}

function minify_js(string $file): string
{
    $minifiedCode = \JShrink\Minifier::minify($file, array('flaggedComments' => false));
    return ($minifiedCode);
}
