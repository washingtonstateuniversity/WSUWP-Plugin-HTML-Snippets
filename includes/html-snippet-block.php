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

        if (is_admin() || empty($attrs['snippet_id']) ) {
            return;
        }

        $args = array(
            'p' => $attrs['snippet_id'],
            'post_type' => 'wsu_html_snippet',
        );

        $the_query = new \WP_Query($args);

        ob_start();
        
        if ($the_query->have_posts() ) {

            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                
                the_content();
            }
        }
        
        // Restore original Post Data.
        wp_reset_postdata();

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

        add_filter(
            'wsu_allowed_blocks_filter', function ( $blocks ) {
                if (!in_array(self::$block_name, $blocks, true)) {
                    array_push($blocks, self::$block_name);
                }
                
                return $blocks;
            }  
        );

    }


    public static function init()
    {

        add_action('init', __CLASS__ . '::register_block');

    }

}

Block_WSUWP_HTML_Snippet::init();
