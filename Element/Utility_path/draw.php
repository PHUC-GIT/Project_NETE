<!DOCTYPE html>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}
?>
<html lang="en">
    <style>
        h1, h2, h3, h4, p {
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        
        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .outline {
            border: 1px solid #5bca80ff; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color: rgba(91, 202, 128, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
            z-index: 100;
        }

        .btn_card {
            padding: 7px;
            border: 3px solid #5bca80ff;
            background-color: #5bca80ff;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            transition: 0.1s;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #5bca80ff;
        }

        .file_view {
            margin-top: 15px;
        }

        /* Canvas function below */
        #canvas {
        border-radius: 5px;
        background: #fff;
        cursor: none;
        }

        /* Button Draw */
        .Draw_Button {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #5bca80ff;
            background-color: #5bca80ff;
            border-radius: 5px 0px 0px 5px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .Erase_Button {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #5bca80ff;
            background-color: #5bca80ff;
            border-radius: 0px 5px 5px 0px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .Draw_Button:hover, .Erase_Button:hover {
            background-color: white;
            border: 3px solid white;
            fill: #5bca80ff;
        }

        .Draw_Button.active, .Erase_Button.active {
          transition: none;
          border: 3px solid white;
          background-color: #5bca80ff;
          fill: white;
          cursor: default;
        }

        .Draw_Button.active:hover, .Erase_Button.active:hover {
          background-color: #5bca80ff;
          border: 3px solid white;
          fill: white; 
        }

        /* Button with icons */
        .btn_card_icons {
          padding-left: 10px;
          padding-right: 10px;
          fill: white;
          border: 3px solid #5bca80ff;
          background-color: #5bca80ff;
          border-radius: 5px;
          color: white;
          font-family: 'Roboto', sans-serif;
          font-size: 15px;
          font-weight: bold;
          margin-left: 10px;
          transition: 0.1s;
          display: flex;
          align-items: center;
          gap: 5px;
          cursor: pointer;
        }

        .btn_card_icons:hover {
          background-color: white;
          border: 3px solid white;
          color: #5bca80ff;
          fill: #5bca80ff;
        }

        /* Color picker styling */
        input[type="color"] {
          width: 40px;
          height: 40px;
          border: none;
          appearance: none;
          background: none;
          cursor: pointer;
          margin-right: 10px;
        }

        /* Universal Styles for the entire color input box */
        input[type="color"] {
          -webkit-appearance: none; /* Strip Webkit default styling */
          -moz-appearance: none;    /* Strip Firefox default styling */
          appearance: none;         /* Standard property */

          border: none;             /* Remove default border */
          width: 32px;              /* Set a fixed size */
          height: 32px;             /* Set a fixed size */
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
          padding: 0; 
        }

        /* Brush size slider spacing */
        input[type="range"] {
          margin-right: 10px;
          width: 200px;
          accent-color: #5bca80ff;
        }
    </style>
    <div>
        <div class="outline">
            <div>
                <h1>TINY ART</h1>
            </div>
            <div style="display: flex; flex-direction: row; align-items: center;">
                <button title="Clear your canvas" id="clearButton" class="btn_card_icons" style="margin-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M6 7H5v13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7H6zm10.618-3L15 2H9L7.382 4H3v2h18V4z"/></svg>Reset</button>
                <input id="color" type="color" value="#000000"/>
                <input title="brush size" id="brush" type="range" min="1" max="20" value="4"/>
                <button title="Draw Mode" id="toggle_draw" class="Draw_Button active" disabled><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M8.707 19.707 18 10.414 13.586 6l-9.293 9.293a1.003 1.003 0 0 0-.263.464L3 21l5.242-1.03c.176-.044.337-.135.465-.263zM21 7.414a2 2 0 0 0 0-2.828L19.414 3a2 2 0 0 0-2.828 0L15 4.586 19.414 9 21 7.414z"/></svg></button> <!-- Draw icons-->
                <button title="Eraser Mode" id="toggle_eraser" class="Erase_Button" style="margin-right: 10px;"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M12.48 3 7.73 7.75 3 12.59a2 2 0 0 0 0 2.82l4.3 4.3A1 1 0 0 0 8 20h12v-2h-7l7.22-7.22a2 2 0 0 0 0-2.83L15.31 3a2 2 0 0 0-2.83 0zM8.41 18l-4-4 4.75-4.84.74-.75 4.95 4.95-4.56 4.56-.07.08z"/></svg></button> <!--Erase icons -->
                <button title="Save and download your art" id="saveButton" class="btn_card_icons"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 8zM4 19h16v2H4z"/></svg>Save & Download</button>
            </div>
            <div>
                <button class="btn_card" onclick="window.location.href='index.php?req=utility';">Return</button>
            </div> 
        </div> 
    </div>
    <div align="center">
        <canvas id="canvas" width="1000" height="500"></canvas>
    </div>
    <script>
        // Get references to canvas and UI elements
        const canvas = document.getElementById("canvas");
        const ctx = canvas.getContext("2d");

        const colorInput = document.getElementById("color");
        colorInput.value = '#000000';
        const brushInput = document.getElementById("brush");
        const eraseButton = document.getElementById("toggle_eraser");
        const drawButton = document.getElementById("toggle_draw")

        const drawing = []; // Stores all drawing strokes

        // Settings object to track current mode and brush state
        const settings = {
          mode: "drawing", // drawing or erase
          brush: 4, // brush size
          color: "#000000", // current color
          mouse: { x: null, y: null }, // current mouse position
          is_drawing: false, // whether mouse is pressed
          previousMouse: null, // last known mouse position for line drawing
        };

        // Update color setting when color input changes
        colorInput.onchange = () => {
          settings.color = colorInput.value;
        };

        // Update brush size when slider moves
        brushInput.oninput = () => {
          settings.brush = parseInt(brushInput.value);
        };

        // Toggle between drawing and erasing
        eraseButton.onclick = () => {
            settings.mode = "erase";
            eraseButton.classList.add("active");
            eraseButton.setAttribute("disabled", "disabled");
            drawButton.removeAttribute("disabled");
            drawButton.classList.remove("active");
        };

        drawButton.onclick = () => {
            settings.mode = "drawing";
            drawButton.classList.add("active");
            drawButton.setAttribute("disabled", "disabled");
            eraseButton.removeAttribute("disabled");
            eraseButton.classList.remove("active");
        };

        // Start drawing on mousedown
        canvas.addEventListener("mousedown", (e) => {
          if (e.button === 0) { // Left mouse button
            settings.is_drawing = true;
            const { x, y } = getMousePos(e);
            settings.previousMouse = { x, y };
            addStroke(x, y);
          }
        });

        // Stop drawing on mouseup
        canvas.addEventListener("mouseup", () => {
          settings.is_drawing = false;
          settings.previousMouse = null;
          drawing.push(null); // Add separator between strokes
        });

        // Draw as the mouse moves
        canvas.addEventListener("mousemove", (e) => {
          const { x, y } = getMousePos(e);
          settings.mouse = { x, y };

          if (settings.is_drawing) {
            if (settings.previousMouse) {
              drawLine(
                settings.previousMouse,
                { x, y },
                settings.brush,
                settings.mode === "erase" ? "#fff" : settings.color
              );
              addStroke(x, y);
            }
            settings.previousMouse = { x, y };
          }
        });

        // Reset drawing state when mouse leaves canvas
        canvas.addEventListener("mouseout", () => {
          settings.mouse = { x: null, y: null };
          if (settings.is_drawing) {
            drawing.push(null);
          }
          settings.is_drawing = false;
          settings.previousMouse = null;
        });

        // Get mouse coordinates relative to canvas
        function getMousePos(event) {
          const rect = canvas.getBoundingClientRect();
          return {
            x: event.clientX - rect.left,
            y: event.clientY - rect.top,
          };
        }

        // Add a new stroke to the drawing history
        function addStroke(x, y) {
          const stroke = {
            x,
            y,
            color: settings.mode === "erase" ? "#fff" : settings.color,
            size: settings.brush,
            mode: settings.mode === "erase",
          };
          drawing.push(stroke);
        }

        // Draw a line segment between two points
        function drawLine(start, end, size, color) {
          ctx.beginPath();
          ctx.strokeStyle = color;
          ctx.lineWidth = size * 2;
          ctx.lineCap = "round";
          ctx.moveTo(start.x, start.y);
          ctx.lineTo(end.x, end.y);
          ctx.stroke();
          ctx.closePath();
        }

        // Draw a dot (circle or square depending on mode)
        function drawDot(x, y, size, color, mode) {
          ctx.beginPath();
          if (!mode) {
            ctx.fillStyle = color;
            ctx.arc(x, y, size, 0, 2 * Math.PI);
            ctx.fill();
          } else {
            ctx.fillStyle = color;
            ctx.fillRect(x - size / 2, y - size / 2, size, size);
          }
          ctx.closePath();
        }

        // Draw a custom cursor on the canvas
        function drawCursor() {
          const { x, y } = settings.mouse;
          const size = settings.brush;
          if (x != null && y != null) {
            if (settings.mode === "drawing") {
              drawDot(x, y, size, settings.color, false);
            } else {
              ctx.beginPath();
              ctx.strokeStyle = "orange";
              ctx.lineWidth=1;
              ctx.arc(x, y, size, 0, 2 * Math.PI, false);
              ctx.stroke();
              ctx.closePath();
            }
          }
        }

        // Clear the entire canvas
        function drawBackground(color) {
            ctx.fillStyle = color;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        // Main render loop to redraw all strokes and cursor
        function update() {
          drawBackground("#fff");

          // Redraw strokes from the drawing history
          for (let i = 1; i < drawing.length; i++) {
            const prev = drawing[i - 1];
            const curr = drawing[i];

            if (!prev || !curr) continue;

            if (prev.mode === curr.mode) {
             drawLine(prev, curr, curr.size, curr.color);
            } else {
              drawDot(curr.x, curr.y, curr.size, curr.color, curr.mode);
            }
          }

          // Draw the custom cursor
          drawCursor();

          // Continue the animation loop
          requestAnimationFrame(update);
        }

        // Start the drawing loop
        update();

        function resetCanvas() {
          ctx.clearRect(0, 0, canvas.width, canvas.height);
          drawing.length = 0;
        }
        document.getElementById('clearButton').addEventListener('click', resetCanvas);

        // Save art
        function saveCanvasAsPNG() {
            const dataURL = canvas.toDataURL('image/png');

            const link = document.createElement('a');

            link.href = dataURL;
            link.download = 'my_art.png';
            document.body.appendChild(link); 
            link.click();
            document.body.removeChild(link);
        }

        document.getElementById('saveButton').addEventListener('click', saveCanvasAsPNG);
    </script>
</html>