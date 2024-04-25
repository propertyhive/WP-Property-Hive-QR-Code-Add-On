<?php
/**
 * Plugin Name: Property Hive QR Code Add On
 * Plugin Uri: https://wp-property-hive.com/addons/qr-code/
 * Description: Add On for Property Hive that generates a QR code for properties
 * Version: 1.0.1
 * Author: PropertyHive
 * Author URI: https://wp-property-hive.com
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PH_QR_Code' ) ) :

final class PH_QR_Code {

    /**
     * @var string
     */
    public $version = '1.0.1';

    /**
     * @var Property Hive The single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * Main Property Hive QR Code Instance
     *
     * Ensures only one instance of Property Hive QR Code is loaded or can be loaded.
     *
     * @static
     * @return Property Hive QR Code - Main instance
     */
    public static function instance() 
    {
        if ( is_null( self::$_instance ) ) 
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {

        $this->id    = 'qr-code';
        $this->label = __( 'QR Code', 'propertyhive' );

        // Define constants
        $this->define_constants();

        // Include required files
        $this->includes();

        add_action( 'admin_notices', array( $this, 'qr_code_error_notices') );

        add_filter( 'propertyhive_property_media_meta_boxes', array( $this, 'add_qr_code_meta_box' ) );
    }

    /**
     * Define PH QR Code Constants
     */
    private function define_constants() 
    {
        define( 'PH_QR_CODE_PLUGIN_FILE', __FILE__ );
        define( 'PH_QR_CODE_VERSION', $this->version );
    }

    private function includes()
    {
        //include_once( dirname( __FILE__ ) . "/includes/class-ph-qr-code-install.php" );
    }

    /**
     * Output error message if core Property Hive plugin isn't active
     */
    public function qr_code_error_notices() 
    {
        if (!is_plugin_active('propertyhive/propertyhive.php'))
        {
            $message = __( "The Property Hive plugin must be installed and activated before you can use the Property Hive QR Code add-on", 'propertyhive' );
            echo"<div class=\"error\"> <p>$message</p></div>";
        }
    }

    public function add_qr_code_meta_box( $meta_boxes )
    {
        $meta_boxes[28] = array(
            'id' => 'propertyhive-property-qr-code',
            'title' => __( 'Property QR Code', 'propertyhive' ),
            'callback' => array( $this, 'output_meta_box' ),
            'screen' => 'property',
            'context' => 'normal',
            'priority' => 'high'
        );

        return $meta_boxes;
    }

    public function output_meta_box( $post )
    {
        $qr_code_width = apply_filters( 'propertyhive_qr_code_width', 200 );
        $qr_code_height = apply_filters( 'propertyhive_qr_code_height', 200 );

        echo '<div class="propertyhive_meta_box">';
        
            echo '<div class="options_group">';
            
                echo '<div class="media_grid" id="property_qr_code_grid"><ul>';
                
                echo '<li id="qr_code">';
                    echo '<img src="https://chart.googleapis.com/chart?chs=' . $qr_code_width . 'x' . $qr_code_height . '&cht=qr&chl=' . get_permalink($post->ID) . '&choe=UTF-8" title="" style="margin:0; padding: 0; width: 100% !important;" />';
                echo '</li>';
                
                echo '</ul></div>';
    
                do_action('propertyhive_property_qr_code_fields');
               
            echo '</div>';
        
        echo '</div>';
    }
}

endif;

/**
 * Returns the main instance of PH_QR_Code to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return PH_QR_Code
 */
function PHQR() {
    return PH_QR_Code::instance();
}

$PHQR = PHQR();

if( is_admin() && file_exists(  dirname( __FILE__ ) . '/propertyhive-qr-code-update.php' ) )
{
    include_once( dirname( __FILE__ ) . '/propertyhive-qr-code-update.php' );
}