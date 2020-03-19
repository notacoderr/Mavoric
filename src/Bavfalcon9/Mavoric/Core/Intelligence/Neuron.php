<?php
/***
 *      __  __                       _      
 *     |  \/  |                     (_)     
 *     | \  / | __ ___   _____  _ __ _  ___ 
 *     | |\/| |/ _` \ \ / / _ \| '__| |/ __|
 *     | |  | | (_| |\ V / (_) | |  | | (__ 
 *     |_|  |_|\__,_| \_/ \___/|_|  |_|\___|
 *                                          
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  @author Bavfalcon9
 *  @link https://github.com/Olybear9/Mavoric                                  
 */

namespace Bavfalcon9\Mavoric\Core\Intelliegence;

use Bavfalcon9\Mavoric\Intelligence\Utils\Map;

class Neuron {
    /** @var Array */
    public $incoming = [];
    /** @var Array */
    public $outgoing = [];
    /** @var String */
    protected $id;
    /** @var Float */
    protected $bias;
    /** @var Error */
    private $error;
    /** @var Output */
    private $_output;
    /** @var Output */
    public $output;

    public function __construct() {
        $this->id = CUID::generate();
        $this->bias = mt_rand() / mt_getrandmax();
        $this->incoming = new ConnectionList();
        $this->outgoing = new ConnectionList();
    }

    public function connect(Neuron $neuron, int $weight) {
        $this->outgoing->neurons[$neuron->getId()] = $neuron;
        $neuron->incoming->neurons[$this->getId()] = $this;
        $weight = mt_rand() / mt_getrandmax();
        $this->outgoing->weights[$neuron->getId()] = $weight; 
        $neuron->incoming->weights[$this->getId()] = $weight;
    } 

    public function getId(): String {
        return $this->id;
    }

    public function train($set = null) {
        $self = $this; // needed because of unnamed functions
        if ($set !== null) {
            $this->_output = 1;
            $this->output = $set;
        } else {
            // Sigma (x • w)
            /**
             * to do, reduce neurons for output
             */
            return $this->output;
        }
    }

    private function sigmoid($int) {
        return 1 / (1 + exp(-$int)); // f of x
    }

    private function sigmoidX($x) {
        return $this->sigmoid(x) * (1 - $this->sigmoid(x)); // f'x derative testing
    }

}