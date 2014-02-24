<?php

include __DIR__.'/../../../wp-load.php';

include __DIR__."/DateCalculator.php";

$dateCalculator = new DateCalculator(__DIR__ . "/suggest.config");

$salt = $dateCalculator->getSalt();

$postData = $_POST["post"];

$key = md5($salt.$postData['url']);

$link = "http://".$_SERVER["HTTP_HOST"]."/wp-content/plugins/suggest-a-link/acceptLink.php?".
        "post[title]=".$postData['title']."&post[url]=".$postData["url"]."&post[submitter]=".$postData["submitter"].
        "&post[category]=".$postData["category"]."&post[key]=".$key;

$category = get_category((int)$postData['category']);

$text = "Es wurde ein Link vorgeschlagen: \n\n";
$text .= "Titel: " . $postData["title"]."\n";
$text .= "Url: " . urldecode($postData["url"])."\n";
$text .= "Kategorie: " . $category->name."\n";
$text .= "Eintragender: " . $postData["submitter"]."\n\n\n";

$text .= "Zum Freigeben bitten folgenden Link aufrufen: \n";
$text .= $link;

mail($dateCalculator->getModeratorEmail(), "whm: Es wurde ein neuer Link eingetragen.", $text);

echo json_encode("success");
