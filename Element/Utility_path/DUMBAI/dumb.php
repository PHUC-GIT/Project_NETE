<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}
?>
<html lang="en">
    <!-- Local CSS -->
    <style>
        h1, h4, p {
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        
        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .warn_text {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            cursor: default;
        }

        .outline {
            border: 1px solid #b20000ff; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color: rgba(178, 0, 0, 0.3); 
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .btn_card {
            padding: 7px;
            border: 3px solid #b20000ff;
            background-color: #b20000ff;
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
            color: #b20000ff;
        }

        #view_value_text {
            border: none;
            border-radius: 7px;
            width: 97%;
            height: 65vh;
            max-height: 73vh;
            padding: 10px;
            overflow-y: auto;
            overflow-x: hidden;
            text-wrap: auto;
            background-color: rgba(0, 0, 0, 0.5);
            scrollbar-color: #b20000ff transparent;
            border: 1px solid gray;
            text-align: left;
            font-family: 'Roboto', sans-serif;
            color: white;
            font-size: 15px;
            outline: none;
            resize: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(7px);
            backdrop-filter: blur(7px);
        }

        #userinput {
            flex: 1;
            padding-left: 10px;
            padding-right: 10px;
            border: 3px solid transparent;
            background-color: rgba(178, 0, 0, 0.5);
            border-radius: 5px 0px 0px 5px;
            font-family: 'Roboto', sans-serif;
            color: white;
            font-size: 15px;
            font-weight: bold;
            transition: 0.1s;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        #userinput::placeholder {
            color: #b20000ff;
        }

        .btn_card_send {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid transparent;
            background-color: #b20000ff;
            border-radius: 0px 5px 5px 0px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .btn_card_send:hover {
            background-color: white;
            color: #b20000ff;
            fill: #b20000ff;
        }

        .msg-spirit   { 
            color: #ff4444; 
        }
        .msg-error    { 
            color: #484848; 
        }
        .msg-user     { 
            color: white; 
        }
    </style>
    <div align="center">
        <div class="outline">
            <div>
                <h1 title="DEGRADE UNINTELLIGIBLE MADNESS BRAIN">D.U.M.B AI SYSTEM</h1>
            </div>
            <div>
                <a class="warn_text">Warning: This "AI" is about existential crisis. Please use it at your own risk!</a>
            </div>
            <div>
                <button class="btn_card" onclick="history.back()">Return</button>
            </div>
        </div>
    </div>
        <div class="flex-div" align="center">
            <div id="view_value_text"></div>
            <div style="display:flex; width:97%; margin-top:10px;">
                <input type="text" id="userinput" 
                       placeholder="Ask D.U.M.B AI anything...">
                <button class="btn_card_send" 
                        onclick="askDUMB()">
                        <svg width="32" height="32" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g><path transform="matrix(.74784 0 0 .74784 4.0345 4.0345)" d="m30.469 16-11.348 3.1204-3.1204 11.348-3.1204-11.348-11.348-3.1204 11.348-3.1204 3.1204-11.348 3.1204 11.348z" style="paint-order:stroke fill markers"/><path d="m10.157 12.745 2.584-2.4976-5.8311-3.4474z" style="paint-order:stroke fill markers"/><path d="m19.255 10.157 2.4976 2.584 3.4474-5.8311z" style="paint-order:stroke fill markers"/><path d="m21.843 19.255-2.584 2.4976 5.8311 3.4474z" style="paint-order:stroke fill markers"/><path d="m12.745 21.843-2.4976-2.584-3.4474 5.8311z" style="paint-order:stroke fill markers"/></g></svg>
                </button>
            </div>
        </div>
    <script>
        async function askDUMB() {
        const inputEl  = document.getElementById('userinput');
        const chatbox  = document.getElementById('view_value_text');
        const input    = inputEl.value.trim();
        if (!input) return;

        // Show user message
        chatbox.innerHTML += `<p class="msg-user">YOU: ${escapeHtml(input)}</p>`;
        inputEl.value = '';

        // Fetch
        const formData = new FormData();
        formData.append('input', input);

        try {
            const response = await fetch('./Element/Utility_path/DUMBAI/respond.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();

            // Pick class
            let cssClass = 'msg-spirit';

            if (data.mode === 'error') {
                cssClass = 'msg-error';
            }

            const label = data.label || data.mode.toUpperCase();

            chatbox.innerHTML += `
                <p class="${cssClass}">
                    D.U.M.B [${label}]: ${escapeHtml(data.response)}
                </p>`;

        } catch (err) {
            chatbox.innerHTML += `
                <p class="msg-error">
                    ERROR: Connection failed. The spirit is unavailable.
                </p>`;
        }

        // Auto scroll
        chatbox.scrollTop = chatbox.scrollHeight;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // Enter key
    document.getElementById('userinput')
        .addEventListener('keypress', function(e) {
            if (e.key === 'Enter') askDUMB();
        });
    </script>
</html>