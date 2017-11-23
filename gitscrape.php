<?php

  date_default_timezone_set('Europe/London');

  class Uploader {

    private $cwd;
    private $use_gem;
    private $commit_n;

    private $git = [

      'lg' => 'git lg',
      'show' => 'git show'

    ];

    private $git_ci_files = [];

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

          $this->git_ci_files = $this->get_files( $shell_out );
          if( !empty($this->git_ci_files) ) {

            foreach($this->git_ci_files as $file) {
              $file_split = explode('.', $file);
              $file_type = ( isset($file_split[1]) ? " \033[34m[{$file_split[1]}]\033[0m " : '' );

              self::log( self::$success, "Found{$file_type}file - {$file}" );
            }
          }
        } else {
          throw new Exception( 'Issue executing git command ['.$command.']' );
        }
      } catch (Exception $ex) {

        self::log( self::$error, $ex->getMessage() );
        exit;
      }
    }

    private function get_files ( $git_files ) {

      $files = [];
      $return = [];
      $lines = explode( PHP_EOL, $git_files );

      if( !empty($lines) ) {

        foreach( $lines as $line ) {

          if( substr($line, 0, 3) == '+++' ) $files[] = $line;
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
        echo "[\033[33m".date( 'H:i:s', time() )."\033[0m] ".$type.$message." \033[0m ".PHP_EOL;
      }
    }

  }

  $commit_n = ( isset($argv[1]) ? $argv[1] : null );

  if( $commit_n ) {

    $params = $argv;
    array_shift($params);

    $uploader = new Uploader( $params );
    $uploader->run();

  } else {

    Uploader::log( Uploader::$error, 'Commit number needed...php uploader.php [commit]' );

  }

?>
