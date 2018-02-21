<?php

  /**
  * Class Query_Author
  */

  class Query_Author
  {

    /**
    * builds array of authors
    * @param author_line
    *
    * @return Array
    */
    public static function author_arr ($author_line)
    {
      $info = [];
      preg_match( '/Author:\s+(\w+\s+\w+|\w+)\s+\<(.*?)\>/i', $author_line, $match );

      if( !empty($match) && isset($match[1]) )
      {

        $info['name'] = $match[1];
        if( isset($match[2]) ) $info['email'] = $match[2];
      }

      return $info;
    }

    /**
    * builds array of commits belonging to an author
    * @param author_line
    *
    * @return Array
    */
    public static function author_commit ($author_line)
    {
      $commit_number = null;
      preg_match( '/commit\s+(.*)/i', $author_line, $match );

      if( !empty($match) && isset($match[1]) )
      {
        $commit_number = $match[1];
      }

      return $commit_number;
    }
  }

?>
