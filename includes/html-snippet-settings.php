<?php

class HTML_Snippet_Settings {


    public static function init() {

        add_action('admin_init', array( __CLASS__, 'add_settings' ) );

    }


    public static function add_settings() {

        // register a new setting for "reading" page
        register_setting('writing', 'html_snippet_classic_editor');

        // register a new section in the "reading" page
        add_settings_section(
            'html_snippet_settings_section',
            'HTML Snippet Settings', array( __CLASS__, 'html_snippet_settings_section_callback'),
            'writing'
        );

        // register a new field in the "wporg_settings_section" section, inside the "reading" page
        add_settings_field(
            'wporg_settings_field',
            'Use Classic Editor', array( __CLASS__, 'html_snippet_settings_field_callback'),
            'writing',
            'html_snippet_settings_section'
        );

    }

    /**
     * callback functions
     */

    // section content cb
    public static function html_snippet_settings_section_callback() {
    }

    // field content cb
    public static function html_snippet_settings_field_callback() {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option('html_snippet_classic_editor', false );
        // output the field
        ?>
        <input type="checkbox" name="html_snippet_classic_editor" <?php if ( ! empty( $setting ) ) : ?>checked="checked"<?php endif; ?>>
        <?php
    }


}

HTML_Snippet_Settings::init();


