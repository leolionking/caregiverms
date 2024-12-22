<?php

namespace CMS\Models;

if (!defined('ABSPATH')) {
    exit;
}

class Skills
{
    /**
     * Get all skills from the database.
     *
     * @return array List of skill objects.
     */
    public static function get_all()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_skills';

        // Fetch all skills from the database.
        $results = $wpdb->get_results("SELECT id, name FROM $table_name ORDER BY name ASC");

        return $results ? $results : [];
    }

    /**
     * Add a new skill to the database.
     *
     * @param string $name The name of the skill.
     * @return int|false Inserted skill ID on success, false on failure.
     */
    public static function add_skill($name)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_skills';

        $inserted = $wpdb->insert(
            $table_name,
            ['name' => sanitize_text_field($name)],
            ['%s']
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Delete a skill by its ID.
     *
     * @param int $id The ID of the skill to delete.
     * @return bool True on success, false on failure.
     */
    public static function delete_skill($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_skills';

        $deleted = $wpdb->delete($table_name, ['id' => intval($id)], ['%d']);

        return (bool) $deleted;
    }

    /**
     * Update an existing skill by its ID.
     *
     * @param int $id The ID of the skill to update.
     * @param string $name The new name of the skill.
     * @return bool True on success, false on failure.
     */
    public static function update_skill($id, $name)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_skills';

        $updated = $wpdb->update(
            $table_name,
            ['name' => sanitize_text_field($name)],
            ['id' => intval($id)],
            ['%s'],
            ['%d']
        );

        return (bool) $updated;
    }
    public static function get_categories() {
        global $wpdb;
        return $wpdb->get_col(
            "SELECT DISTINCT category 
            FROM {$wpdb->prefix}cms_skills 
            ORDER BY category"
        );
    }

    public static function get_by_category($category) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare(
            "SELECT id, name 
            FROM {$wpdb->prefix}cms_skills 
            WHERE category = %s 
            ORDER BY name",
            $category
        ));
    }
}
