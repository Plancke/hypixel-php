<?php


namespace Plancke\HypixelPHP\util;


use Closure;

class CachedGetter {

    protected $generated = false;
    protected $closure;
    protected $value;

    /**
     * Generator constructor.
     * @param Closure $closure
     */
    public function __construct($closure) {
        $this->closure = $closure;
    }


    /**
     * @return mixed
     */
    public function get() {
        if (!$this->generated) {
            $this->generated = true;
            $this->value = ($this->closure)();
        }
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isGenerated(): bool {
        return $this->generated;
    }

    /**
     * @return Closure
     */
    public function getClosure(): Closure {
        return $this->closure;
    }

}