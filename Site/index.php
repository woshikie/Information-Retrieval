<!DOCTYPE html>
<html lang = "en">
    <head>
        <meta charset = "UTF-8">
        <title>COVID-19 Watch</title>
        <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/mini.css/3.0.1/mini-default.min.css">
        <link rel = "stylesheet" type = "text/css" href = "style.css">
    </head>

    <body>
        <div id = "index_body">
            <img src = "resources/front_page_icon.png" alt = "" id = "index_image"/>

            <form class = "search_form" action = "query.php" method = "post">
                <label for = "search_form_input"></label>
                <input id = "search_form_input" class = "search_form_input" name = "search" type = "text" placeholder = "Enter search term...">
                <button class = "search_form_button"><img class = "search_form_button_icon" src = "resources/icon_search.png" alt = ""/>Search</button>
            </form>
        </div>

        <div id = "index_articles">
            <div id = "index_positive_news">
                <h5>Uplifting news at a glance:</h5>

                <?php
                    ini_set("allow_url_fopen", 1);
                    $targetUrl = "http://103.195.5.203:8983/solr/CovidWatchML/select?q=title" . urlencode(":") . "*&rows=50";
                    $response = file_get_contents($targetUrl);
                    $decoded = json_decode($response);
                    $docs = $decoded->response->docs;
                    $queryTime = $decoded->responseHeader->QTime;
                    $articles = array();
                    $articles2 = array();

                    foreach ($docs as $doc) {
                        if (count($articles2) >= 5) break;
                        if (!isset($doc->title) || !isset($doc->url)) continue;
                        $posneg = isset($doc->posneg) ? $doc->posneg[0] : -1;

                        if ($posneg == 1) {
                            if (count($articles) < 5) array_push($articles, $doc);
                            else array_push($articles2, $doc);
                        }
                    }

                    echo "<div class = 'row'>";

                    foreach ($articles as $article) {
                        $title = $article->title[0];
                        $url = $article->url[0];
                        $source = isset($article->source) ? $article->source[0] : "";
                        $sourceSplit = explode(":", $source);
                        $source = $sourceSplit[count($sourceSplit) - 1];
                        $source = trim($source, " '}");
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

                        echo "<div class = 'col-sm'>";
                        echo "<p class = 'article_title'><a href='$url'>$title</a></p>";
                        echo "<p class = 'info_label'>$source - $timeIntervalDisplay</p>";
                        echo "</div>";
                    }

                    echo "</div>";

                    echo "</div>";
                ?>
            </div>

            <div id = "index_negative_news">
                <h5>Depressing news at a glance:</h5>

                <?php
                    ini_set("allow_url_fopen", 1);
                    $targetUrl = "http://103.195.5.203:8983/solr/CovidWatchML/select?q=title" . urlencode(":") . "*&rows=50";
                    $response = file_get_contents($targetUrl);
                    $decoded = json_decode($response);
                    $docs = $decoded->response->docs;
                    $queryTime = $decoded->responseHeader->QTime;
                    $articles = array();
                    $articles2 = array();

                    foreach ($docs as $doc) {
                        if (count($articles2) >= 5) break;
                        if (!isset($doc->title) || !isset($doc->url)) continue;
                        $posneg = isset($doc->posneg) ? $doc->posneg[0] : -1;

                        if ($posneg == 0) {
                            if (count($articles) < 5) array_push($articles, $doc);
                            else array_push($articles2, $doc);
                        }
                    }

                    echo "<div class = 'row'>";

                    foreach ($articles as $article) {
                        $title = $article->title[0];
                        $url = $article->url[0];
                        $source = isset($article->source) ? $article->source[0] : "";
                        $sourceSplit = explode(":", $source);
                        $source = $sourceSplit[count($sourceSplit) - 1];
                        $source = trim($source, " '}");
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

                        echo "<div class = 'col-sm'>";
                        echo "<p class = 'article_title'><a href='$url'>$title</a></p>";
                        echo "<p class = 'info_label'>$source - $timeIntervalDisplay</p>";
                        echo "</div>";
                    }

                    echo "</div>";
                    echo "</div>";
                ?>
            </div>
        </div>
    </body>
</html>