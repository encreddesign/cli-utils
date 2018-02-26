<?php

  /**
  * Class Scrape_Filter
  */

  class Scrape_Filter
  {

    /**
    * @var shell_out
    */
    private $shell_out;

    /**
    * @var author_info
    */
    private $author_info = [];

    /**
    * @var track_idx
    */
    private $track_idx = 0;

    /**
    * Filter construct
    * @param shell_output
    */
    public function __construct ( $shell_output )
    {
      $this->shell_out = $shell_output;
    }

    /**
    * queries shell output for authors, and builds array with names and commits
    * @param author_value
    * @param type
    *
    * @return Array
    */
    public function query_author ( $author_value, $type = 'name' )
    {
      $return = [];
      $authors = [];

      $lines = explode( PHP_EOL, $this->shell_out );

      if( !empty($lines) )
      {
        $current_commit = 0;

        foreach($lines as $line)
        {

          if( substr($line, 0, 6) === 'commit' )
          {

            $current_commit = $line;
            $authors[$current_commit] = null;
          }

          if( substr($line, 0, 7) === 'Author:' ) $authors[$current_commit]['author'] = $line;
        }
      }

      if( !empty($authors) )
      {
        $return = $this->build_author_array($author_value, $authors, $type);
      }

      return $return;
    }

    /**
    * builds author array from scraped log
    * @param authors
    *
    * @return Array
    */
    private function build_author_array ( $author_value, $authors = [], $type )
    {
      $return = [];

      foreach($authors as $key_commit => $author)
      {
        $author_arr = Query_Author::author_arr($author['author']);
        $author_commit = Query_Author::author_commit($key_commit);

        if( isset($author_arr[$type]) && $author_arr[$type] === $author_value )
        {

          $return[$this->track_idx]['commit'] = $author_commit;
          $return[$this->track_idx]['author'] = $author_arr;

          $this->track_idx++;
        }
      }

      return $return;
    }
  }
?>
