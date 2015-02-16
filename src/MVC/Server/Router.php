<?php

namespace MVC\Server;

/**
 * The Router is used to handle url routing.
 *
 * @author  Ramón Serrano <ramon.calle.88@gmail.com>
 * @package MVC\Server
 */
class Router
{

   /**
    * Compiles the regex necessary to capture all match types within a route.
    * @access protected
    * @param string $route    The route.
    * @return string
    */
    protected function _compile_regex($route)
    {
        $pattern = '`(/|\.|)\[([^:\]]*+)(?::([^:\]]*+))?\](\?|)`';

        if ( preg_match_all($pattern, $route, $matches, PREG_SET_ORDER) ) {
            $match_types = array(
                'i'  => '[0-9]++',
                'a'  => '[0-9A-Za-z]++',
                'h'  => '[0-9A-Fa-f]++',
                '*'  => '.+?',
                ''   => '[^/]++'
            );
            foreach ( $matches as $match ) {
                list($block, $pre, $type, $param, $optional) = $match;

                if ( isset($match_types[$type]) ) {
                    $type = $match_types[$type];
                }
                if ( $param ) {
                    $param = "?<{$param}>";
                }
                if ( $optional ) {
                    $optional = '?';
                }

                $replaced = "(?:{$pre}({$param}{$type})){$optional}";
                $route = str_replace($block, $replaced, $route);
            }
        }
        if ( substr($route, strlen($route) - 1) != '/' ) {
            $route .= '/?';
        }
        return "`^{$route}$`";
    }

   /**
    * Parses the supplied request uri based on the supplied routes and
    * the request method.
    *
    * Routes should be of the following format:
    *
    * <code>
    * $routes = array(
    *     array(
    *         mixed $request_method, string $request_uri, callable $callback
    *     ),
    *     ...
    * );
    * </code>
    *
    * where:
    *
    * <code>
    * $request_method can be a string ('GET', 'POST', 'PUT', 'DELETE'),
    * or an array (e.g., array('GET, 'POST')).  Note that $request_method
    * is case-insensitive.
    * </code>
    *
    * <code>
    * $request_uri is a string, with optional match types.  Valid match types
    * are as follows:
    *
    * [i] - integer
    * [a] - alphanumeric
    * [h] - hexadecimal
    * [*] - anything
    *
    * Match types can be combined with parameter names, which will be
    * captured:
    *
    * [i:id] - will match an integer, storing it within the returned 'params'
    * array under the 'id' key
    * [a:name] - will match an alphanumeric value, storing it within the
    * returned 'params' array under the 'name' key
    *
    * Here are some examples to help illustrate:
    *
    * /post/[i:id] - will match on /post/32 (with the returned 'params' array
    * containing an 'id' key with a value of 32), but will not match on
    * /post/today
    *
    * /find/[h:serial] - will match on /find/ae32 (with the returned 'params'
    * array containing a 'serial' key will a value of 'ae32'), but will not
    * match on /find/john
    * </code>
    *
    * <code>
    * $callback is a valid callback function.
    * </code>
    *
    * Returns an array containing the following keys:
    *
    * * 'params'   - The parameters collected from the matched uri
    * * 'callback' - The callback function pulled from the matched route
    *
    * @access public
    * @param string $requestUri       The request uri.
    * @param string $requestMethod    The request method.
    * @param Route[] $routes             The routes.
    * @return array
    */
    public function parse(HttpRequest $request, $routes)
    {
        foreach ( $routes as $route ) {
            if (!$route instanceof Route) {
                throw new \LogicException(sprintf('The route given don\'t is instance of Route. Type given "%s".', gettype($route)));
            }
            list($methods, $patternUri, $action) = array($route->getMethods(), $route->getPatternUri(), $route->getAction());

            if ( is_array($methods) ) {
                $found = false;
                foreach ( $methods as $method ) {
                    if ( strcasecmp($request->getMethod(), $method) == 0 ) {
                        $found = true;
                        break;
                    } elseif (($method === strtolower('ajax') || $method === strtolower('xhr')) && $request->isAjax()) {
                        $found = true;
                        break;
                    }
                }
                if ( !$found ) {
                    continue;
                }
            } elseif ( strcasecmp($request->getMethod(), $methods) != 0 ) {
                continue;
            } elseif (($methods === strtolower('ajax') || $methods === strtolower('xhr')) && $request->isAjax()) {
                continue;
            }

            if ( is_null($patternUri) || $patternUri == '*' ) {
                $found = false;
                $params = array();
                return compact('action', 'found', 'params');
            }

            $found = true;

            $routeToMatch = '';
            $len = strlen($patternUri);

            $requestUri = $request->url;
            for ( $i = 0; $i < $len; $i++ ) {
                $char = $patternUri[$i];
                $is_regex = (
                    $char == '[' || $char == '(' || $char == '.'
                    || $char == '?' || $char == '+' || $char == '{'
                );
                if ( $is_regex ) {
                    $routeToMatch = $patternUri;
                    break;
                } elseif (
                    !isset($requestUri[$i]) || $char != $requestUri[$i]
                ) {
                    continue 2;
                }
                $routeToMatch .= $char;
            }

            $params = array();

            $regex = $this->_compile_regex($routeToMatch);
            if ( preg_match($regex, $requestUri, $paramsMatched) ) {
                foreach ( $paramsMatched as $key => $arg ) {
                    if ( is_numeric($key) ) {
                        unset($paramsMatched[$key]);
                    } else {
                        $params[$key] = $arg;
                    }
                }
                return compact('action', 'found', 'params');
            }
        }
        return array(
            'found'    => false,
            'params'   => null,
            'action' => null
        );
    }

}
