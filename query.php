<?php
    if (!isset($_POST["search"])) {
        header("Location: index.php");
        return;
    }

    $search = $_POST["search"];
    $filterA = isset($_POST["filterA"]);
    $filterB = isset($_POST["filterB"]);
    $filterC = isset($_POST["filterC"]);
    $filterD = isset($_POST["filterD"]);
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset = "UTF-8">
        <title><?php echo "$search" ?> - COVID-19 Watch</title>
        <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/mini.css/3.0.1/mini-default.min.css">
        <link rel = "stylesheet" type = "text/css" href = "style.css">
    </head>

    <body>
        <form class = "search_form" method = "post">
            <a href = "index.php"><img src = "resources/front_page_icon.png" alt = "" id = "query_image"/></a>
            <label for = "search_form_input"></label>
            <input id = "search_form_input" class = "search_form_input" name = "search" type = "text" value = "<?php echo "$search" ?>" placeholder = "Enter search terms..." />
            <button class = "search_form_button"><img class = "search_form_button_icon" src = "resources/icon_search.png" alt = ""/>Search</button>
        </form>

        <div class = "articles_container">
            <?php
                ini_set("allow_url_fopen", 1);
                $searchEncoded = urlencode("\"" . $_POST['search'] . "\"");
                $targetUrl = "http://103.195.5.203:8983/solr/CovidWatch/select?q=title%3A" . $searchEncoded . "&rows=50";

                $response = file_get_contents($targetUrl);
                $decoded = json_decode($response);
                $docs = $decoded->response->docs;
                $count = count($docs);
                $queryTime = $decoded->responseHeader->QTime;

                echo "<p class='info_label'>Found " . $count . " results (" . $queryTime . " milliseconds)</p><br/>";

                if ($count) {
                    $skipped = 0;

                    foreach ($docs as $doc) {
                        if (!isset($doc->{'source.name'}) || !isset($doc->title) || !isset($doc->description) || !isset($doc->url) || !isset($doc->publishedAt)) {
                            $skipped++;
                            continue;
                        }

                        $source = $doc->{'source.name'}[0];
                        $title = $doc->title[0];
                        $description = $doc->description[0];
                        $url = $doc->url[0];
                        $publishedAt = strtotime($doc->publishedAt[0]);
                        $timestampNow = strtotime("now");

                        $publishDT = new DateTime();
                        $publishDT->setTimestamp($publishedAt);

                        $nowDT = new DateTime();
                        $nowDT->setTimestamp($timestampNow);
                        $timeInterval = $nowDT->diff($publishDT);

                        if ($timeInterval->d > 7) {
                            $timeIntervalDisplay = $publishDT->format("d M Y");
                        } else if ($timeInterval->d > 0) {
                            $suffix = $timeInterval->d == 1 ? "day" : "days";
                            $timeIntervalDisplay = $timeInterval->format("%d " . $suffix . " ago");
                        } else if ($timeInterval->h > 0) {
                            $suffix = $timeInterval->h == 1 ? "hour" : "hours";
                            $timeIntervalDisplay = $timeInterval->format("%h " . $suffix . " ago");
                        } else if ($timeInterval->i > 0) {
                            $suffix = $timeInterval->i == 1 ? "minute" : "minutes";
                            $timeIntervalDisplay = $timeInterval->format("%i " . $suffix . " ago");
                        } else if ($timeInterval->s > 0) {
                            $suffix = $timeInterval->s == 1 ? "second" : "seconds";
                            $timeIntervalDisplay = $timeInterval->format("%s " . $suffix . " ago");
                        }

                        echo "<div class = 'article_row_container'>";
                            echo "<div class = 'article_image_container'>";
                            if (isset($doc->urlToImage)) {
                                $imageUrl = $doc->urlToImage[0];
                                echo "<img src = '$imageUrl' alt = '' class = 'article_image' />";
                            }
                            echo "</div>";

                            echo "<div class = 'article_content_container'>";
                                echo "<p class = 'article_title'><a href='$url'>$title</a></p>";
                                echo "<p class = 'info_label'>$source - $timeIntervalDisplay</p>";
                                echo "<p>$description</p>";
                            echo "</div>";
                        echo "</div>";
                    }
                }
            ?>
        </div>

        <div class = "filter_container">
            <form method = "post">
                <fieldset>
                    <legend>Filters</legend>

                    <div class = "row">
                        <div class="col-sm-5">
                            <label for = "filter_filterA">Filter A</label>
                        </div>

                        <div class="col-sm-3">
                            <input id = "filter_filterA" type = "checkbox" name = "filterA" value = "true" <?php if ($filterA) echo "checked" ?> />
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-sm-5">
                            <label for = "filter_filterB">Filter B</label>
                        </div>

                        <div class="col-sm-3">
                            <input id = "filter_filterB" type = "checkbox" name = "filterB" value = "true" <?php if ($filterB) echo "checked" ?> />
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-sm-5">
                            <label for = "filter_filterC">Filter C</label>
                        </div>

                        <div class="col-sm-3">
                            <input id = "filter_filterC" type = "checkbox" name = "filterC" value = "true" <?php if ($filterC) echo "checked" ?> />
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-sm-5">
                            <label for = "filter_filterD">Filter D</label>
                        </div>

                        <div class="col-sm-3">
                            <input id = "filter_filterD" type = "checkbox" name = "filterD" value = "true" <?php if ($filterD) echo "checked" ?> />
                        </div>
                    </div>

                    <div class = "row">
                        <div class="col-sm-8">
                            <button class = "search_form_button" id="query_filter_button">Apply Filters</button>
                        </div>
                    </div>

                    <input name = "search" type = "hidden" value = "<?php echo "$search" ?>" />
                </fieldset>
            </form>
        </div>
    </body>
</html>
