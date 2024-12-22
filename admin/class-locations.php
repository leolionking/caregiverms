<?php
namespace CMS\Models;

class Locations {
    public static function get_provinces() {
        global $wpdb;
        return $wpdb->get_results(
            "SELECT id, name FROM {$wpdb->prefix}cms_provinces ORDER BY name ASC"
        );
    }

    public static function get_cities($province_id) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, name FROM {$wpdb->prefix}cms_cities 
            WHERE province_id = %d ORDER BY name ASC",
            $province_id
        ));
    }

    public static function add_province($name) {
        global $wpdb;
        
        return $wpdb->insert(
            $wpdb->prefix . 'cms_provinces',
            array('name' => $name),
            array('%s')
        );
    }

    public static function add_city($province_id, $name) {
        global $wpdb;
        
        return $wpdb->insert(
            $wpdb->prefix . 'cms_cities',
            array(
                'province_id' => $province_id,
                'name' => $name
            ),
            array('%d', '%s')
        );
    }

    public static function delete_province($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $wpdb->prefix . 'cms_provinces',
            array('id' => $id),
            array('%d')
        );
    }

    public static function delete_city($id) {
        global $wpdb;
        
        return $wpdb->delete(
            $wpdb->prefix . 'cms_cities',
            array('id' => $id),
            array('%d')
        );
    }
}