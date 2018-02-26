<?php

  /**
  * Class Cli_Helper
  */

  class Cli_Helper
  {

    /**
    * @var flag_char
    */
    private static $flag_chars = '--';

    /**
    * @var flag_comparer
    */
    private static $flag_comparer = '=';

    /**
    * parse flags passed to script
    * @param cli_args
    */
    public static function get_flags ( $cli_args = [] )
    {
      $query = [];

      if( empty($cli_args) ) return [];

      foreach($cli_args as $arg)
      {
        if( substr($arg, 0, 2) === self::$flag_chars )
        {
          $remove_flag_char = substr($arg, 2, strlen($arg));
          $query = self::get_key_values($remove_flag_char);
        }
        else {
          throw new Exception('Invalid flag parameters');
        }
      }

      return $query;
    }

    /**
    * get flag key and value
    * @param arg
    */
    private static function get_key_values ( $arg )
    {
      $return = [];
      $split_flag = explode('=', $arg);

      if( count($split_flag) === 1 ) return [];

      $return['type'] = $split_flag[0];

      if( $split_flag[0] === 'author' )
      {
        $return['key'] = 'name';
        $return['value'] = $split_flag[1];
      }

      return $return;
    }
  }
?>
