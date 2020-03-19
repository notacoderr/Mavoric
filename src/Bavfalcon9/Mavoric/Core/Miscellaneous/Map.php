<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *   THIS CODE IS TO NOT BE REDISTRUBUTED
 *   @author MavoricAC
 *   @copyright Everything is copyrighted to their respective owners.
 *   @link https://github.com/Olybear9/Mavoric                                  
 */
namespace Bavfalcon9\Mavoric\Core\Miscellaneous;

class Map {
    private $data = [];

    public function __construct(Array $data=[]) {
        $this->data = $data;
    }

    public function set(String $key, $value) : boolean {
        $data[$key] = $value;
        return true;
    }

    public function delete(String $key) {
        if (!$this->get($key)) return false;
    }

    public function get(String $key) {
        if (!isset($data[$key])) return false;
        else return $data[$key];
    }

    public function getKeys() : Array {
        return array_keys($data);
    }

    public function getEntries() : Array {
        $entries = [];
        foreach ($this->data as $k=>$v) {
            array_push($v);
        }
        return $entries;
    }

    public function forEach($func) {
        foreach($this->data as $k=>$v) {
            $func($k, $v, array_search($k, $this->data));
        }
        return;
    }
}