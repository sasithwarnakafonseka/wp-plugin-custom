<?php
/**
 * @link https://proficientdesigners.com/
 *
 * @package Proficient Designers Plugin
 * @subpackage pd Android FCM
 * @since 1.0.0
 * @version 1.0.0
 */

defined('ABSPATH') OR die('Sorry, you can\'t do what you want to do !!.');

class PDANDROIDFCMPushMsg {
    //notification title
    private $title;
    //notification message 
    private $message;
    //notification image url 
    private $image;
    //notification post id (customized)
    private $post_id;

    //initializing values in this constructor
    function __construct($title, $message, $image, $post_id) {
        $this->title = $title;
        $this->message = $message; 
        $this->image = empty($image) ? null : $image;
        $this->post_id = $post_id; 
    }
    
    //getting the push notification
    public function getPush() {
        $res = array();
        $res['data']['title'] = $this->title;
        $res['data']['body'] = $this->message;
        $res['data']['image'] = $this->image;
        $res['data']['post_id'] = $this->post_id;
        return $res;
    }
 
}