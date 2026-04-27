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
        h1, h4, p {
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        
        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .h1_font {
            font-size: 55px;
        }

        .outline {
            border: 1px solid #01df95; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(1, 223, 149, 0.3); 
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
            border: 3px solid #01df95;
            background-color: #01df95;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #01df95;
        }

        #browser_search {
            padding: 15px;
            width: 750px;
            border: 3px solid gray;
            background-color:rgba(37, 37, 37, 0.7);
            border-radius: 0px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }
        
        .search_btn {
            padding: 15px;
            border: 3px solid gray;
            background-color: rgba(37, 37, 37, 0.7);
            border-radius: 0px 20px 20px 0px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        .search_btn:hover {
            background-color: white;
            color: #060606;
        }

        .css_sort {
            border: 3px solid gray;
            padding: 15px;
            background: rgba(37, 37, 37, 0.7);
            border-radius: 20px 0px 0px 20px;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
            color: white;
            margin-left: 10px;
            cursor: pointer;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        option {
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
        }

        .css_sort:hover, .css_sort:focus{
            background-color: white;
            color: #060606;
        }
    </style>
    <div>
        <div class="outline">
            <div>
                <h1>INTERNET GATEWAY</h1>
            </div>
            <div>
                <button class="btn_card" onclick="history.back()">Return</button>
            </div>
        </div>
    </div>
        <div class="flex-div" align="center">
            <form name="formbrowser" id="formbrowser" style="display:inline;" onsubmit="return openMinimalWindow(event);">
                <input type="hidden" name="req" value="net">
                <h1 class="h1_font" style="margin-bottom: 70px; margin-top: 40px;">GATEWAY</h1>
                <select name="which_browser" class="css_sort dropdown_control">
                    <option value="green">Ecosia</option>
                    <option value="Duck">DuckDuckGo</option>
                    <option value="dontbe">Youtube</option>
                    <option value="MS">Bing</option>
                    <option value="hoho">Yahoo!</option>
                    <option value="Data_Mine">Google</option>
                </select>
                <input id="browser_search"  type="text" id="internet_search" name="search_content"/>
                <button class="search_btn">Search!</button>
            </form>
        </div>
        <script>
            function openMinimalWindow(event) {
                event.preventDefault();
                const form = document.getElementById('formbrowser');
                const search_content = encodeURIComponent(form.search_content.value.trim());
                const which_browser = form.which_browser.value;
                let url = '';
                if (!search_content) return false;

                switch (which_browser) {
                    case 'green':
                        url = 'https://www.ecosia.org/search?method=index&q=' + search_content;
                        break;
                    case 'Duck':
                        url = 'https://duckduckgo.com/?t=h_&q=' + search_content + '&ia=web';
                        break;
                    case 'dontbe':
                        url = 'https://www.youtube.com/results?search_query=' + search_content;
                        break;
                    case 'MS':
                        url = 'https://www.bing.com/search?q=' + search_content + '&lq=0&pq&ghsh=0&ghacc=0&ghpl';
                        break;
                    case 'hoho':
                        url = 'https://search.yahoo.com/search?p=' + search_content + '&fr=yfp-t&fr2=p%3Afp%2Cm%3Asb&ei=UTF-8&fp=1';
                        break;
                    case 'Data_Mine':
                        url = 'https://www.google.com/search?q=' + search_content;
                        break;
                }
                // Open in a minimal window (width: 600px, height: 500px)
                window.open(url, '_blank', 'width=600,height=500,menubar=no,toolbar=no,location=no,status=no');
                return false;
            }
        </script>
</html>