<?php

  date_default_timezone_set('Europe/London');

  class Scrape_Core {

    private $cwd;

    private $commit_n;
    private $script_args;

    private $git = [
      'lg' => 'git lg',
      'show' => 'git show'
    ];

    private $git_ci_files = [];
    private $git_processed_files = [];

    public static $error = "\033[31m";
    public static $warning = "\033[33m";
    public static $success = "\033[32m";

    public function __construct ( $commit_n, $script_args = [] ) {

      $this->commit_n = $commit_n;
      $this->script_args = $script_args;

      $this->cwd = dirname( __FILE__ );
    }

    public function run () {

      try {

        $command = $this->args( $this->git['show'], $this->commit_n );
        $shell_out = shell_exec( $command );

        if( $shell_out ) {

          $filtered_commits = $this->filter_commits($shell_out, $this->cli_args);

          foreach($filtered_commits as $commit) {
            $command = $this->args( $this->git['show'], array($commit['commit']) );
            $shell_out = shell_exec( $command );

            if( $shell_out ) $this->format_output($shell_out);
          }
        } else {
          throw new Exception( 'Issue executing git command ['.$command.']' );
        }
      } catch (Exception $ex) {

        self::log( self::$error, $ex->getMessage() );
        exit;
      }
    }

    private function get_files ( $shell_out ) {
      $files = [];
      $return = [];

      $lines = explode( PHP_EOL, $shell_out );

      if( !empty($lines) ) {

        foreach( $lines as $line ) {
          if( substr($line, 0, 3) === '+++' ) $files[] = $line;
        }
      }

      if( !empty($files) ) {

        foreach( $files as $file ) {

          preg_match( '/[\s*]b\/(.*)/i', $file, $match );
          if( !empty($match) && $match[1] ) $return[] = $match[1];
        }
      }

      return $return;
    }

    private function format_output ($shell_out) {
      $commited_files = $this->get_files( $shell_out );
      if( !empty($commited_files) ) {

        foreach($commited_files as $file) {
          if( in_array($file, $this->git_processed_files) ) continue;

          $file_split = explode('.', $file);
          $file_type = ( isset($file_split[1]) ? $file_split[1] : 'file' );

          $this->git_ci_files[$file_type][] = $file;
          $this->git_processed_files[] = $file;
        }

        $this->format_log_tree();
      }
    }

    private function filter_commits ($shell_out, $query = []) {
      $filter = new Scrape_Filter($shell_out);

      if( isset($query['type']) && $query['type'] === 'author' ) {
        $matched_author = $filter->query_author($query['value'], $query['key']);

        if( empty($matched_author) ) {
          throw new Exception("No commits found by author {$query['author']['value']}");
        }

        return $matched_author;
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

          $type = " \033[34m[Unkown Group]\033[0m ";
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
