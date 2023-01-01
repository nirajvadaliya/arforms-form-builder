<?php
class arfliteelementcontroller {


	function __construct() {

		add_action( 'plugins_loaded', array( $this, 'arflite_element_widget' ) );

	}
	function arflite_element_widget() {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}
		require_once ARFLITE_CONTROLLERS_PATH . '/arflite_elm_widgets/arflite_elementor_element.php';
	}

}

