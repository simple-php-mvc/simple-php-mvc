<?php

namespace MVC;

/**
 * View
 * 
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC
 */
class View
{

    /**
     * Path folder templates
     * @access public
     * @var string|array
     */
    public $templatesPath;

    /**
     * Add Template Path
     *
     * @param string $path Templates path
     */
    public function addTemplatePath($path)
    {
        if (is_array($this->templatesPath)) {
            if (array_search($path, $this->templatesPath)) {
                throw new \LogicException(sprintf('Templates path "%s" exists.', $path));
            } else {
                $this->templatesPath[] = $path;
            }
        } else if (is_string($this->templatesPath)) {
            $lastTemplate = $this->templatesPath;
            $this->templatesPath = array(
                $lastTemplate, $path
            );
        }
    }

    /**
     * Display the content of template
     * 
     * @access public
     * @param string $file    The file to be rendered.
     * @param mixed $vars     The variables to be substituted in the view.
     * @return void
     */
    public function display($file, $vars = null)
    {
        die($this->render($file, $vars));
    }

    /**
     * Escapes a value for output in an HTML context.
     * 
     * @access public
     * @param mixed $value
     * @return string
     */
    public function escape($value)
    {
        return nl2br(htmlspecialchars($value, ENT_QUOTES, "UTF-8"));
    }

    /**
     * Renders a given file with the supplied variables.
     * 
     * @access public
     * @param string $file    The file to be rendered.
     * @param mixed $vars     The variables to be substituted in the view.
     * @return string
     * @throws \LogicException
     */
    public function render($file, $vars = null) 
    {
        $template = '';

        if (is_null($this->templatesPath)) {
            throw new \LogicException('Variable "templatesPath" can\'t be NULL.');
        } else if (is_string($this->templatesPath) && !file_exists($this->templatesPath)) {
            throw new \LogicException(sprintf('Folder "%s" don\'t exists.', $this->templatesPath));
        } else if (is_array($this->templatesPath)) {
            foreach ($this->templatesPath as $folderPath) {
                if (file_exists($template = "$folderPath/{$file}")) {
                    break;
                }
            }
        }
        
        if (!$template) {
            $template = "$this->templatesPath/{$file}";
        }
        
        if(!file_exists($template)){
           throw new \LogicException(sprintf('View "%s" don\'t exists.', $template));
        }
        
        if (is_array($vars)) {
            extract($vars);
            foreach ($vars as $key => $value) {
                $key = $value;
            }
        }        
        
        ob_start();
        require $template;
        return ob_get_clean();
    }

}            
