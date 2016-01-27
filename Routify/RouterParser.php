<?php

namespace Routify;


class RouterParser {

    /**
     * @var string The path requested by the browser(or any client)
     */
    private $path;

    /**
     * The delimiter used to separate different paths(e.g mydomain.com/api/v1/user/Siro_Diaz).
     */
    const DELIMITER = '/';

    /**
     * The parameter identifier for the path pattern (e.g /api/v1/user/:screen_name).
     */
    const PARAMETER_IDENTIFIER = ':';

    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * Sets the path.
     *
     * @param $path
     */

    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Give the requested path.
     *
     * @return string
     */

    public function getPath() {
        return $this->path;
    }

    /**
     * Checks if the path pattern has parameters(strings with : at
     * the beginning).
     *
     * @param $pattern
     * @return bool
     */

    public function hasParams($pattern) {
        $position = strpos($pattern, self::PARAMETER_IDENTIFIER);
        return ($position === false) ? false : true;
    }

    /**
     * Returns the number of parameters in the path pattern.
     *
     * @param $pattern
     * @return int
     */

    public function countParams($pattern) {
        if($this->hasParams($pattern) === false) {
            return 0;
        }

        $pattern = str_split($pattern); // split string in characters
        $totalParams = 0;
        for($i = 0; $i < count($pattern); $i++) {
            if($pattern[$i] === self::PARAMETER_IDENTIFIER) {
                $totalParams++;
            }
        }

        return $totalParams;
    }

    /**
     * Returns an associative array with the parameter name
     * and its value. If there is not any parameter in the pattern
     * then return an empty array.
     *
     * @param $pattern
     * @return array
     */

    public function getParams($pattern) {
        if(!$this->hasParams($pattern)) {
            return [];
        }

        $pattern = explode('/', $pattern);
        $path = explode('/', $this->path);
        $params = [];
        for($i = 0; $i < count($pattern); $i++) {
            if(strpos($pattern[$i], self::PARAMETER_IDENTIFIER) !== false) {
                $params[substr($pattern[$i], 1)] = $path[$i];
            }
        }

        return $params;
    }

    /**
     * Checks if the path pattern matches with the requested uri.
     *
     * @param $pattern
     * @return bool
     */

    public function match($pattern) {
        $pattern = explode('/', $pattern);
        $path = explode('/', $this->path);

        if(count($pattern) !== count($path)) {
            return false;
        }

        $found = true;
        $index = 0;
        while($found && $index < count($pattern)) {
            $check = true;
            if(strpos($pattern[$index], self::PARAMETER_IDENTIFIER) !== false) {
                $check = false;
            }

            if($check) {
                if (!preg_match('/^'. $pattern[$index] .'$/', $path[$index])) {
                    $found = false;
                }
            }

            $index++;
        }

        return $found;
    }

}