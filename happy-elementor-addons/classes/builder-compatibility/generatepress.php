<?php
namespace Happy_Addons\Elementor\Classes\Builder_Compatibility;

use Happy_Addons\Elementor\Classes\Theme_Builder;

defined( 'ABSPATH' ) || exit;

/**
 * GeneratePress theme compatibility.
 */
class Generatepress {

	/**
	 * Instance of Elementor Frontend class.
	 *
	 * @var \Elementor\Frontend()
	 */
	private $elementor;

	private $header;
	private $footer;

	/**
	 * Run all the Actions / Filters.
	 */
	function __construct($template_ids) {
		$this->header = $template_ids[0];
		$this->footer = $template_ids[1];

		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) {
			$this->elementor = \Elementor\Plugin::instance();
		}

		if($this->header != null){
			add_action( 'template_redirect', array( $this, 'remove_theme_header_markup' ), 10 );
			add_action( 'generate_header', [$this, 'add_plugin_header_markup'] );
		}

		if($this->footer != null){
			add_action( 'template_redirect', array( $this, 'remove_theme_footer_markup' ), 10 );
			add_action( 'generate_footer', [$this, 'add_plugin_footer_markup'] );
		}

	}

	// header actions
	public function remove_theme_header_markup() {
		remove_action( 'generate_header', 'generate_construct_header' );
  }

  public function add_plugin_header_markup(){
		do_action('happyaddons/template/before_header');
		echo '<div class="ekit-template-content-markup ekit-template-content-header">';
		echo Theme_Builder::render_builder_data($this->header);
		echo '</div>';
		do_action('happyaddons/template/after_header');
  }


	// footer actions
	public function remove_theme_footer_markup() {
		remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
		remove_action( 'generate_footer', 'generate_construct_footer' );
  }

  public function add_plugin_footer_markup(){
		do_action('happyaddons/template/before_footer');
		echo '<div class="ekit-template-content-markup ekit-template-content-footer">';
		echo Theme_Builder::render_builder_data($this->footer);
		echo '</div>';
		do_action('happyaddons/template/after_footer');
  }


}
