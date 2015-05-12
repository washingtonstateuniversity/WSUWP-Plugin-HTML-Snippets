<?php
/*
Plugin Name: WSU HTML Snippets
Version: 0.0.1
Description: Embed common HTML content throughout a WordPress site.
Author: washingtonstateuniversity, jeremyfelt
Author URI: https://web.wsu.edu/
Plugin URI: https://web.wsu.edu/
Text Domain: wsuwp-html-snippets
Domain Path: /languages
*/

class WSU_HTML_Snippets {
	/**
	 * @var string The slug used for registering the content type.
	 */
	static $content_type_slug = 'wsu_html_snippet';

	/**
	 * Setup the plugin.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_content_type' ) );
		add_action( 'init', array( $this, 'setup_shortcode_ui' ) );
		add_shortcode( 'html_snippet', array( $this, 'display_html_snippet' ) );
	}

	/**
	 * Register the content type to be used for HTML Snippets
	 */
	public function register_content_type() {
		$labels = array(
			'name' => 'HTML Snippets',
			'singular_name' => 'HTML Snippet',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New HTML Snippet',
			'edit_item' => 'Edit HTML Snippet',
			'new_item' => 'New HTML Snippet',
			'all_items' => 'All HTML Snippets',
			'view_item' => 'View HTML Snippets',
			'search_items' => 'Search HTML Snippets',
			'not_found' => 'No HTML snippets found',
			'not_found_in_trash' => 'No HTML snippets found in Trash',
			'menu_name' => 'HTML Snippets',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => false,
			'has_archive'        => false,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor' ),
		);
		register_post_type( $this::$content_type_slug, $args );
	}

	public function display_html_snippet( $atts ) {
		$default_atts = array(
			'id' => 0,
			'container' => '',
			'container_class' => '',
			'container_id' => '',
		);
		$atts = wp_parse_args( $atts, $default_atts );

		if ( empty( $atts['id'] ) ) {
			return '';
		}

		if ( 0 === absint( $atts['id'] ) ) {
			return '';
		}

		$post = get_post( $atts['id'] );

		if ( ! $post || $this::$content_type_slug !== $post->post_type ) {
			return '';
		}

		if ( in_array( $atts['container'], array( 'div', 'span' ) ) ) {
			$container_open = '<' . $atts['container'];

			if ( '' !== sanitize_key( $atts['container_class'] ) ) {
				$container_open .= ' class="' . $atts['container_class'] . '"';
			}

			if ( '' !== sanitize_key( $atts['container_id'] ) ) {
				$container_open .= ' id="' . $atts['container_id'] . '"';
			}

			$container_open .= '>';

			return $container_open .  apply_filters( 'the_content', $post->post_content ) . '</' . $atts['container'] . '>';
		}

		return apply_filters( 'the_content', $post->post_content );
	}

	/**
	 * Configure support for the HTML Snippet shortcode with Shortcode UI.
	 */
	public function setup_shortcode_ui() {
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		$args = array(
			'label'         => 'HTML Snippet',
			'listItemImage' => 'dashicons-editor-quote',
			'post_type'     => array( 'post', 'page' ),
			'attrs'         => array(
				array(
					'label'    => 'Select HTML Snippet',
					'attr'     => 'id',
					'type'     => 'post_select',
					'query'    => array( 'post_type' => $this::$content_type_slug ),
					'multiple' => false,
				),

				array(
					'label'    => 'Wrapping container',
					'attr'     => 'container',
					'type'     => 'select',
					'options'  => array(
						''     => 'None',
						'div'  => 'div',
						'span' => 'span',
					)
				),

				array(
					'label' => 'Wrapping container ID',
					'attr'  => 'container_id',
					'type'  => 'text',
				),

				array(
					'label' => 'Wrapping container class',
					'attr'  => 'container_class',
					'type'  => 'text',
				)
			),
		);
		shortcode_ui_register_for_shortcode( 'html_snippet', $args );
	}
}
new WSU_HTML_Snippets();
