<?php
/**
 * DomPDF UI Tester Configuration
 * 
 * This file contains configuration settings for the DomPDF UI Tester application.
 * Modify these settings according to your environment and requirements.
 */

// Application Configuration
define('APP_NAME', 'Project MatDom');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', false); // Set to true for development

// PDF Generation Settings
define('PDF_MEMORY_LIMIT', '256M');
define('PDF_EXECUTION_TIME', 300); // seconds
define('PDF_MAX_FILE_SIZE', '10M');

// Default PDF Settings
define('DEFAULT_PAPER_SIZE', 'A4');
define('DEFAULT_ORIENTATION', 'portrait');
define('DEFAULT_DPI', 96);
define('DEFAULT_MARGIN_TOP', 20);
define('DEFAULT_MARGIN_RIGHT', 20);
define('DEFAULT_MARGIN_BOTTOM', 20);
define('DEFAULT_MARGIN_LEFT', 20);

// Security Settings
define('ENABLE_REMOTE_CONTENT', true);
define('ENABLE_JAVASCRIPT', false);
define('ENABLE_PHP', false);

// File Paths
define('TEMP_DIR', sys_get_temp_dir());
define('LOG_DIR', __DIR__ . '/logs');
define('CACHE_DIR', __DIR__ . '/cache');

// Supported Paper Sizes
$SUPPORTED_PAPER_SIZES = [
    'A4' => ['width' => 210, 'height' => 297, 'unit' => 'mm'],
    'A3' => ['width' => 297, 'height' => 420, 'unit' => 'mm'],
    'Letter' => ['width' => 8.5, 'height' => 11, 'unit' => 'in'],
    'Legal' => ['width' => 8.5, 'height' => 14, 'unit' => 'in']
];

// DPI Options
$SUPPORTED_DPI = [72, 96, 150, 300];

// Maximum Values
define('MAX_MARGIN', 50); // mm
define('MIN_MARGIN', 0);  // mm

// Error Messages
$ERROR_MESSAGES = [
    'no_html' => 'No HTML content provided. Please enter some HTML content.',
    'invalid_config' => 'Invalid configuration parameters provided.',
    'generation_failed' => 'PDF generation failed. Please check your HTML content.',
    'file_too_large' => 'The generated PDF file is too large.',
    'memory_exceeded' => 'Memory limit exceeded during PDF generation.',
    'timeout' => 'PDF generation timed out. Try with smaller content.'
];

// Success Messages
$SUCCESS_MESSAGES = [
    'pdf_generated' => 'PDF generated successfully!',
    'config_saved' => 'Configuration saved successfully.',
    'content_copied' => 'Content copied to clipboard.'
];

/**
 * Get configuration value
 * 
 * @param string $key Configuration key
 * @param mixed $default Default value if key not found
 * @return mixed Configuration value
 */
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Check if debug mode is enabled
 * 
 * @return bool True if debug mode is enabled
 */
function isDebugMode() {
    return getConfig('APP_DEBUG', false);
}

/**
 * Get supported paper sizes
 * 
 * @return array Supported paper sizes
 */
function getSupportedPaperSizes() {
    global $SUPPORTED_PAPER_SIZES;
    return $SUPPORTED_PAPER_SIZES;
}

/**
 * Get supported DPI values
 * 
 * @return array Supported DPI values
 */
function getSupportedDPI() {
    global $SUPPORTED_DPI;
    return $SUPPORTED_DPI;
}

/**
 * Get error message
 * 
 * @param string $key Error key
 * @return string Error message
 */
function getErrorMessage($key) {
    global $ERROR_MESSAGES;
    return isset($ERROR_MESSAGES[$key]) ? $ERROR_MESSAGES[$key] : 'An unknown error occurred.';
}

/**
 * Get success message
 * 
 * @param string $key Success key
 * @return string Success message
 */
function getSuccessMessage($key) {
    global $SUCCESS_MESSAGES;
    return isset($SUCCESS_MESSAGES[$key]) ? $SUCCESS_MESSAGES[$key] : 'Operation completed successfully.';
}

/**
 * Validate paper size
 * 
 * @param string $paperSize Paper size to validate
 * @return bool True if valid
 */
function isValidPaperSize($paperSize) {
    $supportedSizes = getSupportedPaperSizes();
    return array_key_exists($paperSize, $supportedSizes);
}

/**
 * Validate DPI value
 * 
 * @param int $dpi DPI value to validate
 * @return bool True if valid
 */
function isValidDPI($dpi) {
    return in_array((int)$dpi, getSupportedDPI());
}

/**
 * Validate margin value
 * 
 * @param int $margin Margin value to validate
 * @return bool True if valid
 */
function isValidMargin($margin) {
    $margin = (int)$margin;
    return $margin >= MIN_MARGIN && $margin <= MAX_MARGIN;
}

/**
 * Sanitize HTML content
 * 
 * @param string $html HTML content to sanitize
 * @return string Sanitized HTML
 */
function sanitizeHTML($html) {
    // Basic HTML sanitization - you might want to use a more robust library
    // like HTML Purifier for production use
    return $html;
}

/**
 * Log error message
 * 
 * @param string $message Error message
 * @param string $level Error level (error, warning, info)
 */
function logError($message, $level = 'error') {
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }
    
    $logFile = LOG_DIR . '/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Initialize directories
if (!is_dir(LOG_DIR)) {
    mkdir(LOG_DIR, 0755, true);
}

if (!is_dir(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}
?>
