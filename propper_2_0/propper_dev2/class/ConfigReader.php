<?php
	error_reporting(0);
    class ConfigReader
    {
        private $configFile = 'config.ini';
        private $items = array();

        function __construct() {
            $this->parse();
        }

        function __get($id) {
            return $this->items[ $id ];
        }

        function parse(){
            $fh = fopen( $this->configFile, 'r' );
            while( $l = fgets( $fh ) ){
                //if ( preg_match( '/^#/', $l ) == false ){
                    preg_match( '/^(.*?)=(.*?)$/', $l, $found );
                    $this->items[ $found[1] ] = substr($found[2],0,strlen($found[2])-1);
                //}
            }
            fclose( $fh );
        }
    }

?>