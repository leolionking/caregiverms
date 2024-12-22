<?php
namespace CMS\Setup;

class Sample_Data {
    public static function insert_skills() {
        global $wpdb;

        $skills = array(
            'medical' => array(
                'Medication Administration',
                'Vital Signs Monitoring',
                'Wound Care',
                'Diabetes Management',
                'Blood Pressure Monitoring',
                'Injection Administration',
                'First Aid',
                'CPR'
            ),
            'personal_care' => array(
                'Bathing Assistance',
                'Grooming',
                'Dressing Assistance',
                'Toileting Assistance',
                'Mobility Support',
                'Transfer Assistance',
                'Feeding Assistance',
                'Personal Hygiene'
            ),
            'specialized_care' => array(
                'Dementia Care',
                'Alzheimer\'s Care',
                'Palliative Care',
                'Post-Surgery Care',
                'Stroke Recovery',
                'Cancer Care',
                'Respiratory Care',
                'Physical Therapy Support'
            ),
            'household' => array(
                'Meal Preparation',
                'Light Housekeeping',
                'Laundry',
                'Grocery Shopping',
                'Medication Reminders',
                'Transportation',
                'Companionship',
                'Exercise Assistance'
            )
        );

        foreach ($skills as $category => $skill_list) {
            foreach ($skill_list as $skill) {
                $wpdb->insert(
                    $wpdb->prefix . 'cms_skills',
                    array(
                        'name' => $skill,
                        'category' => $category
                    ),
                    array('%s', '%s')
                );
            }
        }
    }

    public static function insert_languages() {
        global $wpdb;

        $languages = array(
            array('English', 'en'),
            array('French', 'fr'),
            array('Spanish', 'es'),
            array('Mandarin', 'zh'),
            array('Cantonese', 'yue'),
            array('Hindi', 'hi'),
            array('Punjabi', 'pa'),
            array('Urdu', 'ur'),
            array('Arabic', 'ar'),
            array('Portuguese', 'pt'),
            array('Italian', 'it'),
            array('German', 'de'),
            array('Russian', 'ru'),
            array('Korean', 'ko'),
            array('Japanese', 'ja'),
            array('Vietnamese', 'vi'),
            array('Tagalog', 'tl'),
            array('Greek', 'el'),
            array('Polish', 'pl'),
            array('Ukrainian', 'uk')
        );

        foreach ($languages as $language) {
            $wpdb->insert(
                $wpdb->prefix . 'cms_languages',
                array(
                    'name' => $language[0],
                    'code' => $language[1]
                ),
                array('%s', '%s')
            );
        }
    }
}