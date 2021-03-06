<?php
namespace libs\scifile;

class File{
    protected $data_file, $accept_types, $type, $min_size, $max_size;
    protected static $relative_storage = null;
    protected static $absolute_storage = null;
    public static $base = null;
    protected $name, $title, $extension;

    public function __construct($data_file, $title=""){
        $this->data_file = $data_file;
        if(!empty($this->data_file)){
            $this->name = $this->getName();
            $this->extension = $this->getExtension();
        }
        $this->title = $title;
    }

    /**
    * Set storage location of images.
    * @param string $absolute - absolute path to storage folder.
    * @param string $relative - relative path to storage folder base on the app root.
    */
    public static function register_location($absolute, $relative=null){
        self::$relative_storage = $relative;
        self::$absolute_storage = $absolute;
        self::mkdir(self::$absolute_storage);
    }
    
    /**
    * Builds a file path with the appropriate directory separator.
    * @param string $segments,... unlimited number of path segments
    * @return string Path
    */
    public static function build_path() {
        return join(DIRECTORY_SEPARATOR, func_get_args());
    }

    /**
    * Make director recursively, where applicable.
    * @param string $dir - Path to make if not exists. It is Default to empty string.
    * @return bool - True if the directory has been created or already exists, else
    *   false.
    */
    public static function mkdir($dir=""){
        if(!$dir){
            $dir = self::$absolute_storage;
        }
        if(!is_dir($dir)){
            return mkdir($dir, 0775, true);
        }
        return true;
    }

    /**
    * Get storage location of images.
    * @param string $type - type of storage to get, i.e. absolute or relative path.
    */
    public static function getStorage($type="absolute"){
        if($type=="absolute")
            return self::$absoluteStorage;
        return self::$relativeStorage;
    }

    /**
    * Get the name of the file.
    * @return string - name of the file.
    */
    public function getName(){
        if(strripos($this->data_file,'/') != false){
            $name = substr($this->data_file, strripos($this->data_file,'/')+1, (strlen($this->data_file) - strripos($this->data_file,'.') + 1));
        }else{
            $name = substr($this->data_file, 0,  strripos($this->data_file,'.'));
        }
        return $name;
    }

    /**
    * Get the file extension.
    * @return string - file extension.
    */
    public function getExtension(){
        return strtolower(strrchr($this->data_file, '.'));
    }
    
    /**
    * Change from absolute to relative path based on the app root.
    * @param string $path - Absolute path of file/folder.
    * @return string - Relative path of file/folder.
    */
    public static function getRelativeFromAbsolute($path){
        if(!empty(self::$base)){
            return str_replace(self::$base, "", $path);
        }
        return $path;
    }
}

?>
