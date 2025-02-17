<?php
function adminer_object()
{
    // Required to run any plugin.
    include_once "./plugins/plugin.php";

    // Autoloader.
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }

    // Enable extra drivers just by including them.
    // include_once "./plugins/drivers/elastic.php";

    // Specify enabled plugins.
    $plugins = [
    new AdminerDumpXml(),
    new AdminerTinymce(),
    new AdminerEditCalendar(),
    // new AdminerFileUpload("data/"),
    // ...
    ];

    // It is possible to combine customization and plugins.
    // class AdminerCustomization extends AdminerPlugin {
    // }
    // return new AdminerCustomization($plugins);

    return new AdminerPlugin($plugins);
}

// Include original Adminer or Adminer Editor.
require "./adminer.php";
