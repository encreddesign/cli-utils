<?php

  date_default_timezone_set('Europe/London');

  class Scrape_Core {

    private $cwd;
    private $use_gem;
    private $commit_n;

    private $git = [
      'lg' => 'git lg',
      'show' => 'git show'
    ];

    private $git_ci_files = [];
    private $git_processed_files = [];

    public static $error = "\033[31m";
    public static $warning = "\033[33m";
    public static $success = "\033[32m";

    public function __construct ( $commit_n ) {

      $this->commit_n = $commit_n;
      $this->cwd = dirname( __FILE__ );
    }

    public function run () {

      try {

        $command = $this->args( $this->git['show'], $this->commit_n );
        $shell_out = shell_exec( $command );

        if( $shell_out ) {

          $filter = new Scrape_Filter($shell_out);
          $matches = $filter->query_author('Joshua Grierson', 'name');

          // $commited_files = $this->get_files( $shell_out );
          // if( !empty($commited_files) ) {
          //
          //   foreach($commited_files as $file) {
          //     if( in_array($file, $this->git_processed_files) ) continue;
          //
          //     $file_split = explode('.', $file);
          //     $file_type = ( isset($file_split[1]) ? $file_split[1] : 'file' );
          //
          //     $this->git_ci_files[$file_type][] = $file;
          //     $this->git_processed_files[] = $file;
          //   }
          //
          //   $this->format_log_tree();
          // }
        } else {
          throw new Exception( 'Issue executing git command ['.$command.']' );
        }
      } catch (Exception $ex) {

        self::log( self::$error, $ex->getMessage() );
        exit;
      }
    }

    private function format_log_tree () {
      $file_config = include('gitscrape_config.php');

      foreach($this->git_ci_files as $file_type => $file_array) {
        $type = " \033[34m[{$file_type}]\033[0m ";

        if( empty($file_array) ) {

          self::log( self::$success, "file group{$type} - No files listed in this group" );
          continue;
        }

        if( isset($file_config[$file_type]) ) {

          $type = " \033[34m[{$file_config[$file_type]}]\033[0m ";
          self::log( self::$success, "file group{$type}" );
        } else {

          $type = " \033[34m[Unkown Group}]\033[0m ";
          self::log( self::$success, "file group{$type}" );
        }

        foreach($file_array as $file) {
          self::log( self::$success, "--- \033[0m{$file}" );
        }
      }
    }

    private function args ( $cmd, $args = [] ) {

      if( !empty($args) ) {

        return $cmd.' '.join( ' ', $args );
      } else {
        return $cmd;
      }
    }

    public function get_ci_files () {
      return $this->git_ci_files;
    }

    public static function log ( $type, $message ) {

      if( $message ) {
        echo "[\033[33m".date( 'H:i:s', time() )."\033[0m] {$type}{$message} \033[0m ".PHP_EOL;
      }
    }

  }

?>
