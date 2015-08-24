<?php

namespace System\StdLib\View\ViewHelper;



use System\Helper\ServiceManager;
use System\Helper\Exception\ServiceMissingException;
use System\Http\Request;
use System\Cache\Storage\StorageAdapter;
use System\StdLib\MvcEvent\Router;

class HeadScript extends AbstractViewHelper {
    
    /**
     * Script files
     * @var array
     */
    private $scriptfiles = array();
    /**
     * Scripts between tags
     * @var array
     */
    private $scripts = array();
    
    public function __construct() {
        parent::__construct();
        $this->config = $this->getConfig();
    }
    
    private function getConfig() {
        return isset($this->view_config['js']) ? $this->view_config['js'] : false;
    }
    /**
     * Return if the merged js files are cached or not
     * @return boolean
     */
    private function isCacheEnabled() {
        return isset($this->config['cache']['enabled']) ? $this->config['cache']['enabled'] : false;
    }
    
    /**
     * Get the directory containing JS files
     * Its in the view_manager->js->js_dir index.
     * @throws ServiceMissingException
     */
    private function getJsDir() {
        if (isset($this->config['js_dir']) && is_dir($this->config['js_dir']))
    
            return $this->config['js_dir'];
        else
            throw new ServiceMissingException('The configuration for the JavaScript direcotry is missing or malformed!');
    }    
    /**
     * Get the URL containing JS files
     * Its in the view_manager->js->js_url index.
     * @throws ServiceMissingException
     */
    private function getJsUrl() {
        if (isset($this->config['js_url']))
        
            return $this->config['js_url'];
        else
            throw new ServiceMissingException('The configuration for the JavaScript URL is missing or malformed!');
        }
    
    /**
     * Prepends a file onto the start of the scripts
     * 
     * @param string $src Src field
     * @param string $type Type field
     * @param array $attrs attributes in assoc array
     * @param string $conditionals The conditional field for the script
     * @return \System\StdLib\View\ViewHelper\HeadScript
     */
    public function prependFile($src, $type = 'text/javascript', $attrs = array(), $conditionals = null) {
        $script = array(
            'src' => $src,
            'type' => $type,
            'attributes' => $attrs,
            'conditionals' => $conditionals
        );
        array_unshift($this->scriptfiles, $script);
        return $this;
    }   

    /**
     * Builds a scripts markup
     * @param array $script
     * @return string $string The markup built
     */
    private function buildMarkup($script) {
        $attrs = $script['attributes'];
        $src = $script['src'];
        $conditionals = $script['conditionals'];
        $type = $script['type'];
        $attributes = "";
        foreach ($attrs as $key => $value) {
            $attributes .= " $key = \"$value\"";
        }
        $string = "<script type=\"$type\" src=\"$src\" $attributes></script>";
        if ($conditionals != false) {
            $string = "<!--[$conditionals]><!-->$string<!--<![endif]-->";
        }
        return $string;
    }
    /**
     * Appends a file onto the end of the scripts
     *
     * @param string $src Src field
     * @param string $type Type field
     * @param array $attrs attributes in assoc array
     * @param string $conditionals The conditional field for the script
     * @return \System\StdLib\View\ViewHelper\HeadScript
     */
    public function appendFile($src, $type = 'text/javascript', $attrs = array(), $conditionals = null) {
        $script = array(
            'src' => $src,
            'type' => $type,
            'attributes' => $attrs,
            'conditionals' => $conditionals
        );
        array_push($this->scriptfiles, $script);
        return $this;
    }
    /**
     * Minifies a Javascript file
     * @param string $src The src element of the script tag
     * @return string $src The src element of the minified file
     */
    private function minifyJS() {
        // pass the file elements by reference
        foreach ($this->scriptfiles as &$file) {
            $src = $file['src'];
            if (strpos($src,".min.js") === false) { // it's already minified
                $jsfile = $file['src']; // get the last element (the concrete filename)
                $content = file_get_contents(PUBLIC_FOLDER.$jsfile);
                $jsmin = ServiceManager::getInstance()->get('jsmin', $content);
                $minifiedcontent = $jsmin->min(); // get the minified content
                $minifiedfile = PUBLIC_FOLDER.rtrim($jsfile, "js")."min.js";
                file_put_contents($minifiedfile, $minifiedcontent); // put the contents into a file
                $file['src'] = str_replace(PUBLIC_FOLDER, BASEPATH."/",$minifiedfile);
            }
        }
    }
    
    /**
     * Merge the javascript files in their order
     * into one file, named by the current controller.action."js" md5 hash placed in the js directory
     */
    private function mergeJS() {
        $content = "/* Cached at ".date("Y-m-d H:i:s")." */";
        foreach ($this->scriptfiles as $script) {

            $filename = str_replace(BASEPATH . "/", PUBLIC_FOLDER, $script['src']); // get the filename
            $content .= file_get_contents($filename); // append the content
        }
        $name = "head" . md5(Router::getInstance()->getController() . Router::getInstance()->getAction()) . ".min.js";
        file_put_contents($this->getJsDir().$name,$content); // put the merged content into a file 
        $this->scriptfiles = array();
        array_push($this->scriptfiles, array(
                'src' => $this->getJsUrl().$name,
                'type' => 'text/javascript',
                'attributes' => array(),
                'conditionals' => false,
             )
        );
    }
    
    
    
    /**
    * Append script to the head tag between script tags
     */
    public function appendScript($script, $type="text/javascript", $attrs= array()) {
        array_push($this->scripts, "<script type=\"$type\">$script</script>");
        return $this;
    }
    /**
     * Prepend script to the head tag between script tags
     */
    public function prependScript($script, $type="text/javascript", $attrs= array()) {
        array_unshift($this->scripts, "<script type=\"$type\">$script</script>");
        return $this;
    }
    /**
     * If the cache file for the given controller/action already exists
     * @return boolean
     */
    private function isCached() {
        $name = "head" . md5(Router::getInstance()->getController() . Router::getInstance()->getAction()) . ".min.js";
        clearstatcache(true, $this->getJsDir() . $name);
        // check if the file exists
        if (file_exists($this->getJsDir().$name)) {
            if ($this->config['cache']['ttl'] == 0) return true;
            // check if the file is recent
            if ((time() - $this->config['cache']['ttl']) > filemtime($this->getJsDir() . $name)) {
                return true;
            } else return false;

        } else
            return false; 
    }
    /**
     * 
     */
    private function getFromCache() {
        $name = "head" . md5(Router::getInstance()->getController() . Router::getInstance()->getAction()) . ".min.js";
        $this->scriptfiles = array( array(
           'src' => $this->getJsUrl().$name,
           'type' => "text/javascript",
           'conditionals' => false,
           "attributes" => array()
       ));
    }
    
    /**
     * Renders the HTML markup
     * @return string
     */
    public function render()
    {
        if (!empty($this->scriptfiles)) {
            if (!$this->isCached() && $this->isCacheEnabled()) {
                $this->minifyJS(); // minify files one by one
                $this->mergeJS(); // merge
            } else $this->getFromCache();
        }
        $scriptMarkup = ''; // define an empty script markup if no scripts were added
        foreach ($this->scriptfiles as $script) { // iterate through the scriptfiles
            $scriptMarkup .= $this->buildMarkup($script)."\n";
        }
        foreach ($this->scripts as $script) { // iterate through the script tags
            $scriptMarkup .= $script."\n";
        }

        return $scriptMarkup;
    } 
}