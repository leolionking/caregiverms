<?php

namespace CMS\Models;

if (!defined('ABSPATH')) {
    exit;
}

class Languages
{
    /**
     * Get all languages from the database.
     *
     * @return array List of language objects.
     */
    public static function get_all()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_languages';

        // Fetch all languages ordered alphabetically.
        $results = $wpdb->get_results("SELECT id, name FROM $table_name ORDER BY name ASC");

        return $results ? $results : [];
    }

    /**
     * Add a new language to the database.
     *
     * @param string $name The name of the language.
     * @return int|false Inserted language ID on success, false on failure.
     */
    public static function add_language($name)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_languages';

        $inserted = $wpdb->insert(
            $table_name,
            ['name' => sanitize_text_field($name)],
            ['%s']
        );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Delete a language by its ID.
     *
     * @param int $id The ID of the language to delete.
     * @return bool True on success, false on failure.
     */
    public static function delete_language($id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_languages';

        $deleted = $wpdb->delete($table_name, ['id' => intval($id)], ['%d']);

        return (bool) $deleted;
    }

    /**
     * Update an existing language by its ID.
     *
     * @param int $id The ID of the language to update.
     * @param string $name The new name of the language.
     * @return bool True on success, false on failure.
     */
    public static function update_language($id, $name)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cms_languages';

        $updated = $wpdb->update(
            $table_name,
            ['name' => sanitize_text_field($name)],
            ['id' => intval($id)],
            ['%s'],
            ['%d']
        );

        return (bool) $updated;
    }
}
