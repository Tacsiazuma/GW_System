<?php
namespace System\StdLib\View\ViewHelper;

use System\Helper\ServiceManager;
use System\Helper\Exception\ServiceMissingException;
use System\StdLib\MvcEvent\Router;

/**
 * 
 * @author Papp Krisztián <tacsiazuma@gmail.com>
 *
 */
class HeadLink extends AbstractViewHelper {

    private $links = array();
    
    public function __construct() {
        parent::__construct(); // get the inherited configuration
        $this->config = $this->getConfig();
        
        
    }
    private function getConfig() {
        return isset($this->view_config['css']) ? $this->view_config['css'] : false;
    }
    /**
     * Return if the merged css files are cached or not
     * @return boolean
     */
    private function isCacheEnabled() {
        return isset($this->config['cache']) ? $this->config['cache'] : false;
    }
    /**
     * Get the directory containing scss files
     * Its in the view_manager->css->sass_dir index.
     * @throws ServiceMissingException
     */
    private function getScssDir() {
        if (isset($this->config['sass_dir']) && is_dir($this->config['sass_dir'])) 
            
            return $this->config['sass_dir'];
        else 
            throw new ServiceMissingException('The configuration for the Sass direcotry is missing or malformed!');
    }
    
    private function getCssUrl() {
        if (isset($this->config['css_url']))
        
            return $this->config['css_url'];
        else
            throw new ServiceMissingException('The configuration for the CSS URL is missing or malformed!');
        }
    /**
     * Get the directory containing css files
     * Its in the view_manager->css->css_dir index.
     * @throws ServiceMissingException
     */
    private function getCssDir() {
        if (isset($this->config['css_dir'])) {
            return $this->config['css_dir'];
        }
        else
            throw new ServiceMissingException('The configuration for the css direcotry is missing or malformed!');
    }

    public function prependStylesheet($href, $media = "screen", $conditionalStylesheet = true, $extras = array())
    {
        $link = array(
            'href' => $href,
            'media' => $media,
            'conditionals' => $conditionalStylesheet,
            'extras' => $extras
        );
        array_unshift($this->links, $link);
        return $this;

    }

    public function appendStylesheet($href, $media = "screen", $conditionalStylesheet = true, $extras = array())
    {
        $link = array(
            'href' => $href,
            'media' => $media,
            'conditionals' => $conditionalStylesheet,
            'extras' => $extras
        );
        array_push($this->links, $link);
        return $this;
    }

    private function buildMarkup($link)
    {
        $href = $link['href'];
        $media = $link['media'];
        $extras = $link["extras"];
        $string = "<link rel=\"stylesheet\" href=\"$href\" media=\"$media\" type=\"text/css\" ";
        foreach ($extras as $key => $value) {
            $string .= "$key = \"$value\" ";
        }
        $string .= ">";
        return $string;
    }

    /**
     * Compile the scss files in the directory given in the configuration 
     * @var $href the HREF field in the stylesheet tags
     * @return the HREF field of the compiled file
     * at the index sass_dir
     */
    private function compileCSS($href) {


        $segments = explode("/",$href); // explode the href by /
       $scssfile = array_pop($segments); // get the last element (the concrete filename)
        $cssfile = $this->getCssDir() . str_replace("scss", "min.css", $scssfile); // the css file name

       $precompiled = file_get_contents($this->getScssDir().$scssfile); // get its contents
        $scss = ServiceManager::getInstance()->get('scss'); // get the scss class
       $scss->setImportPaths($this->getScssDir()); // setting the import path
       $compiled = $scss->compile($precompiled); // compile it

       if (!file_put_contents($cssfile, $compiled)) // put the contents into the css file
           throw new \InvalidArgumentException("Cannot write compiled content into file '$cssfile'");
       return str_replace($this->getCssDir(), $this->getCssUrl(), $cssfile); // and return the href
    }

    private function isCached()
    {
        $file = $this->getCssDir() . md5(Router::getInstance()->getController() . Router::getInstance()->getAction()) . ".min.css";
        clearstatcache(true, $file);

        if (file_exists($file)) {
            if ($this->config['cache']['ttl'] == 0) return true;
            $limit = (filemtime($file) + $this->config['cache']['ttl']);
            if ($limit > time())
                return true;
        }
        return false;
    }

    /**
     * Get the css file from cache
     */
    private function getFromCache()
    {
        $name = $this->getCssUrl() . md5(Router::getInstance()->getController() . Router::getInstance()->getAction()) . ".min.css";
        $this->links = array(array(
            'href' => $name,
            'media' => "screen",
            'conditionals' => false,
            'extras' => array()
        ));
    }


    /**
     * Minifies CSS files
     */
    private function minify()
    {
        // pass the file elements by reference
        foreach ($this->links as &$file) {
            $href = $file['href'];
            if (strpos($href, ".scss") == true) {
                $href = $this->compileCSS($href);
            }
            if (strpos($href, ".min.css") === false) { // it's already minified
                $segments = explode('/', $href);
                $cssfile = array_pop($segments); // get the last element (the concrete filename)
                $content = file_get_contents($this->getCssDir() . $cssfile);
                ServiceManager::getInstance()->get('cssmin', $content);
                $minifiedcontent = \CssMin::minify($content); // get the minified content
                $minifiedfile = $this->getCssDir() . rtrim($cssfile, "css") . "min.css";
                file_put_contents($minifiedfile, $minifiedcontent); // put the contents into a file
                $file['href'] = str_replace($this->getCssDir(), $this->getCssUrl(), $minifiedfile);
            }
        }
    }

    /**
     * Merge the javascript files in their order
     * into one file, named by the current controller.action."js" md5 hash placed in the js directory
     */
    private function merge()
    {
        $content = "/* Cached at " . date("Y-m-d H:i:s") . " */";
        foreach ($this->links as $link) {
            $filename = str_replace($this->getCssUrl(), $this->getCssDir(), $link['href']); // get the filename
            $content .= file_get_contents($filename); // append the content
        }
        $name = md5(Router::getInstance()->getController() . Router::getInstance()->getAction()) . ".min.css";
        file_put_contents($this->getCssDir() . $name, $content); // put the merged content into a file
        $this->links = array();
        array_push($this->links, array(
                'href' => $this->getCssUrl() . $name,
                'media' => 'screen',
                'extras' => array(),
                'conditionals' => false,
            )
        );
    }

    /**
     * Renders the files into HTML markup
     * @return Ambigous <string, unknown>
     */
    public function render() {
        $markup = '';
        if (!empty($this->links)) {
            if (!$this->isCached() || !$this->isCacheEnabled()) {
                $this->minify(); // minify files one by one
                $this->merge(); // merge
            } else $this->getFromCache();
        }
        foreach ($this->links as $link) {
            $markup .= $this->buildMarkup($link)."\n";
        }
        return $markup;
    }
}