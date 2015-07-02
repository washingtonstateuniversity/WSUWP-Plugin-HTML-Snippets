<?php

class Tests_WSU_HTML_Snippets extends WP_UnitTestCase {

	function test_working_tests() {
		$this->assertEquals( 1, 1 );
	}

	public function test_html_snippet_post_type_exists() {
		$this->assertTrue( post_type_exists( WSU_HTML_Snippets::$content_type_slug ) );
	}

	public function test_html_snippet_shortcode_empty_id() {
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet]' ) );
		$post = get_post( $post_id );

		$this->assertEquals( "\n", apply_filters( 'the_content', $post->post_content ) );
	}

	public function test_html_snippet_shortcode_invalid_id() {
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet id="bad"]' ) );
		$post = get_post( $post_id );

		$this->assertEquals( "\n", apply_filters( 'the_content', $post->post_content ) );
	}

	public function test_html_snippet_shortcode_with_id() {
		$html_snippet_id = $this->factory->post->create( array( 'post_type' => 'wsu_html_snippet', 'post_content' => '<h1>A Headline</h1>' ) );
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet id=' . $html_snippet_id . ']' ) );
		$post = get_post( $post_id );

		$this->assertContains( '<h1>A Headline</h1>', apply_filters( 'the_content', $post->post_content ) );
	}

	public function test_html_snippet_shortcode_with_container() {
		$html_snippet_id = $this->factory->post->create( array( 'post_type' => 'wsu_html_snippet', 'post_content' => '<h1>A Headline</h1>' ) );
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet id=' . $html_snippet_id . ' container="div"]' ) );
		$post = get_post( $post_id );

		$this->assertContains( '<div><h1>A Headline</h1>' . "\n" . '</div>', apply_filters( 'the_content', $post->post_content ) );
	}

	public function test_html_snippet_shortcode_with_container_class() {
		$html_snippet_id = $this->factory->post->create( array( 'post_type' => 'wsu_html_snippet', 'post_content' => '<h1>A Headline</h1>' ) );
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet id=' . $html_snippet_id . ' container="div" container_class="test-class"]' ) );
		$post = get_post( $post_id );

		$this->assertContains( '<div class="test-class"><h1>A Headline</h1>' . "\n" . '</div>', apply_filters( 'the_content', $post->post_content ) );
	}

	public function test_html_snippet_shortcode_with_container_id() {
		$html_snippet_id = $this->factory->post->create( array( 'post_type' => 'wsu_html_snippet', 'post_content' => '<h1>A Headline</h1>' ) );
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet id=' . $html_snippet_id . ' container="div" container_id="container-id"]' ) );
		$post = get_post( $post_id );

		$this->assertContains( '<div id="container-id"><h1>A Headline</h1>' . "\n" . '</div>', apply_filters( 'the_content', $post->post_content ) );
	}

	/**
	 * An HTML snippet embedded from another site will need a snippet ID built from the site ID and the ID of the HTML
	 * snippet itself.
	 */
	public function test_html_snippet_shortcode_with_snippet_id() {
		$html_snippet_id = $this->factory->post->create( array( 'post_type' => 'wsu_html_snippet', 'post_content' => '<h1>A Headline</h1>' ) );
		$site_id = get_current_blog_id();

		$html_snippet_id = $site_id . '-' . $html_snippet_id;
		$post_id = $this->factory->post->create( array( 'post_content' => '[html_snippet snippet_id="' . $html_snippet_id . '"]' ) );
		$post = get_post( $post_id );

		if ( is_multisite() ) {
			$this->assertContains( '<h1>A Headline</h1>', apply_filters( 'the_content', $post->post_content ) );
		} else {
			$this->assertEquals( "\n", apply_filters( 'the_content', $post->post_content ) );
		}
	}
}