<?php

  // required scripts
  require_once('models/query_Author.php');

  require_once('classes/gitscrape_Filter.php');
  require_once('classes/gitscrape_Core.php');

  $commit_n = ( isset($argv[1]) ? $argv[1] : null );

  if( $commit_n ) {

    $params = $argv;
    array_shift($params);

    $uploader = new Scrape_Core( $params );
    $uploader->run();

  } else {

    Scrape_Core::log( GitScraper::$error, 'Commit number needed...php uploader.php [commit]' );

  }
?>
