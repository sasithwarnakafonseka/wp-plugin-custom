<?php

add_action( "wp_ajax_generate_pd_fcm_api", 'generate_pd_fcm_api_cb', 10 );
add_action( "wp_ajax_nopriv_generate_pd_fcm_api", 'generate_pd_fcm_api_cb', 10 );

function generate_pd_fcm_api_cb(){

    return wp_ajax_generate_password();
}