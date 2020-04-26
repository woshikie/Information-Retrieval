<?php
    if (!isset($_POST["search"])) {
        header("Location: index.php");
        return;
    }

    $search = $_POST["search"];

    $pageString = isset($_POST["page"]) ? $_POST["page"] : "0";

    $categories = array(
        "general",
        "business",
        "environment",
        "politics",
        "science",
        "tech",
        "positive",
        "negative"
    );

    $filters = array();

    if (isset($_POST["page"])) {
        foreach ($categories as $category) array_push($filters, isset($_POST[$category]));
    } else {
        foreach ($categories as $category) array_push($filters, true);
    }
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
            <input name = "page" type = "hidden" value = "0" />

            <?php
                for ($index = 0; $index < count($categories); $index++) {
                    $filter = $filters[$index];
                    $category = $categories[$index];
                    if ($filter) echo "<input name = $category type = 'hidden' value = 'true' />";
                }
            ?>
        </form>

        <div class = "articles_container">
            <?php
                ini_set("allow_url_fopen", 1);
                $page = (int) $pageString;
                $rows = 10.0;

                $searchTerms = explode(" ", $search);
                $searchString = "";

                foreach ($searchTerms as $term) {
                    if ($searchString !== "") $searchString .= " ";
                    $searchString .= "title:" . $term;
                }

                $targetUrl = "http://103.195.5.203:8983/solr/CovidWatchML/select?q=" . urlencode($searchString) . "&rows=10000";
                $response = file_get_contents($targetUrl);
                $decoded = json_decode($response);
                $docs = $decoded->response->docs;
                $queryTime = $decoded->responseHeader->QTime;
                $articlesFiltered = array();

                foreach ($docs as $doc) {
                    $add = true;
                    $category = isset($doc->predicted_category) ? $doc->predicted_category[0] : "general";
                    $category = strtolower($category);
                    $posneg = isset($doc->posneg) ? $doc->posneg[0] : -1;

                    for ($index1 = 0; $index1 < count($categories); $index1++) {
                        $filter = $filters[$index1];
                        $category1 = $categories[$index1];

                        if (!$filter && $category1 === $category) {
                            $add = false;
                            break;
                        } else if (!$filter && $category1 === "positive" && $posneg == 1) {
                            $add = false;
                            break;
                        } else if (!$filter && $category1 === "negative" && $posneg == 0) {
                            $add = false;
                            break;
                        }
                    }

                    if ($add) array_push($articlesFiltered, $doc);
                }

                $count = count($articlesFiltered);
                $pages = ceil($count / $rows);
                $start = $rows * $page;
                $articles = array();

                for ($index = $start; $index < $start + $rows; $index++) {
                    if ($index >= $count) break;
                    array_push($articles, $articlesFiltered[$index]);
                }

                echo "<p class='info_label'>Found " . $count . " result(s). (" . $queryTime . " milliseconds)</p><br/>";

                foreach ($articles as $article) {
                    if (!isset($article->title) || !isset($article->url)) continue;
                    $title = $article->title[0];
                    $url = $article->url[0];
                    $source = isset($article->source) ? $article->source[0] : "";
                    $sourceSplit = explode(":", $source);
                    $source = $sourceSplit[count($sourceSplit) - 1];
                    $source = trim($source, " '}");
                    $description = isset($article->description) ? $article->description[0] : "";
                    $category = isset($article->predicted_category) ? $article->predicted_category[0] : "";
                    $category = strtolower($category);

                    $posneg = isset($article->posneg) ? $article->posneg[0] : -1;
                    $timeIntervalDisplay = "";

                    if (isset($article->publishedAt)) {
                        $publishedAt = strtotime($article->publishedAt[0]);
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

                        $timeIntervalDisplay = $publishDT->format("d M Y");
                    }

                    echo "<div class = 'article_row_container'>";
                    echo "<div class = 'article_image_container'>";
                    if (isset($article->urlToImage)) {
                        $imageUrl = $article->urlToImage[0];
                        echo "<img src = '$imageUrl' alt = '' class = 'article_image' />";
                    }
                    echo "</div>";

                    echo "<div class = 'article_content_container'>";
                    echo "<p class = 'article_title'><a href='$url'>$title</a></p>";
                    if ($posneg == 1) echo "<p class = 'pos_label'>Positive News</p>";
                    else if ($posneg == 0) echo "<p class = 'neg_label'>Negative News</p>";
                    echo "<p class = 'info_label'>$source - $timeIntervalDisplay</p>";
                    echo "<p>$description</p>";
                    echo "</div>";
                    echo "</div>";
                }

                echo "<div class = 'article_page_container'>";
                echo "<form id = 'page_form' method = 'post'>";
                echo "<input name = 'page' type = 'hidden' value = '0' />";
                echo "<div class = 'row'>";

                for ($index = 0; $index < $pages; $index++) {
                    echo "<div class = 'col-sm-1'>";
                    if ($index == $page) echo "<label>" . ($index + 1) . "</label>";
                    else echo "<label class = 'button_label' onclick = 'pageClick(" . $index . ")'>" . ($index + 1) . "</label>";
                    echo "</div>";
                }

                echo "</div>";

                for ($index = 0; $index < count($categories); $index++) {
                    $filter = $filters[$index];
                    $category = $categories[$index];
                    if ($filter) echo "<input name = $category type = 'hidden' value = 'true' />";
                }

                echo "<input id = 'page_input' name = 'page' type = 'hidden' value = '<?php echo $pageString ?>' />";
                echo "<input name = 'search' type = 'hidden' value = '"; echo $search; echo "' />";
                echo "</form>";
                echo "</div>";
            ?>
        </div>

        <div class = "filter_container">
            <form method = "post">
                <fieldset>
                    <legend>Filters</legend>

                    <?php
                        for ($index = 0; $index < count($categories); $index++) {
                            $filter = $filters[$index];
                            $category = $categories[$index];

                            echo "<div class = 'row'>";
                            echo "<div class = 'col-sm-7'>";

                            if ($category === "positive" || $category === "negative") echo "<label for = 'filter$index'>" . ucfirst($category) . " News</label>";
                            else echo "<label for = 'filter$index'>" . ucfirst($category) . "</label>";
                            echo "</div>";

                            echo "<div class = 'col-sm-3'>";
                            echo "<input id = 'filter$index' type = 'checkbox' name = $category value = 'true' "; if ($filter) echo "checked"; echo " />";
                            echo "</div>";
                            echo "</div>";
                        }
                    ?>

                    <div class = "row">
                        <div class="col-sm-9">
                            <button class = "search_form_button" id="query_filter_button">Apply Filters</button>
                        </div>
                    </div>

                    <input name = "page" type = "hidden" value = "0" />
                    <input name = "search" type = "hidden" value = "<?php echo "$search" ?>" />
                </fieldset>
            </form>
        </div>

        <script>
            function pageClick(index) {
                document.getElementById("page_input").value = index.toString();
                document.getElementById("page_form").submit();
            }
        </script>
    </body>
</html>
