<?php
/**
 * Whiteboard Partial
 */
?>
<div class="whiteboard-container">
    <!-- Whiteboard Tools -->
    <div class="whiteboard-tools mb-3">
        <div class="btn-group me-2">
            <button type="button" id="penTool" class="btn btn-outline-primary active">
                <i class="fas fa-pen"></i> ปากกา
            </button>
            <button type="button" id="eraserTool" class="btn btn-outline-primary">
                <i class="fas fa-eraser"></i> ยางลบ
            </button>
        </div>

        <div class="d-flex align-items-center me-2">
            <label class="me-2">สี:</label>
            <input type="color" id="colorPicker" class="form-control form-control-color" value="#000000">
        </div>

        <div class="d-flex align-items-center me-2">
            <label class="me-2">ขนาด:</label>
            <select id="brushSize" class="form-select" style="width: auto;">
                <option value="1">1px</option>
                <option value="3">3px</option>
                <option value="5" selected>5px</option>
                <option value="8">8px</option>
                <option value="10">10px</option>
                <option value="15">15px</option>
            </select>
        </div>

        <button type="button" id="undoBtn" class="btn btn-outline-secondary me-2" disabled>
            <i class="fas fa-undo"></i> ย้อนกลับ
        </button>

        <button type="button" id="clearCanvas" class="btn btn-outline-danger">
            <i class="fas fa-trash"></i> ล้างกระดาน
        </button>
    </div>

    <!-- Canvas Container -->
    <div class="canvas-container">
        <canvas id="whiteboardCanvas"></canvas>
    </div>
</div>

<style>
.whiteboard-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
}

.canvas-container {
    position: relative;
    width: 100%;
    height: 400px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
}

#whiteboardCanvas {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: crosshair;
    touch-action: none;
}

#colorPicker {
    width: 40px;
    padding: 0;
    height: 31px;
}

.whiteboard-tools {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    align-items: center;
}

@media (max-width: 768px) {
    .whiteboard-tools {
        justify-content: center;
    }
}
</style>

<script>
let canvas, ctx;
let isDrawing = false;
let lastX = 0;
let lastY = 0;
let drawHistory = [];
let historyStep = -1;

document.addEventListener('DOMContentLoaded', function() {
    initWhiteboard();
});

// Tab change handler
document.querySelector('button[data-bs-target="#whiteboard"]').addEventListener('shown.bs.tab', function () {
    initWhiteboard();
});

function initWhiteboard() {
    canvas = document.getElementById('whiteboardCanvas');
    if (!canvas) return;

    ctx = canvas.getContext('2d');
    resizeCanvas();
    
    // Event listeners
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    canvas.addEventListener('touchstart', handleTouchStart);
    canvas.addEventListener('touchmove', handleTouchMove);
    canvas.addEventListener('touchend', stopDrawing);

    // Tool buttons
    document.getElementById('penTool').addEventListener('click', () => {
        setTool('pen');
    });

    document.getElementById('eraserTool').addEventListener('click', () => {
        setTool('eraser');
    });

    document.getElementById('colorPicker').addEventListener('change', (e) => {
        ctx.strokeStyle = e.target.value;
        setTool('pen');
    });

    document.getElementById('brushSize').addEventListener('change', (e) => {
        ctx.lineWidth = e.target.value;
    });

    document.getElementById('clearCanvas').addEventListener('click', clearCanvas);
    document.getElementById('undoBtn').addEventListener('click', undo);

    // Window resize handler
    window.addEventListener('resize', debounce(resizeCanvas, 250));

    // Initial setup
    setTool('pen');
    clearCanvas();
}

// Initialize all necessary functions and event handlers for the whiteboard
// (Full whiteboard implementation from your previous script)