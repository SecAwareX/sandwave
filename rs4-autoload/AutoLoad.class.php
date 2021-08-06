<?php
/**
 *  autoload.class.php :
 *  Author : Kurt Van Nieuwenhuyze <kurt@balancecoding.nl>
 *  Project : Fixc Framework 
 *  Package : rs4-autoload
 *  Version : 1.0.0
 *  @todo : 
 *      - Unittest
 *      - Implementation test
 *      - Performance test 
 * 
 */

namespace Ficx\Core\Autoload;


class AutoLoad
{
    
    /**
     * Storage for the registerd namespaces / prefixes and basedirectorys
     * Key is the namespace & value is a array with base directorys 
     * @var array 
     */
   protected $aNamespaces;
    
    /**
     * The real application ( absolute application path
     * @var string
     */
    protected $sRealPath;
    
    /**
     * Class Constructor
     * 
     * @return void
     */
    public function __construct(string $real_path)
    {
        $this->aNamespaces = array();
        $this->sRealPath = $real_path;
    }
    
    /**
     * Registering the auto load function(s) withe the SPL loader
     * 
     * @return void
     */
    public function register():void
    {
        spl_autoload_register(array($this, 'loadClassFile'));
    }
    
    /**
     * Registers the namespaces and basedirectorys
     * 
     * @param string $p_namespacePrefix : namespace 
     * @param string $p_baseDir : The base dir where classes / files can be found
     * @return void
     */
    public function addNamespace(string $p_namespacePrefix, string $p_baseDir = ""):void
    {
        //Clean the input from leading and trail separators
        $p_namespacePrefix = trim($p_namespacePrefix, "\\"). "\\";
        $p_baseDir = "/".trim($p_baseDir, "/")."/";
        
        //Instantiate the namespace in the array as array
        if(!isset($this->aNamespaces[$p_namespacePrefix]))
        {
            $this->aNamespaces[$p_namespacePrefix] = array();
        }
        
        array_push($this->aNamespaces[$p_namespacePrefix], $p_baseDir);
    }
    
    /**
     * Loads the classfile
     * 
     * @param string $p_Class Fully Qualified Classname
     * @return mixed boolean false when loading fails, when succeed the loaded name
     */
    public function loadClassFile(string $p_Class)
    {
        $namespacePrefix = $p_Class;
        while (false !== $pos = strrpos($namespacePrefix, '\\')) {
           
            // retain the trailing namespace separator in the prefix
            $namespacePrefix = substr($p_Class, 0, $pos + 1);
          
            // the rest is the relative class name
            $className = substr($p_Class, $pos + 1);
            
            //Do the loading of the file
            //When the first try failed , try to load by resolving the full namespace as filepath
            $file = $this->loadFile($namespacePrefix, $className);
            if($file) {
                return $file;
            } else {
                
                if($this->loadFileFromNamespace($namespacePrefix,$className)) {
                    return $file;
                } else {
                    return false;
                }
            }
           
            // remove the trailing namespace separator for the next iteration
            // of strrpos()
            $namespacePrefix = rtrim($namespacePrefix, '\\');
        }
        
        //The file isn't loaded
        return false;
    }
    
    /**
     * Load the files from the given basedirectorys 
     * 
     * @param string $p_Prefix
     * @param string $p_className
     * @return mixed boolean|string boolean false when loading fails, filename when succeed
     */
    public function loadFile(string $p_Prefix, string $p_className)
    {
        //If the namespace is not registerd
        if(!isset($this->aNamespaces[$p_Prefix])) {
            return false;
        }
        
        //loop the registerd basedirectorys
        foreach($this->aNamespaces[$p_Prefix] AS $dir) {
            
            $file =  $this->sRealPath.str_replace("/",DIRECTORY_SEPARATOR , $dir).$p_className.".php";
           
            if($this->includeFile($file)) {
                return $file;
            } else {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Resolves a path from the fully qualified name
     * 
     * @param string $p_namespace
     * @param string $p_className
     * @return mixed string|boolean boolean false when fails else filename
     */
    public function loadFileFromNamespace(string $p_namespace, string $p_className)
    {
        $namespacePath = "\\".strtolower($p_namespace);
        $file =  $this->sRealPath.$namespacePath.$p_className.".php";
        if($this->includeFile($file)) {
            return $file;
        } else {
            return false;
        }
    }
    
    /**
     * Include the requested file 
     * 
     * @param string class filename
     * @return boolean
     */
    public function includeFile(string $p_file):bool
    {
        if(file_exists($p_file)){
            require_once($p_file);
            return true;
        } else {
            return false;
        }
        
            
    }
    
    
}