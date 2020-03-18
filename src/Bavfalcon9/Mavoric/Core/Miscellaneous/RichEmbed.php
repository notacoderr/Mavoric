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

// Fuck this 
//namespace Bavfalcon9\Mavoric\misc\Classes;

/**
 * @author: John.#9309
 * @description: Simple Embed Constructor
 */

/**
 * FOR DISCORD.JS FOLLOW THESE STEPS:
 * CRTL + F
 * FIND: $this;
 * REPLACE: return $this->embed;
 * RETURN -> SAVE
 */

class SimpleEmbed {
    public function constructor() {
        $this->embed = [];
    }

    /**
     * 
     * @param {String} title - Short, bold text of embed
     * @param {String} description - Large text of embed
     * @param {Boolean} inline - Should discord try to "stack" fields?
     * 
     */

    public function addField($title=null, $description=null, $inline = false) {
        if (!$title) $title = 'No title provided';
        if (!$description) $description = 'No description provided';
        if (strlen($title) >= 256) return 'You need a shorter title';
        if (strlen($description) >= 1024) return 'You need a shorter description';

        $temp = [
            'name' => $title,
            'value' => $description,
            'inline' => $inline
        ];

        if (!$this->embed->fields) $this->embed->fields = [];
        array_push($this->embed->fields, $temp);
        return $this;
    }

    /**
     * 
     * @param {String} title - Large title of embed (first one)
     * 
     */

    public function setTitle(String $title) {
        if (strlen($title) >= 256) return 'You need a shorter title.';
        $this->embed->title = $title;
        return $this;
    }

    /**
     * 
     * @param {String} desc - Large text of embed (first one, not bold)
     * 
     */

    public function setDescription(String $desc) {
        //2048 chars
        if (strlen($desc) >= 2048) return 'You need a shorter description';
        $this->embed->description = desc;
        $this;
    }

    /**
     * 
     * @param {Number|String} val - Color of the embed
     * 
     */

    public function setColor($val) {
        if (!$this->embed->color) $this->embed->color = 0xffffff;
        if (!$val instanceof String) $this->embed->color = $val;
        else {
            $str = explode("#", $val)[1];
            if (!(int)(`0x${str}`)) return 'Color must be HEX or INTEGER.';
            else $this->embed->color = (int) (`0x${str}`);
        }
        $this;
    }

    /**
     * @param {String} str - Message 
     */

    public function setContent($str) {
        $this->content = $str;
        $this;
    }

    /**
     * 
     * @param {String} url - URL or file directory
     * @param {Number} height - height in px (pixels)
     * @param {Number} width - width in px (pixels)
     * 
     */

    public function setImage(url, height, width) {
        let temp = {};
        if (!url) return 'Invalid Image.';
        else temp.url = url;
        if (height && parseInt(height)) temp.height = height;
        if (height && parseInt(width)) temp.width = width;
        $this->embed.image = temp;
        $this;
    }

    /**
     * 
     * @param {String} url - URL or file directory
     * @param {Number} height - height in px (pixels)
     * @param {Number} width - width in px (pixels)
     * 
     */

    public function setVideo(url, height, width) {
        let temp = {};
        if (!url) return 'Invalid Video.';
        else temp.url = url;
        if (height && parseInt(height)) temp.height = height;
        if (height && parseInt(width)) temp.width = width;
        $this->embed.video = temp;
        $this;
    }

    /**
     * 
     * @param {String} url - URL or file directory
     * @param {Number} height - height in px (pixels)
     * @param {Number} width - width in px (pixels)
     * 
     */

    public function setThumbnail(url, height, width) {
        let temp = {};
        if (!url) return 'Invalid Image.';
        else temp.url = url;
        if (height && parseInt(height)) temp.height = height;
        if (height && parseInt(width)) temp.width = width;
        $this->embed.thumbnail = temp;
        $this;
    }

    /**
     * 
     * @param {Date} date - Javascript new Date() object. Appears left of the footer.
     * 
     */

    public function setTimestamp(date) {
        if (!date) $this;
        else {
            $this->embed.timestamp = date;
            $this;
        }
    }

    /**
     * 
     * @param {String} name - Text on top left
     * @param {String} url - Icon to left
     * @param {String} iconURL - Icon to right
     *  
     */

    public function setAuthor(name, iconURL, url) {
        let temp = {};
        if (name) temp.name = name;
        if (url) temp.url = url;
        if (iconURL) temp.icon_url = iconURL;
        if (Object.keys(temp).length > 0) $this->embed.author = temp;
        $this;
    }

    /**
     * 
     * @param {String} url - URL or File directory
     *  
     */

    public function setUrl(url) {
        if (!url) $this;
        else {
            $this->embed.url = url;
            $this;
        }
    }

    /**
     * 
     * @param {String} text - Text for embed footer
     * @param {String} url - URL or file directory
     *  
     */

    public function setFooter(text, url) {
        let temp = {};
        if (!text) return 'You need a text string.';
        if (text.length >= 2048) text = text.split(text[2047][0]);
        if (url) temp.icon_url = url;
        temp.text = text;
        $this->embed.footer = temp;
        $this
    }

    /**
     * @returns Boolean
     */

    public function hasVideo() {
        return (!$this->embed.video) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasTitle() {
        return (!$this->embed.title) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasDescription() {
        return (!$this->embed.description) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasFooter() {
        return (!$this->embed.footer) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasImage() {
        return (!$this->embed.image) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasTimestamp() {
        return (!$this->embed.timestamp) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasAuthor() {
        return (!$this->embed.author) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasFields() {
        return (!$this->embed.fields) ? false : true;
    }

    /**
     * @returns Boolean
     */

    public function hasColor() {
        return (!$this->embed.color) ? false : true;
    }

    /**
     * @param {Number} val - Key to check
     * @returns Boolean
     */

    public function hasField($val) {
        $val -= 1;
        if (!$this->embed.fields) false;
        else return (!$this->embed.fields[val]) ? false : true;
    }
}

module.exports = SimpleEmbed;