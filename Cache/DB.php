<?php

class SPF_Cache_DB extends SPF_Cache_Handler {

    public function set($index, $data, $expire) {
        SPF_DB::Replace("spf_cache", array(
            'cache_index'=>$index,
            'cache_data'=>$data,
            'cache_expire'=>time()+$expire
        ));
    }

    public function get($index) {
        SPF_DB::Select("SELECT * FROM spf_cache WHERE cache_index='%s'", $index);
        if(SPF_DB::Num()) {
            $data = SPF_DB::Data();
            if($data['cache_expire'] < time()) {
                $this->remove($index);
                return NULL;
            } else {
                return $data['cache_data'];
            }
        } else return NULL;
    }
    
    public function exists($index) {
        SPF_DB::Select("SELECT * FROM spf_cache WHERE cache_index='%s'", md5($index));
        return (SPF_DB::Num()==1?TRUE:FALSE);
    }
    
    public function remove($index) {
        SPF_DB::Delete("spf_cache", array('cache_index'=>md5($index)));
    }

}