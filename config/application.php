<?php
/**
 * Your base production configuration goes in this file. Environment-specific
 * overrides go in their respective config/environments/{{WP_ENV}}.php file.
 *
 * A good default policy is to deviate from the production config as little as
 * possible. Try to define as much of your configuration in this file as you
 * can.
 */

use Roots\WPConfig\Config;
use function Env\env;

/**
 * Directory containing all of the site's files
 *
 * @var string
 */
$root_dir = dirname(__DIR__);

/**
 * Document Root
 *
 * @var string
 */
$webroot_dir = $root_dir . '/web';

/**
 * Use Dotenv to set required environment variables and load .env file in root
 * .env.local will override .env if it exists
 */
if (file_exists($root_dir . '/.env')) {
    $env_files = file_exists($root_dir . '/.env.local')
        ? ['.env', '.env.local']
        : ['.env'];

    $dotenv = Dotenv\Dotenv::createUnsafeImmutable($root_dir, $env_files, false);

    $dotenv->load();

    $dotenv->required(['WP_HOME', 'WP_SITEURL']);
    if (!env('DATABASE_URL')) {
        $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD']);
    }
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define('WP_ENV', env('WP_ENV') ?: 'production');

/**
 * Infer WP_ENVIRONMENT_TYPE based on WP_ENV
 */
if (!env('WP_ENVIRONMENT_TYPE') && in_array(WP_ENV, ['production', 'staging', 'development', 'local'])) {
    Config::define('WP_ENVIRONMENT_TYPE', WP_ENV);
}

/**
 * URLs
 */
Config::define('WP_HOME', env('WP_HOME'));
Config::define('WP_SITEURL', env('WP_SITEURL'));

/**
 * Custom Content Directory
 */
Config::define('CONTENT_DIR', '/app');
Config::define('WP_CONTENT_DIR', $webroot_dir . Config::get('CONTENT_DIR'));
Config::define('WP_CONTENT_URL', Config::get('WP_HOME') . Config::get('CONTENT_DIR'));

/**
 * WP Rocket Settings
 */
Config::define('WP_ROCKET_EMAIL', env('WP_ROCKET_EMAIL'));
Config::define('WP_ROCKET_KEY', env('WP_ROCKET_KEY'));

if (! defined('WP_CACHE'))
    Config::define('WP_CACHE', env('WP_CACHE'));

/**
 * DB settings
 */
if (env('DB_SSL')) {
    Config::define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}

Config::define('DB_NAME', env('DB_NAME'));
Config::define('DB_USER', env('DB_USER'));
Config::define('DB_PASSWORD', env('DB_PASSWORD'));
Config::define('DB_HOST', env('DB_HOST') ?: 'localhost');
Config::define('DB_CHARSET', 'utf8mb4');
Config::define('DB_COLLATE', '');
$table_prefix = env('DB_PREFIX') ?: 'wp_';

if (env('DATABASE_URL')) {
    $dsn = (object) parse_url(env('DATABASE_URL'));

    Config::define('DB_NAME', substr($dsn->path, 1));
    Config::define('DB_USER', $dsn->user);
    Config::define('DB_PASSWORD', isset($dsn->pass) ? $dsn->pass : null);
    Config::define('DB_HOST', isset($dsn->port) ? "{$dsn->host}:{$dsn->port}" : $dsn->host);
}

/**
 * Authentication Unique Keys and Salts
 */
Config::define('AUTH_KEY', env('AUTH_KEY'));
Config::define('SECURE_AUTH_KEY', env('SECURE_AUTH_KEY'));
Config::define('LOGGED_IN_KEY', env('LOGGED_IN_KEY'));
Config::define('NONCE_KEY', env('NONCE_KEY'));
Config::define('AUTH_SALT', env('AUTH_SALT'));
Config::define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT'));
Config::define('LOGGED_IN_SALT', env('LOGGED_IN_SALT'));
Config::define('NONCE_SALT', env('NONCE_SALT'));

/**
 * Custom Settings
 */
Config::define('AUTOMATIC_UPDATER_DISABLED', true);
Config::define('DISABLE_WP_CRON', env('DISABLE_WP_CRON') ?: false);

// Disable the plugin and theme file editor in the admin
Config::define('DISALLOW_FILE_EDIT', true);

// Disable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', true);

// Limit the number of post revisions
Config::define('WP_POST_REVISIONS', env('WP_POST_REVISIONS') ?? true);

/**
 * WP Migrate DB Pro
 */
Config::define('WPMDB_LICENCE', env('WPMDB_LICENCE'));

/**
 * WP Offload Media
 */
Config::define('AS3CFPRO_LICENCE', env('AS3CFPRO_LICENCE'));

/**
 * WP Offload Media: Cloud File Settings
 */
Config::define('AS3CF_SETTINGS', serialize(array(
    // Storage Provider ('aws', 'do', 'gcp')
    'provider' => env('AS3CF_PROVIDER'),
    // Access Key ID for Storage Provider (aws and do only, replace '*')
    'access-key-id' => env('AS3CF_S3_UPLOADS_KEY'),
    // Secret Access Key for Storage Providers (aws and do only, replace '*')
    'secret-access-key' => env('AS3CF_S3_UPLOADS_SECRET'),
    // GCP Key File Path (gcp only)
    // Make sure hidden from public website, i.e. outside site's document root.
    // 'key-file-path' => '/path/to/key/file.json',
    // Bucket to upload files to
    'bucket' => env('AS3CF_BUCKET'),
    // Bucket region (e.g. 'us-west-1' - leave blank for default region)
    'region' => '',
    // Automatically copy files to bucket on upload
    'copy-to-s3' => env('AS3CF_COPY_TO_S3'),
    // Rewrite file URLs to bucket
    'serve-from-s3' => env('AS3CF_SERVE_FROM_S3'),
    // Bucket URL format to use ('path', 'cloudfront')
    'domain' => env('AS3CF_DOMAIN'),
    // Custom domain if 'domain' set to 'cloudfront'
    // 'cloudfront' => 'cdn.exmple.com',
    // Enable object prefix, useful if you use your bucket for other files
    'enable-object-prefix' => env('AS3CF_ENABLE_OBJECT_PREFIX'),
    // Object prefix to use if 'enable-object-prefix' is 'true'
    'object-prefix' => env('AS3CF_OBJECT_PREFIX'),
    // Organize bucket files into YYYY/MM directories
    'use-yearmonth-folders' => env('AS3CF_USE_YEARMONTH_FOLDERS'),
    // Serve files over HTTPS
    'force-https' => env('AS3CF_FORCE_HTTPS'),
    // Remove the local file version once offloaded to bucket
    'remove-local-file' => env('AS3CF_REMOVE_LOCAL_FILE'),
    // Append a timestamped folder to path of files offloaded to bucket
    'object-versioning' => env('AS3CF_OBJECT_VERSIONING'),
)));

/**
 * Debugging Settings
 */
Config::define('WP_DEBUG_DISPLAY', false);
Config::define('WP_DEBUG_LOG', false);
Config::define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');

/**
 * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
 * See https://codex.wordpress.org/Function_Reference/is_ssl#Notes
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
    require_once $env_config;
}

Config::apply();

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
    define('ABSPATH', $webroot_dir . '/wp/');
}
