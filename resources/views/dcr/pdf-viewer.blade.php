<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Viewer - {{ $filename }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf_viewer.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
        }
        
        .toolbar {
            background: #fff;
            border-bottom: 1px solid #ddd;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s;
        }
        
        .btn:hover {
            background: #4338ca;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .page-info {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
        }
        
        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .zoom-btn {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            width: 32px;
            height: 32px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        
        .zoom-btn:hover {
            background: #e5e7eb;
        }
        
        .zoom-level {
            min-width: 50px;
            text-align: center;
            font-size: 14px;
            color: #374151;
        }
        
        #pdf-container {
            height: calc(100vh - 70px);
            overflow: auto;
            background: #525659;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
        }
        
        #pdf-canvas {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            background: white;
        }
        
        .loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 400px;
            color: white;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .error {
            background: #ef4444;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
            text-align: center;
        }
        
        .filename {
            font-weight: 600;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div class="toolbar-left">
            <button onclick="window.close()" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Close
            </button>
            <div class="filename">{{ $filename }}</div>
        </div>
        
        <div class="toolbar-right">
            <div class="zoom-controls">
                <button onclick="zoomOut()" class="zoom-btn" title="Zoom Out">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <span class="zoom-level" id="zoom-level">100%</span>
                <button onclick="zoomIn()" class="zoom-btn" title="Zoom In">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </button>
            </div>
            
            <div class="page-info">
                Page <span id="current-page">1</span> of <span id="total-pages">1</span>
            </div>
            
            <button onclick="previousPage()" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Previous
            </button>
            
            <button onclick="nextPage()" class="btn btn-secondary">
                Next
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            
            <a href="{{ $pdfUrl }}" download="{{ $filename }}" class="btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download
            </a>
        </div>
    </div>
    
    <div id="pdf-container">
        <div class="loading" id="loading">
            <div class="spinner"></div>
            <div>Loading PDF...</div>
        </div>
        <canvas id="pdf-canvas" style="display: none;"></canvas>
        <div class="error" id="error" style="display: none;">
            <h3>Failed to load PDF</h3>
            <p>{{ $error ?? 'The PDF file could not be loaded. Please try downloading the file instead.' }}</p>
        </div>
    </div>

    <script>
        // Set the worker source
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        let pdfDoc = null;
        let currentPage = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        
        // Load the PDF
        const url = '{{ $pdfUrl }}';
        
        /**
         * Get page info from document, and render page
         * @param num Page number
         */
        function renderPage(num) {
            pageRendering = true;
            
            // Using promise to fetch the page
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                
                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                    
                    // Update page info
                    document.getElementById('current-page').textContent = num;
                    document.getElementById('total-pages').textContent = pdfDoc.numPages;
                });
            });
            
            // Update page number
            document.getElementById('current-page').textContent = num;
        }
        
        /**
         * If another page rendering in progress, waits until the rendering is
         * finished. Otherwise, executes rendering immediately.
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }
        
        /**
         * Displays previous page.
         */
        function previousPage() {
            if (currentPage <= 1) {
                return;
            }
            currentPage--;
            queueRenderPage(currentPage);
        }
        
        /**
         * Displays next page.
         */
        function nextPage() {
            if (currentPage >= pdfDoc.numPages) {
                return;
            }
            currentPage++;
            queueRenderPage(currentPage);
        }
        
        /**
         * Zoom in
         */
        function zoomIn() {
            if (scale < 3.0) {
                scale += 0.25;
                updateZoomLevel();
                queueRenderPage(currentPage);
            }
        }
        
        /**
         * Zoom out
         */
        function zoomOut() {
            if (scale > 0.25) {
                scale -= 0.25;
                updateZoomLevel();
                queueRenderPage(currentPage);
            }
        }
        
        /**
         * Update zoom level display
         */
        function updateZoomLevel() {
            document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
        }
        
        /**
         * Handle keyboard shortcuts
         */
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                previousPage();
            } else if (e.key === 'ArrowRight') {
                nextPage();
            } else if (e.key === '+' || e.key === '=') {
                zoomIn();
            } else if (e.key === '-') {
                zoomOut();
            } else if (e.key === 'Escape') {
                window.close();
            }
        });
        
        /**
         * Load PDF document
         */
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('total-pages').textContent = pdfDoc.numPages;
            
            // Initial/first page rendering
            renderPage(currentPage);
            
            // Hide loading and show canvas
            document.getElementById('loading').style.display = 'none';
            canvas.style.display = 'block';
        }).catch(function(error) {
            // Show error message
            console.error('Error loading PDF:', error);
            document.getElementById('loading').style.display = 'none';
            document.getElementById('error').style.display = 'block';
        });
    </script>
</body>
</html>
