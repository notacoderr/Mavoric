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

namespace Bavfalcon9\Mavoric\Intelliegence;

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
    private $output;

    public function __construct() {
        $this->id = CUID::generate();
        $this->bias = mt_rand() / mt_getrandmax();
        $this->incoming = new ConnectionList();
        $this->outgoing = new ConnectionList();
    }

    public function connect(Neuron $neuron, int $weight) {
        $this->outgoing->addNeuron($neuron);
        $neuron->incoming->addNueron($this);
        $weight = mt_rand() / mt_getrandmax();
        $this->outgoing->weights[$neuron->getId()] = $weight; 
        $neuron->incoming->weights[$this->getId()] = $weight;
    } 

}