<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomPDF UI Tester</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .code-editor {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
        }
        .pdf-preview {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border: 1px solid #e5e7eb;
        }
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        .editor-container {
            display: flex;
            height: 80vh;
            background: #1a202c;
            border-radius: 0 0 0.5rem 0.5rem;
            overflow: hidden;
        }
        .line-numbers {
            background: #2d3748;
            color: #718096;
            padding: 1rem 0.75rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
            text-align: right;
            user-select: none;
            border-right: 1px solid #4a5568;
            min-width: 60px;
            overflow: hidden;
            white-space: pre;
        }
        .editor-textarea {
            flex: 1;
            background: #1a202c;
            color: #e2e8f0;
            border: none;
            outline: none;
            resize: none;
            padding: 1rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
            overflow-y: auto;
            white-space: pre;
            word-wrap: break-word;
        }
        .tab-button.active {
            border-bottom-color: #3b82f6 !important;
            color: #3b82f6 !important;
        }
        .pdf-iframe {
            width: 100%;
            height: 80vh;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            background: #f8f9fa;
        }
        .pdf-viewer-container {
            width: 100%;
            height: 80vh;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            background: #ffffff;
            overflow: hidden;
        }
        .pdf-viewer-fallback {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            text-align: center;
            padding: 2rem;
        }
        /* Ensure line numbers and textarea scroll together */
        .editor-container::-webkit-scrollbar {
            width: 8px;
        }
        .editor-container::-webkit-scrollbar-track {
            background: #2d3748;
        }
        .editor-container::-webkit-scrollbar-thumb {
            background: #4a5568;
            border-radius: 4px;
        }
        .editor-container::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="gradient-bg shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center justify-center w-10 h-10 bg-white bg-opacity-20 rounded-lg backdrop-filter backdrop-blur-sm">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white">DomPDF UI Tester</h1>
                        <p class="text-sm text-white text-opacity-80">Professional HTML to PDF conversion</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button onclick="generatePDF()" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 text-white text-sm font-medium rounded-lg hover:bg-opacity-30 transition-all duration-200 backdrop-filter backdrop-blur-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Generate PDF
                    </button>
                    <button onclick="downloadPDF()" class="inline-flex items-center px-4 py-2 bg-emerald-500 text-white text-sm font-medium rounded-lg hover:bg-emerald-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar - Configuration Panel -->
            <div class="lg:col-span-1">
                <div class="glass-effect rounded-xl shadow-lg border border-white border-opacity-20 p-6 sticky top-8">
                    <div class="flex items-center space-x-2 mb-6">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900">PDF Configuration</h2>
                    </div>
                    
                    <form id="pdfConfig" class="space-y-6">
                        <!-- Paper Settings -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Paper Settings
                            </h3>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Paper Size</label>
                                <select name="paper_size" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <option value="A4">A4 (210 × 297 mm)</option>
                                    <option value="A3">A3 (297 × 420 mm)</option>
                                    <option value="Letter">Letter (8.5 × 11 in)</option>
                                    <option value="Legal">Legal (8.5 × 14 in)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Orientation</label>
                                <div class="flex space-x-2">
                                    <label class="flex-1">
                                        <input type="radio" name="orientation" value="portrait" checked class="sr-only">
                                        <div class="px-3 py-2 text-xs font-medium text-center rounded-lg cursor-pointer transition-all duration-200 bg-blue-100 text-blue-700 border border-blue-300">
                                            Portrait
                                        </div>
                                    </label>
                                    <label class="flex-1">
                                        <input type="radio" name="orientation" value="landscape" class="sr-only">
                                        <div class="px-3 py-2 text-xs font-medium text-center rounded-lg cursor-pointer transition-all duration-200 bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200">
                                            Landscape
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Margins -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                                Margins (mm)
                            </h3>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Top</label>
                                    <input type="number" name="margin_top" value="20" min="0" max="50" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Right</label>
                                    <input type="number" name="margin_right" value="20" min="0" max="50" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Bottom</label>
                                    <input type="number" name="margin_bottom" value="20" min="0" max="50" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Left</label>
                                    <input type="number" name="margin_left" value="20" min="0" max="50" class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Rendering Options -->
                        <div class="space-y-4">
                            <h3 class="text-sm font-semibold text-gray-800 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Rendering Options
                            </h3>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">DPI Quality</label>
                                <select name="dpi" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="72">72 DPI (Screen)</option>
                                    <option value="96" selected>96 DPI (Default)</option>
                                    <option value="150">150 DPI (High)</option>
                                    <option value="300">300 DPI (Print)</option>
                                </select>
                            </div>

                            <div class="space-y-3">
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="enable_javascript" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-xs text-gray-700">Enable JavaScript</span>
                                </label>
                                
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="enable_remote" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-xs text-gray-700">Enable Remote Content</span>
                                </label>
                                
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="font_subsetting" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="text-xs text-gray-700">Font Subsetting</span>
                                </label>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="pt-4 border-t border-gray-200">
                            <button type="button" onclick="resetDefaults()" class="w-full px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                Reset to Defaults
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200 bg-gray-50">
                        <nav class="flex space-x-8 px-6" id="tabNavigation">
                            <button onclick="switchTab('source')" id="sourceTab" class="tab-button py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600 transition-colors active">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                HTML Source
                            </button>
                            <button onclick="switchTab('render')" id="renderTab" class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Render Result
                            </button>
                            <!-- Generated PDF tab will be added dynamically -->
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="h-[calc(100vh-300px)]">
                        <!-- HTML Source Tab -->
                        <div id="sourceContent" class="tab-content active h-full flex flex-col">
                            <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-700">HTML Editor</span>
                                    <span class="text-xs text-gray-500" id="charCount">• 0 characters</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="copyToClipboard()" class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-800 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Copy
                                    </button>
                                    <button onclick="resetHTML()" class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-600 hover:text-gray-800 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Reset
                                    </button>
                                </div>
                            </div>
                            
                            <div class="editor-container">
                                <div class="line-numbers" id="lineNumbers"></div>
                                <textarea id="htmlEditor" class="editor-textarea" placeholder="Paste your HTML content here..." spellcheck="false"></textarea>
                            </div>
                        </div>

                        <!-- Render Result Tab -->
                        <div id="renderContent" class="tab-content h-full bg-gray-100">
                            <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Render Result</span>
                                    </div>
                                    <div class="text-xs text-gray-500" id="pdfInfo">
                                        A4 • Portrait • 96 DPI
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <button onclick="refreshPreview()" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        Refresh
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 overflow-auto p-8">
                                <div class="flex justify-center">
                                    <div id="pdfPreviewContainer" class="pdf-preview bg-white rounded-lg p-8 max-w-4xl w-full">
                                        <div class="text-center text-gray-500 py-12">
                                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <p class="text-lg font-medium">No PDF generated yet</p>
                                            <p class="text-sm">Click "Generate PDF" to see the result</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Generated PDF Tab (will be added dynamically) -->
                        <div id="generatedContent" class="tab-content h-full bg-gray-100" style="display: none;">
                            <div class="flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700">Generated PDF</span>
                                    </div>
                                    <div class="text-xs text-emerald-600" id="generatedInfo">
                                        Ready for download
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <button onclick="downloadPDF()" class="inline-flex items-center px-3 py-1 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>

                            <div class="flex-1 overflow-auto p-8">
                                <div class="flex justify-center">
                                    <div class="pdf-viewer-container">
                                        <iframe id="pdfViewer" class="pdf-iframe" style="display: none;"></iframe>
                                        <div id="pdfFallback" class="pdf-viewer-fallback">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">PDF Ready for Download</h3>
                                            <p class="text-sm text-gray-500 mb-4">Your PDF has been generated successfully. Click the download button to save it.</p>
                                            <button onclick="downloadPDF()" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <div class="flex items-center space-x-3">
                <div class="loading-spinner"></div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Generating PDF</h3>
                    <p class="text-sm text-gray-500">Please wait while we process your document...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPdfBlob = null;
        let currentPdfUrl = null;

        // Initialize with sample HTML
        const sampleHTML = `<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sample Document</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            margin: 40px; 
            line-height: 1.6;
            color: #333;
        }
        .header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px; 
            border-radius: 12px; 
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 { 
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .content { 
            max-width: 800px;
            margin: 0 auto;
        }
        .section {
            margin-bottom: 40px;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .section h2 {
            color: #667eea;
            margin-top: 0;
            font-size: 1.8em;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 25px 0;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #eee;
        }
        th { 
            background: #667eea;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
        }
        tr:hover {
            background: #f8f9fa;
        }
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        .feature-item {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .feature-item h3 {
            color: #667eea;
            margin-top: 0;
            font-size: 1.2em;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-supported {
            background: #d4edda;
            color: #155724;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DomPDF Professional Test</h1>
        <p>Advanced HTML to PDF Conversion Demonstration</p>
    </div>
    
    <div class="content">
        <div class="section">
            <h2>🚀 Advanced Features Test</h2>
            <p>This document demonstrates the advanced capabilities of DomPDF with modern CSS styling, responsive layouts, and professional typography.</p>
            
            <div class="feature-list">
                <div class="feature-item">
                    <h3>Typography</h3>
                    <p>Modern font stacks with proper line-height and spacing for optimal readability.</p>
                </div>
                <div class="feature-item">
                    <h3>CSS Grid & Flexbox</h3>
                    <p>Advanced layout techniques for responsive and flexible designs.</p>
                </div>
                <div class="feature-item">
                    <h3>Gradients & Shadows</h3>
                    <p>Beautiful visual effects including gradients, shadows, and modern styling.</p>
                </div>
                <div class="feature-item">
                    <h3>Professional Tables</h3>
                    <p>Styled data tables with hover effects and proper spacing.</p>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>📊 Compatibility Matrix</h2>
            <table>
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th>Status</th>
                        <th>CSS Level</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>CSS Styling</td>
                        <td><span class="status-badge status-supported">✓ Supported</span></td>
                        <td>CSS 2.1 + Partial CSS3</td>
                        <td>Excellent support for most properties</td>
                    </tr>
                    <tr>
                        <td>Typography</td>
                        <td><span class="status-badge status-supported">✓ Supported</span></td>
                        <td>Full</td>
                        <td>Web fonts and font stacks work perfectly</td>
                    </tr>
                    <tr>
                        <td>Tables & Layout</td>
                        <td><span class="status-badge status-supported">✓ Supported</span></td>
                        <td>Full</td>
                        <td>Complex table layouts render correctly</td>
                    </tr>
                    <tr>
                        <td>Images & Media</td>
                        <td><span class="status-badge status-supported">✓ Supported</span></td>
                        <td>Full</td>
                        <td>PNG, JPEG, GIF, SVG support</td>
                    </tr>
                    <tr>
                        <td>CSS Grid</td>
                        <td><span class="status-badge status-supported">✓ Supported</span></td>
                        <td>Partial</td>
                        <td>Basic grid layouts work well</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>🎯 Performance Metrics</h2>
            <p>DomPDF provides excellent performance for document generation with the following characteristics:</p>
            <ul style="list-style-type: none; padding: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid #eee;">⚡ Fast rendering for documents up to 100 pages</li>
                <li style="padding: 8px 0; border-bottom: 1px solid #eee;">🎨 High-quality output with proper font rendering</li>
                <li style="padding: 8px 0; border-bottom: 1px solid #eee;">📱 Responsive design support for various paper sizes</li>
                <li style="padding: 8px 0; border-bottom: 1px solid #eee;">🔒 Secure processing with no external dependencies</li>
                <li style="padding: 8px 0;">💾 Memory efficient for large document processing</li>
            </ul>
        </div>
    </div>
    
    <div class="footer">
        <p><strong>DomPDF UI Tester</strong> - Professional HTML to PDF Conversion</p>
        <p>Generated on ${new Date().toLocaleDateString()} at ${new Date().toLocaleTimeString()}</p>
    </div>
</body>
</html>`;

        document.getElementById('htmlEditor').value = sampleHTML;
        updateCharCount();
        updateLineNumbers();

        // Tab switching
        function switchTab(tab) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            document.getElementById(tab + 'Content').classList.add('active');
            const tabButton = document.getElementById(tab + 'Tab');
            if (tabButton) {
                tabButton.classList.remove('border-transparent', 'text-gray-500');
                tabButton.classList.add('active', 'border-blue-500', 'text-blue-600');
            }
        }

        // Add Generated PDF tab
        function addGeneratedPDFTab() {
            const tabNav = document.getElementById('tabNavigation');
            
            // Check if tab already exists
            if (document.getElementById('generatedTab')) {
                return;
            }

            const generatedTab = document.createElement('button');
            generatedTab.id = 'generatedTab';
            generatedTab.className = 'tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors';
            generatedTab.onclick = () => switchTab('generated');
            generatedTab.innerHTML = `
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Generated PDF
            `;
            
            tabNav.appendChild(generatedTab);
        }

        // Line numbers update
        function updateLineNumbers() {
            const editor = document.getElementById('htmlEditor');
            const lineNumbers = document.getElementById('lineNumbers');
            
            // Count actual lines including empty ones
            const content = editor.value;
            const lines = content.split('\n');
            const lineCount = lines.length;
            
            // Generate line numbers
            let lineNumbersHTML = '';
            for (let i = 1; i <= lineCount; i++) {
                lineNumbersHTML += i + '\n';
            }
            
            // Remove the last newline to prevent extra line
            lineNumbers.textContent = lineNumbersHTML.slice(0, -1);
        }

        // Sync scroll between line numbers and editor
        function syncScroll() {
            const editor = document.getElementById('htmlEditor');
            const lineNumbers = document.getElementById('lineNumbers');
            lineNumbers.scrollTop = editor.scrollTop;
        }

        // Character count update
        function updateCharCount() {
            const editor = document.getElementById('htmlEditor');
            const count = editor.value.length;
            document.getElementById('charCount').textContent = `• ${count.toLocaleString()} characters`;
        }

        // Editor event listeners
        document.getElementById('htmlEditor').addEventListener('input', () => {
            updateCharCount();
            updateLineNumbers();
        });

        document.getElementById('htmlEditor').addEventListener('scroll', syncScroll);

        // Copy to clipboard
        function copyToClipboard() {
            const editor = document.getElementById('htmlEditor');
            editor.select();
            document.execCommand('copy');
            
            // Show feedback
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Copied!';
            setTimeout(() => {
                button.innerHTML = originalText;
            }, 2000);
        }

        // Reset HTML
        function resetHTML() {
            if (confirm('Are you sure you want to reset the HTML content?')) {
                document.getElementById('htmlEditor').value = sampleHTML;
                updateCharCount();
                updateLineNumbers();
            }
        }

        // Reset defaults
        function resetDefaults() {
            document.querySelector('[name="paper_size"]').value = 'A4';
            document.querySelector('[name="orientation"][value="portrait"]').checked = true;
            document.querySelector('[name="margin_top"]').value = '20';
            document.querySelector('[name="margin_right"]').value = '20';
            document.querySelector('[name="margin_bottom"]').value = '20';
            document.querySelector('[name="margin_left"]').value = '20';
            document.querySelector('[name="dpi"]').value = '96';
            document.querySelector('[name="enable_javascript"]').checked = false;
            document.querySelector('[name="enable_remote"]').checked = true;
            document.querySelector('[name="font_subsetting"]').checked = true;
            updateOrientationButtons();
            updatePDFInfo();
        }

        // Update orientation buttons
        function updateOrientationButtons() {
            document.querySelectorAll('[name="orientation"]').forEach(radio => {
                const label = radio.parentElement.querySelector('div');
                if (radio.checked) {
                    label.className = 'px-3 py-2 text-xs font-medium text-center rounded-lg cursor-pointer transition-all duration-200 bg-blue-100 text-blue-700 border border-blue-300';
                } else {
                    label.className = 'px-3 py-2 text-xs font-medium text-center rounded-lg cursor-pointer transition-all duration-200 bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200';
                }
            });
        }

        // Update PDF info
        function updatePDFInfo() {
            const paperSize = document.querySelector('[name="paper_size"]').value;
            const orientation = document.querySelector('[name="orientation"]:checked').value;
            const dpi = document.querySelector('[name="dpi"]').value;
            document.getElementById('pdfInfo').textContent = `${paperSize} • ${orientation.charAt(0).toUpperCase() + orientation.slice(1)} • ${dpi} DPI`;
        }

        // Event listeners for form changes
        document.querySelectorAll('[name="orientation"]').forEach(radio => {
            radio.addEventListener('change', () => {
                updateOrientationButtons();
                updatePDFInfo();
            });
        });

        document.querySelector('[name="paper_size"]').addEventListener('change', updatePDFInfo);
        document.querySelector('[name="dpi"]').addEventListener('change', updatePDFInfo);

        // Generate PDF
        function generatePDF() {
            const htmlContent = document.getElementById('htmlEditor').value;
            if (!htmlContent.trim()) {
                alert('Please enter some HTML content first.');
                return;
            }

            document.getElementById('loadingModal').classList.remove('hidden');
            
            // Switch to render result tab
            switchTab('render');
            
            // Create form data
            const formData = new FormData(document.getElementById('pdfConfig'));
            formData.append('html_content', htmlContent);

            // Make AJAX request to generate PDF
            fetch('generate_pdf.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('PDF generation failed');
                }
                return response.blob();
            })
            .then(blob => {
                // Store the PDF blob for later use
                currentPdfBlob = blob;
                
                // Create object URL for the PDF
                if (currentPdfUrl) {
                    URL.revokeObjectURL(currentPdfUrl);
                }
                currentPdfUrl = URL.createObjectURL(blob);
                
                // Update render result
                document.getElementById('pdfPreviewContainer').innerHTML = `
                    <div class="text-center py-8">
                        <div class="inline-block p-4 bg-green-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">PDF Generated Successfully!</h3>
                        <p class="text-sm text-gray-500 mb-4">Your document has been processed and is ready for viewing.</p>
                        <div class="bg-gray-50 rounded-lg p-4 text-left">
                            <h4 class="font-medium text-gray-900 mb-2">Document Details:</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>• Paper Size: ${document.querySelector('[name="paper_size"]').value}</p>
                                <p>• Orientation: ${document.querySelector('[name="orientation"]:checked').value}</p>
                                <p>• DPI: ${document.querySelector('[name="dpi"]').value}</p>
                                <p>• Content Length: ${htmlContent.length.toLocaleString()} characters</p>
                                <p>• File Size: ${(blob.size / 1024).toFixed(1)} KB</p>
                            </div>
                        </div>
                        <button onclick="switchTab('generated')" class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Generated PDF
                        </button>
                    </div>
                `;
                
                // Add Generated PDF tab if it doesn't exist
                addGeneratedPDFTab();
                
                // Try to display PDF in iframe
                const pdfViewer = document.getElementById('pdfViewer');
                const pdfFallback = document.getElementById('pdfFallback');
                
                // Create a data URL for the PDF
                const reader = new FileReader();
                reader.onload = function(e) {
                    try {
                        pdfViewer.src = e.target.result;
                        pdfViewer.style.display = 'block';
                        pdfFallback.style.display = 'none';
                    } catch (error) {
                        console.log('PDF iframe display failed, showing fallback');
                        pdfViewer.style.display = 'none';
                        pdfFallback.style.display = 'flex';
                    }
                };
                reader.readAsDataURL(blob);
                
                // Update generated info
                document.getElementById('generatedInfo').textContent = `${(blob.size / 1024).toFixed(1)} KB • Ready for download`;
                
                document.getElementById('loadingModal').classList.add('hidden');
            })
            .catch(error => {
                console.error('Error generating PDF:', error);
                document.getElementById('pdfPreviewContainer').innerHTML = `
                    <div class="text-center py-8">
                        <div class="inline-block p-4 bg-red-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">PDF Generation Failed</h3>
                        <p class="text-sm text-gray-500 mb-4">There was an error processing your document. Please check your HTML content and try again.</p>
                    </div>
                `;
                document.getElementById('loadingModal').classList.add('hidden');
            });
        }

        // Download PDF
        function downloadPDF() {
            if (!currentPdfBlob) {
                alert('Please generate a PDF first.');
                return;
            }

            // Create download link
            const link = document.createElement('a');
            link.href = currentPdfUrl;
            link.download = `document_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.pdf`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Refresh preview
        function refreshPreview() {
            generatePDF();
        }

        // Initialize
        updateOrientationButtons();
        updatePDFInfo();
    </script>
</body>
</html>