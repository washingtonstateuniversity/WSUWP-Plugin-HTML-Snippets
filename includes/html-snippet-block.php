<?php namespace WSUWP\Plugin\HTML_Snippets;

class Block_WSUWP_HTML_Snippet
{

    protected static $block_name    = 'wsuwp/html-snippet';
    protected static $default_attrs = array(
        'className' => '',
        'snippet_id' => '',
    );


    public static function render( $attrs, $content = '' )
    {

        if ('' === $attrs['snippet_id']) {
            return;
        }

        $content = apply_filters('the_content', get_the_content(null, false, $attrs['snippet_id']));

        ob_start();
        
        include plugin_dir_path(__DIR__) . '/templates/default.php';

        return ob_get_clean();

    }


    public static function register_block()
    {

        register_block_type(
            self::$block_name,
            array(
            'render_callback' => array( __CLASS__, 'render' ),
            'api_version'     => 2,
            'editor_script'   => 'wsuwp-plugin-html-snippets-editor-scripts',
            'editor_style'    => 'wsuwp-plugin-html-snippets-editor-styles',
            )
        );

    }


    public static function init()
    {

        add_action('init', __CLASS__ . '::register_block');

    }

}

Block_WSUWP_HTML_Snippet::init();
