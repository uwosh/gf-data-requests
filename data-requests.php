<?php
/*
Plugin Name: UWO Gravity Forms Data Requests Add-On
Plugin URI: https://uwosh.edu
Description: A Jira integration to allow Gravity Forms to interface with our Data Requests project.
Version: 0.1
Author: Joseph Kerkhof
Author URI: https://twitter.com/musicaljoeker
*/

define( 'UWO_GF_DATA_REQUESTS_ADDON_VERSION', '0.1' );

add_action( 'gform_loaded', array( 'GF_Data_Requests_AddOn_Bootstrap', 'load' ), 5 );

class GF_Data_Requests_AddOn_Bootstrap {
    public static function load() {
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }
        require_once( 'class-gfdatarequestsaddon.php' );
        GFAddOn::register( 'GFDataRequestsAddOn' );
    }
}

function gf_data_requests_addon() {
    return GFDataRequestsAddOn::get_instance();
}