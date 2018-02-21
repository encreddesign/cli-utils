<?php

  // required scripts
  require_once('models/query_Author.php');

  require_once('classes/gitscrape_Filter.php');
  require_once('classes/gitscrape_Core.php');

  $commit_n = ( isset($argv[1]) ? $argv[1] : null );

  /**
  * make sure we commit number(s) passed to our script
  * if report error to console
  */
  if( $commit_n )
  {
    $script_args = $argv;

    // shift first 2 args, not classed as script args
    array_shift($script_args);
    array_shift($script_args);

    $uploader = new Scrape_Core( $commit_n, $script_args );
    $uploader->run();
  }
  else {
    Scrape_Core::log( Scrape_Core::$error, "Commit number needed...php uploader.php [commit(s)]" );
  }
?>
