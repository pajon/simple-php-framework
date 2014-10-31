<?php
abstract class SPF_Cache_Handler {
    abstract public function set($index, $data, $expire);
    abstract public function get($index);
    abstract public function exists($index);
    abstract public function remove($index);
}
?>
