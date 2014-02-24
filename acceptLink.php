<?php

include __DIR__.'/../../../wp-load.php';

include __DIR__."/DateCalculator.php";

$dateCalculator = new DateCalculator(__DIR__ . "/suggest.config");
$nextDate = $dateCalculator->getNextDate();

$salt = $dateCalculator->getSalt();

$postData = $_GET["post"];

if( (md5($salt . $postData['url']) != $postData['key'] )) {
  die("Wrong key.");
}

$post = array( "post_title" => $postData["title"],
               "post_status" => "publish",
               "post_date" => $nextDate,
               "post_author" => 1,
               "post_category" => array((int)$postData["category"]),
               "post_type" => "post" );

$postId = wp_insert_post($post, $wp_error);

add_post_meta($postId, 'url', urldecode($postData['url']));
add_post_meta($postId, 'submitter', $postData['submitter']);

set_post_format($postId, 'link');

$dateCalculator->setLastLinkDate($nextDate);

echo "Link wurde eingetragen und erscheint ".$nextDate.".";