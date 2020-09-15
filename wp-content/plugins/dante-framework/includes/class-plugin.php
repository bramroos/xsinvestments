<?php

/**
 * The main plugin class.
 */
class DanteFramework_Plugin
{

    private $loader;
    private $plugin_slug;
    private $version;
    private $option_name;

    public function __construct() {
        $this->plugin_slug = DanteFramework_Info::SLUG;
        $this->version     = DanteFramework_Info::VERSION;
        $this->option_name = DanteFramework_Info::OPTION_NAME;
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_frontend_hooks();
    }

    private function load_dependencies() {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'frontend/class-frontend.php';


         /* ASSET FUNCTIONS
        ================================================== */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/asset-functions.php';


        /* CUSTOM POST TYPES
        ================================================== */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/portfolio-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/team-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/clients-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/gallery-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/testimonials-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/jobs-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/faqs-type.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/custom-post-types/sf-post-type-permalinks.php' ;


         /* WIDGETS
        ================================================== */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-twitter.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-flickr.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-video.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-posts.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-portfolio.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-portfolio-grid.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-advertgrid.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-infocus.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/widgets/widget-loveit.php';


        /* SWIFT PAGE BUILDER
        ================================================== */ 
        $disable_gutenberg = true;
        $options = get_option('sf_dante_options');
        if (isset($options['disable_gutenberg'])) {
            $disable_gutenberg = $options['disable_gutenberg'];
        }
        if ( $disable_gutenberg ) {
            add_filter('use_block_editor_for_post', '__return_false');
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/page-builder/sf-page-builder.php';


        /* META BOX FRAMEWORK
        ================================================== */ 
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/meta-boxes.php';


        /* SHORTCODES
        ================================================== */  
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/shortcodes.php';

        $this->loader = new DanteFramework_Loader();
    }

    private function define_admin_hooks() {
        $plugin_admin = new DanteFramework_Admin($this->plugin_slug, $this->version, $this->option_name);
        // $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'assets');
        // $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
        // $this->loader->add_action('admin_menu', $plugin_admin, 'add_menus');
        $this->loader->add_action( 'add_meta_boxes', $this, 'register_meta_box_holder' );
    }

    private function define_frontend_hooks() {
        $plugin_frontend = new DanteFramework_Frontend($this->plugin_slug, $this->version, $this->option_name);
        // $this->loader->add_action('wp_enqueue_scripts', $plugin_frontend, 'assets');
        // $this->loader->add_action('wp_footer', $plugin_frontend, 'render');
    }

    public function register_meta_box_holder() {
        add_meta_box( 'dante_meta_box', __( 'Meta Options', 'swift-framework-plugin' ), array( $this, 'dante_build_meta_box_tabs' ), '', 'normal', 'high' );
    }

    public function dante_build_meta_box_tabs() {
        echo'<div class="sf-meta-tabs-wrap"><div id="sf-tabbed-meta-boxes"></div></div>';
    }

    public function run() {
        $this->loader->run();
    }
}
