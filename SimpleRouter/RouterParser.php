<?php

namespace SimpleRouter;


class RouterParser {
    private $path;
    const DELIMITER = '/';
    const PARAMETER_IDENTIFIER = ':';

    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * @param $path
     */

    public function setPath($path) {
        $this->path = $path;
    }

    public function getPath() {
        return $this->path;
    }

    /**
     * @param $pattern
     * @return bool
     */

    public function hasParams($pattern) {
        $position = strpos($pattern, self::PARAMETER_IDENTIFIER);
        return ($position === false) ? false : true;
    }

    /**
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