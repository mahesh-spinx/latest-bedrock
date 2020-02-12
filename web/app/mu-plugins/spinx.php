<?php
/**
 * Plugin Name: Spinx Core Module
 * Description: This Plugin is loading all core project's functionality.
 */

// require_once(dirname(__FILE__) . "/spinx/vendor/autoload.php");
// require_once(dirname(__FILE__) . '/spinx/vendor/johnbillion/extended-cpts/extended-cpts.php');

/**
 * Register: Post Types, Taxonomies, Shortcodes, Widgets
 */
registerModules(['post-types']);

// require_once(dirname(__FILE__) . "/spinx/post-types/book.php");

/**
 * ACF: Json Sync
 */
add_filter('acf/settings/save_json', function ($path) {
    $path = dirname(__FILE__) . '/spinx/acf-json';
    return $path;
});

add_filter('acf/settings/load_json', function ( $paths ) {
    unset($paths[0]);
    $paths[] = dirname(__FILE__) . '/spinx/acf-json';
    return $paths;
});

/**
 * ACF: Load only for developer
 */
if (WP_ENV !== 'development') {
    add_action('acf/init', function() {
        acf_update_setting('show_admin', false);
    });
}

/**
 * Helper Functions
 */
function registerModules($modules) {
    foreach ($modules as $moduleName) {
        $files = glob(dirname(__FILE__) . '/spinx/'.$moduleName.'/*.php');

        foreach ($files as $file)
            require_once($file);
    }
}
