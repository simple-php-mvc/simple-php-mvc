<?php

/**
 * Error Handle
 *
 * @author Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC
 */

namespace MVC;

class Error
{

    /**
     * Puts the exeption in HTML
     * @access public 
     * @param \Exception $e
     * @return void
     */
    public static function run(\Exception $e)
    {
        print '<meta charset="UTF8">';
        print "<h1>MVC Framework</h1>";
        print "<p><b>Error:</b> {$e->getMessage()}</p>";
        print "<p><b>File:</b> {$e->getFile()}</p>";
        print "<p><b>Line:</b> {$e->getLine()}</p>";
        print "<p><b>Code:</b> {$e->getCode()}</p>";
        print "<p><b>Trace:</b></p>";
        print str_replace("\n", '<br>', $e->getTraceAsString());
    }

}