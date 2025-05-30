<?php
/**
 * Elementor default widgets enhancements
 *
 * @package Happy_Addons
 */
namespace Happy_Addons\Elementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Happy_Addons\Elementor\Controls\Group_Control_Text_Stroke;

defined('ABSPATH') || die();

class Text_Stroke {

	private static $instance = null;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		 return self::$instance;
	}

	public static function add_text_stroke_old( Widget_Base $widget ) {
		$common = [
			'of'     => 'blend_mode',
			'target' => '.elementor-heading-title',
		];

		$map = [
			'heading'                   => $common,
			'theme-page-title'          => $common,
			'theme-site-title'          => $common,
			'theme-post-title'          => $common,
			'woocommerce-product-title' => $common,
			'animated-headline'         => [
				'of'     => 'title_color',
				'target' => '.elementor-headline',
			],
			'ha-gradient-heading'       => [
				'of'     => 'blend_mode',
				'target' => '.ha-gradient-heading',
			],
		];

		$of     = $map[ $widget->get_name() ]['of'];
		$target = $map[ $widget->get_name() ]['target'];

		if ( 'ha-gradient-heading' != $widget->get_name() ) {
			$widget->update_control(
				$of,
				[
					'control_type' => 'content',
				]
			);
		}

		$widget->start_injection( [
			'at' => 'after',
			'of' => $of,
		] );

		$widget->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'text_stroke',
				'selector' => '{{WRAPPER}} ' . $target,
			]
		);

		$widget->end_injection();
	}

	public static function add_text_stroke( Widget_Base $widget ) {
		$common = [
			'target' => '.elementor-heading-title',
		];

		$map = [
			'heading'                   => $common,
			'theme-page-title'          => $common,
			'theme-site-title'          => $common,
			'theme-post-title'          => $common,
			'woocommerce-product-title' => $common,
			'animated-headline'         => [
				'target' => '.elementor-headline',
			],
			'ha-gradient-heading'       => [
				'target' => '.ha-gradient-heading',
			],
		];

		$target = $map[ $widget->get_name() ]['target'];

		if ( 'animated-headline' == $widget->get_name() ) {
			$widget->add_control(
				'ha_text_stroke_heading',
				[
					'label' => esc_html__( 'Whole Text', 'happy-elementor-addons' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);
		}

		$widget->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'text_stroke',
				'selector' => '{{WRAPPER}} ' . $target,
			]
		);
	}

	public static function add_button_controls( Widget_Base $widget ) {
		$widget->add_control(
			'ha_fixed_size_toggle',
			[
				'label' => __( 'Fixed Size', 'happy-elementor-addons' ) . '<i style="margin-left: 5px;" class="hm hm-happyaddons"></i>',
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$widget->start_popover();

		$widget->add_responsive_control(
			'ha_height',
			[
				'label' => __( 'Height', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 500,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'ha_fixed_size_toggle' => 'yes',
				]
			]
		);

		$widget->add_responsive_control(
			'ha_width',
			[
				'label' => __( 'Width', 'happy-elementor-addons' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 500,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'width: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'ha_fixed_size_toggle' => 'yes',
				]
			]
		);

		$widget->add_control(
			'ha_align_x',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Horizontal Align', 'happy-elementor-addons' ),
				'default' => 'center',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' =>  __( 'Left', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' =>  __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' =>  __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => '{{VALUE}}'
				],
				'selectors_dictionary' => [
					'left' => '-webkit-box-pack: start; -ms-flex-pack: start; justify-content: flex-start;',
					'center' => '-webkit-box-pack: center; -ms-flex-pack: center; justify-content: center;',
					'right' => '-webkit-box-pack: end; -ms-flex-pack: end; justify-content: flex-end;',
				],
				'condition' => [
					'ha_fixed_size_toggle' => 'yes',
				],
			]
		);

		$widget->add_control(
			'ha_align_y',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Vertical Align', 'happy-elementor-addons' ),
				'default' => 'center',
				'toggle' => false,
				'options' => [
					'top' => [
						'title' =>  __( 'Top', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' =>  __( 'Center', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' =>  __( 'Right', 'happy-elementor-addons' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'top' => '-webkit-box-align: start; -ms-flex-align: start; align-items: flex-start;',
					'center' => '-webkit-box-align: center; -ms-flex-align: center; align-items: center;',
					'bottom' => '-webkit-box-align: end; -ms-flex-align: end; align-items: flex-end;',
				],
				'condition' => [
					'ha_fixed_size_toggle' => 'yes',
				],
			]
		);

		$widget->add_control(
			'ha_flex_display',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'inline-flex',
				'selectors' => [
					'{{WRAPPER}} .elementor-button' => 'display: -webkit-inline-box; display: -ms-inline-flexbox; display: inline-flex;',
				],
				'condition' => [
					'ha_fixed_size_toggle' => 'yes',
					'ha_align_x!' => '',
					'ha_align_y!' => '',
				],
			]
		);

		$widget->end_popover();
	}
}
