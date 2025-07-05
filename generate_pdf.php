<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set memory limit for large documents
ini_set('memory_limit', '256M');

class PDFGenerator {
    private $dompdf;
    private $options;
    
    public function __construct() {
        $this->options = new Options();
        $this->configureDompdf();
        $this->dompdf = new Dompdf($this->options);
    }
    
    private function configureDompdf() {
        // Basic configuration
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isRemoteEnabled', true);
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('isFontSubsettingEnabled', true);
        $this->options->set('isPhpEnabled', false);
        
        // Security settings
        $this->options->set('chroot', realpath(__DIR__));
        $this->options->set('logOutputFile', __DIR__ . '/dompdf.log');
        
        // Performance settings
        $this->options->set('debugKeepTemp', false);
        $this->options->set('debugCss', false);
        $this->options->set('debugLayout', false);
        $this->options->set('debugLayoutLines', false);
        $this->options->set('debugLayoutBlocks', false);
        $this->options->set('debugLayoutInline', false);
        $this->options->set('debugLayoutPaddingBox', false);
    }
    
    public function generatePDF($htmlContent, $config = []) {
        try {
            // Apply configuration
            $this->applyConfiguration($config);
            
            // Load HTML content
            $this->dompdf->loadHtml($htmlContent);
            
            // Set paper size and orientation
            $paperSize = isset($config['paper_size']) ? strtolower($config['paper_size']) : 'a4';
            $orientation = isset($config['orientation']) ? $config['orientation'] : 'portrait';
            
            $this->dompdf->setPaper($paperSize, $orientation);
            
            // Render PDF
            $this->dompdf->render();
            
            // Apply custom margins if specified
            if (isset($config['margin_top']) || isset($config['margin_right']) || 
                isset($config['margin_bottom']) || isset($config['margin_left'])) {
                $this->applyCustomMargins($config);
            }
            
            return $this->dompdf;
            
        } catch (Exception $e) {
            throw new Exception("PDF Generation Error: " . $e->getMessage());
        }
    }
    
    private function applyConfiguration($config) {
        // Enable/disable JavaScript
        if (isset($config['enable_javascript'])) {
            $this->options->set('isJavascriptEnabled', (bool)$config['enable_javascript']);
        }
        
        // Enable/disable remote content
        if (isset($config['enable_remote'])) {
            $this->options->set('isRemoteEnabled', (bool)$config['enable_remote']);
        }
        
        // Enable/disable font subsetting
        if (isset($config['font_subsetting'])) {
            $this->options->set('isFontSubsettingEnabled', (bool)$config['font_subsetting']);
        }
        
        // Set DPI
        if (isset($config['dpi'])) {
            $this->options->set('dpi', (int)$config['dpi']);
        }
    }
    
    private function applyCustomMargins($config) {
        $canvas = $this->dompdf->getCanvas();
        $w = $canvas->get_width();
        $h = $canvas->get_height();
        
        // Convert mm to points (1mm = 2.834645669 points)
        $mmToPoints = 2.834645669;
        
        $marginTop = isset($config['margin_top']) ? $config['margin_top'] * $mmToPoints : 56.69;
        $marginRight = isset($config['margin_right']) ? $config['margin_right'] * $mmToPoints : 56.69;
        $marginBottom = isset($config['margin_bottom']) ? $config['margin_bottom'] * $mmToPoints : 56.69;
        $marginLeft = isset($config['margin_left']) ? $config['margin_left'] * $mmToPoints : 56.69;
        
        // Note: DomPDF doesn't support runtime margin changes after rendering
        // This would need to be handled in CSS or during initial setup
    }
    
    public function outputPDF($filename = 'document.pdf', $mode = 'D') {
        return $this->dompdf->output();
    }
    
    public function streamPDF($filename = 'document.pdf') {
        $this->dompdf->stream($filename, array('Attachment' => true));
    }
}

// Handle PDF generation request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get HTML content
        $htmlContent = $_POST['html_content'] ?? '';
        
        if (empty($htmlContent)) {
            throw new Exception('No HTML content provided');
        }
        
        // Get configuration
        $config = [
            'paper_size' => $_POST['paper_size'] ?? 'A4',
            'orientation' => $_POST['orientation'] ?? 'portrait',
            'margin_top' => (int)($_POST['margin_top'] ?? 20),
            'margin_right' => (int)($_POST['margin_right'] ?? 20),
            'margin_bottom' => (int)($_POST['margin_bottom'] ?? 20),
            'margin_left' => (int)($_POST['margin_left'] ?? 20),
            'dpi' => (int)($_POST['dpi'] ?? 96),
            'enable_javascript' => isset($_POST['enable_javascript']),
            'enable_remote' => isset($_POST['enable_remote']),
            'font_subsetting' => isset($_POST['font_subsetting'])
        ];
        
        // Add custom CSS for margins
        $marginCSS = sprintf(
            '@page { margin: %dmm %dmm %dmm %dmm; }',
            $config['margin_top'],
            $config['margin_right'],
            $config['margin_bottom'],
            $config['margin_left']
        );
        
        // Inject margin CSS into HTML
        if (strpos($htmlContent, '</head>') !== false) {
            $htmlContent = str_replace('</head>', "<style>$marginCSS</style></head>", $htmlContent);
        } else {
            $htmlContent = "<style>$marginCSS</style>" . $htmlContent;
        }
        
        // Generate PDF
        $pdfGenerator = new PDFGenerator();
        $dompdf = $pdfGenerator->generatePDF($htmlContent, $config);
        
        // Generate filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "document_{$timestamp}.pdf";
        
        // Set headers for PDF download
        //header("Access-Control-Allow-Origin: *"); // for testing, restrict in production
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Output PDF
        echo $dompdf->output();
        exit;
        
    } catch (Exception $e) {
        // Handle errors gracefully
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        
        echo '<!DOCTYPE html>
<html>
<head>
    <title>PDF Generation Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f8f9fa; }
        .error-container { 
            max-width: 600px; 
            margin: 0 auto; 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error-icon { 
            width: 48px; 
            height: 48px; 
            background: #dc3545; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0 auto 20px;
        }
        h1 { color: #dc3545; text-align: center; margin-bottom: 20px; }
        .error-message { 
            background: #f8d7da; 
            color: #721c24; 
            padding: 15px; 
            border-radius: 4px; 
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .back-button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        </div>
        <h1>PDF Generation Failed</h1>
        <div class="error-message">
            <strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '
        </div>
        <p>Please check your HTML content and configuration settings, then try again.</p>
        <a href="javascript:history.back()" class="back-button">← Go Back</a>
    </div>
</body>
</html>';
        exit;
    }
}

// If accessed directly, redirect to main page
header('Location: index.php');
exit;
?>