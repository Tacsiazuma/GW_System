<?php

namespace System\Cache;

use System\Helper\Singleton;


class OpCache extends Singleton {

    private $memory, $statistics, $scripts;
    
    
    public function __construct() {
        if (!function_exists('opcache_compile_file'))
            throw new \Exception("The OpCache extension isn't available!");
        $status = opcache_get_status(true);
        $this->memory = $status['memory_usage'];
        $this->statistics = $status['opcache_statistics'];
        $this->scripts = $status['scripts'];
    }
    
    public function getConfig() {
        return opcache_get_configuration();
    }
    
    public function getStatistics() {
        return $this->statistics;
    } 
    public function getMemoryUsage() {
        return $this->memory;
    }
    
    public function getScripts() {
        return $this->scripts;
    }
    
    public function reset() {
        //opcache_reset();
        
        $files = $this->compileFiles(APP_ROOT);
        return $files;
    }
    private function getFiles( $path , &$files = array() ) {
        if ( !is_dir( $path ) ) return null;
        $handle = opendir( $path );
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( $file != '.' && $file != '..' ) {
                $path2 = $path . '/' . $file;
                if ( is_dir( $path2 ) ) {
                    $this->getFiles( $path2 , $files );
                } else {
                    if ( preg_match( "/\.(php|php5)$/i" , $file ) ) {
                        $files[] = $path2;
                    }
                }
            }
        }
        return $files;
    }
    
    private function compileFiles($path) {
        $files = $this->getFiles($path);
        $br = (php_sapi_name() == "cli") ? "\n" : "<br />";
        foreach($files as $file){
            @opcache_compile_file($file);
        }
        return $files;
    }
    
    
    
}