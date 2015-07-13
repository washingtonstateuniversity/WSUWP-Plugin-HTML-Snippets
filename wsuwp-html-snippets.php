<?php
/*
Plugin Name: WSU HTML Snippets
Version: 0.2.0
Description: Embed common HTML content throughout a WordPress site.
Author: washingtonstateuniversity, jeremyfelt
Author URI: https://web.wsu.edu/
Plugin URI: https://web.wsu.edu/
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
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
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

	/**
	 * Add the meta boxes used for HTML Snippets.
	 *
	 * @param string $post_type The post type of the current post being edited.
	 */
	public function add_meta_boxes( $post_type ) {
		if ( $this::$content_type_slug !== $post_type || ! is_multisite() ) {
			return;
		}

		add_meta_box( 'wsu_snippet_id', 'Snippet ID', array( $this, 'display_snippet_id_metabox' ), null, 'side', 'high' );
	}

	/**
	 * Display the snippet ID for the current HTML snippet being edited. This snippet ID can
	 * be used to embed a snippet in content throughout this site's network.
	 *
	 * @param WP_Post $post The current post being edited.
	 */
	public function display_snippet_id_metabox( $post ) {
		?>
		<p class="description">Use this ID to embed an HTML snippet in another site on this network.</p>
		<p><strong><?php echo get_current_blog_id() . '-' . $post->ID; ?></strong></p>
		<?php
	}

	/**
	 * Display an HTML Snippet shortcode given attributes.
	 *
	 * @param array $atts
	 *
	 * @return string
	 */
	public function display_html_snippet( $atts ) {
		$default_atts = array(
			'id' => 0,
			'snippet_id' => '',
			'container' => '',
			'container_class' => '',
			'container_id' => '',
		);
		$atts = wp_parse_args( $atts, $default_atts );

		// If a snippet ID has been passed, we default to parsing it. This should be a
		// string that breaks into a site ID and a post ID for the desired HTML snippet.
		// This is only supported in multisite.
		if ( is_multisite() && ! empty( $atts['snippet_id'] ) ) {
			$snippet_id = explode( '-', $atts['snippet_id'] );

			if ( 2 !== count( $snippet_id ) ) {
				return '';
			}

			$site_id = absint( $snippet_id[0] );
			$site_details = get_blog_details( $site_id );

			// The snippet must be pulled from a site on the same network.
			if ( get_current_site()->id != $site_details->site_id ) {
				return '';
			}

			$atts['id'] = absint( $snippet_id[1] );

			switch_to_blog( $site_id );
		}

		if ( ( empty( $atts['id'] ) || 0 === absint( $atts['id'] ) ) ) {
			return '';
		}

		$post = get_post( $atts['id'] );

		if ( is_multisite() && ms_is_switched() ) {
			restore_current_blog();
		}

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

			$content = $container_open .  apply_filters( 'the_content', $post->post_content ) . '</' . $atts['container'] . '>';
		} else {
			$content = apply_filters( 'the_content', $post->post_content );
		}

		if ( ! has_filter( 'the_content', 'wpautop' ) ) {
			$content = wpautop( $content );
		}

		return $content;
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
			'listItemImage' => 'dashicons-format-aside',
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
					'label'    => 'Or HTML Snippet ID',
					'attr'     => 'snippet_id',
					'type'     => 'text',
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
