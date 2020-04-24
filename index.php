<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset = "UTF-8">
        <title>COVID-19 Watch</title>
        <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/mini.css/3.0.1/mini-default.min.css">
        <link rel = "stylesheet" type = "text/css" href = "style.css">
    </head>

    <body id = "index_body">
        <img src = "resources/front_page_icon.png" alt = "" id = "index_image"/>

        <form class = "search_form" action = "query.php" method = "post">
            <label for = "search_form_input"></label>
            <input id = "search_form_input" class = "search_form_input" name = "search" type = "text" placeholder = "Enter search term...">
            <button class = "search_form_button"><img class = "search_form_button_icon" src = "resources/icon_search.png" alt = ""/>Search</button>
        </form>
    </body>
</html>