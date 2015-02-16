<?php

namespace MVC\File;

/**
 * Description of Explorer
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 */
class Explorer extends \FilesystemIterator
{
    
    /**
     * Regex pattern search
     * 
     * @var string 
     */
    protected $searchPattern;
    
    /**
     * Explorer files and folders
     * 
     * @param string $path  Dir path of folder
     * @param int $flags FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS
     */
    public function __construct($path, $flags = null)
    {
        if (!is_null($flags)) {
            parent::__construct($path, $flags);
        } else {
            parent::__construct($path);
        }
    }
    
    /**
     * Copy directory to destiny directory
     * 
     * @param string $sourceDir
     * @param string $destinyDir
     */
    public function copy($sourceDir, $destinyDir)
    {
        $dir = opendir($sourceDir);
        $this->mkdir($destinyDir);
        while (false !== ( $file = readdir($dir))) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if (is_dir($sourceDir . '/' . $file)) {
                    $this->copy($sourceDir . '/' . $file, $destinyDir . '/' . $file);
                } else {
                    copy($sourceDir . '/' . $file, $destinyDir . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
    
    /**
     * Get files
     * 
     * @return Explorer
     */
    public function getFiles()
    {
        $files = array();
        while ($this->valid() && $this->isFile()) {
            if (!$this->searchPattern) {
                $files[] = $this->current();
            } elseif(preg_match($this->searchPattern, $this->getFilename())) {
                $files[] = $this->current();
            }
            $this->next();
        }
        return $files;
    }
    
    /**
     * Make dir
     * 
     * @param string $path
     * @param int $mode
     * @return boolean
     */
    public function mkdir($path, $mode = 0777)
    {
        return @mkdir($path, $mode);
    }
    
    /**
     * Remove dir
     * 
     * @param string $path
     * @return boolean
     */
    public function rmdir($path)
    {
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$path/$file")) ? $this->rmdir("$path/$file") : unlink("$path/$file");
        }
        return rmdir($path);
    }
    
    /**
     * Set Regex Pattern search
     * 
     * @param string $searchPattern
     * @return Explorer
     */
    public function setSearchPattern($searchPattern)
    {
        $this->searchPattern = $searchPattern;
        
        return $this;
    }


}
