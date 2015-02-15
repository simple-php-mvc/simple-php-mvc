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
