<?php

/**
 * Register custom post type
 *
 * @link       http://joe.szalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Portfolio
 * @subpackage Exopite_Portfolio/includes
 */
class Niu_range_calculator_Post_Types {

    /**
     * Register custom post type
     *
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     */
    private function register_single_post_type( $fields ) {

        /**
		 * Labels used when displaying the posts in the admin and sometimes on the front end.  These
		 * labels do not cover post updated, error, and related messages.  You'll need to filter the
		 * 'post_updated_messages' hook to customize those.
		 */
        $labels = array(
            'name'                  => $fields['plural'],
            'singular_name'         => $fields['singular'],
            'menu_name'             => $fields['menu_name'],
            'new_item'              => sprintf( __( 'New %s', 'plugin-name' ), $fields['singular'] ),
            'add_new_item'          => sprintf( __( 'Add new %s', 'plugin-name' ), $fields['singular'] ),
            'edit_item'             => sprintf( __( 'Edit %s', 'plugin-name' ), $fields['singular'] ),
            'view_item'             => sprintf( __( 'View %s', 'plugin-name' ), $fields['singular'] ),
            'view_items'            => sprintf( __( 'View %s', 'plugin-name' ), $fields['plural'] ),
            'search_items'          => sprintf( __( 'Search %s', 'plugin-name' ), $fields['plural'] ),
            'not_found'             => sprintf( __( 'No %s found', 'plugin-name' ), strtolower( $fields['plural'] ) ),
            'not_found_in_trash'    => sprintf( __( 'No %s found in trash', 'plugin-name' ), strtolower( $fields['plural'] ) ),
            'all_items'             => sprintf( __( 'All %s', 'plugin-name' ), $fields['plural'] ),
            'archives'              => sprintf( __( '%s Archives', 'plugin-name' ), $fields['singular'] ),
            'attributes'            => sprintf( __( '%s Attributes', 'plugin-name' ), $fields['singular'] ),
            'insert_into_item'      => sprintf( __( 'Insert into %s', 'plugin-name' ), strtolower( $fields['singular'] ) ),
            'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'plugin-name' ), strtolower( $fields['singular'] ) ),

            /* Labels for hierarchical post types only. */
            'parent_item'           => sprintf( __( 'Parent %s', 'plugin-name' ), $fields['singular'] ),
            'parent_item_colon'     => sprintf( __( 'Parent %s:', 'plugin-name' ), $fields['singular'] ),

            /* Custom archive label.  Must filter 'post_type_archive_title' to use. */
			'archive_title'        => $fields['plural'],
        );

        $args = array(
            'labels'             => $labels,
            'description'        => ( isset( $fields['description'] ) ) ? $fields['description'] : '',
            'public'             => ( isset( $fields['public'] ) ) ? $fields['public'] : true,
            'publicly_queryable' => false,
            'exclude_from_search'=> ( isset( $fields['exclude_from_search'] ) ) ? $fields['exclude_from_search'] : false,
            'show_ui'            => ( isset( $fields['show_ui'] ) ) ? $fields['show_ui'] : true,
            'show_in_menu'       => ( isset( $fields['show_in_menu'] ) ) ? $fields['show_in_menu'] : true,
            'query_var'          => ( isset( $fields['query_var'] ) ) ? $fields['query_var'] : true,
            'show_in_admin_bar'  => ( isset( $fields['show_in_admin_bar'] ) ) ? $fields['show_in_admin_bar'] : true,
            'capability_type'    => ( isset( $fields['capability_type'] ) ) ? $fields['capability_type'] : 'post',
            'has_archive'        => ( isset( $fields['has_archive'] ) ) ? $fields['has_archive'] : true,
            'hierarchical'       => ( isset( $fields['hierarchical'] ) ) ? $fields['hierarchical'] : true,
            'supports'           => ( isset( $fields['supports'] ) ) ? $fields['supports'] : array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
            ),
            'menu_position'      => ( isset( $fields['menu_position'] ) ) ? $fields['menu_position'] : 21,
            'menu_icon'          => ( isset( $fields['menu_icon'] ) ) ? $fields['menu_icon']: 'dashicons-admin-generic',
            'show_in_nav_menus'  => ( isset( $fields['show_in_nav_menus'] ) ) ? $fields['show_in_nav_menus'] : true,
        );

        if ( isset( $fields['rewrite'] ) ) {

            /**
             *  Add $this->plugin_name as translatable in the permalink structure,
             *  to avoid conflicts with other plugins which may use customers as well.
             */
            $args['rewrite'] = $fields['rewrite'];
        }

        if ( $fields['custom_caps'] ) {

            /**
             * Provides more precise control over the capabilities than the defaults.  By default, WordPress
             * will use the 'capability_type' argument to build these capabilities.  More often than not,
             * this results in many extra capabilities that you probably don't need.  The following is how
             * I set up capabilities for many post types, which only uses three basic capabilities you need
             * to assign to roles: 'manage_examples', 'edit_examples', 'create_examples'.  Each post type
             * is unique though, so you'll want to adjust it to fit your needs.
             *
             * @link https://gist.github.com/creativembers/6577149
             * @link http://justintadlock.com/archives/2010/07/10/meta-capabilities-for-custom-post-types
             */
            $args['capabilities'] = array(

                // Meta capabilities
                'edit_post'                 => 'edit_' . strtolower( $fields['singular'] ),
                'read_post'                 => 'read_' . strtolower( $fields['singular'] ),
                'delete_post'               => 'delete_' . strtolower( $fields['singular'] ),

                // Primitive capabilities used outside of map_meta_cap():
                'edit_posts'                => 'edit_' . strtolower( $fields['plural'] ),
                'edit_others_posts'         => 'edit_others_' . strtolower( $fields['plural'] ),
                'publish_posts'             => 'publish_' . strtolower( $fields['plural'] ),
                'read_private_posts'        => 'read_private_' . strtolower( $fields['plural'] ),

                // Primitive capabilities used within map_meta_cap():
                'delete_posts'              => 'delete_' . strtolower( $fields['plural'] ),
                'delete_private_posts'      => 'delete_private_' . strtolower( $fields['plural'] ),
                'delete_published_posts'    => 'delete_published_' . strtolower( $fields['plural'] ),
                'delete_others_posts'       => 'delete_others_' . strtolower( $fields['plural'] ),
                'edit_private_posts'        => 'edit_private_' . strtolower( $fields['plural'] ),
                'edit_published_posts'      => 'edit_published_' . strtolower( $fields['plural'] ),
                'create_posts'              => 'edit_' . strtolower( $fields['plural'] )

            );

            /**
             * Adding map_meta_cap will map the meta correctly.
             * @link https://wordpress.stackexchange.com/questions/108338/capabilities-and-custom-post-types/108375#108375
             */
            $args['map_meta_cap'] = true;

            /**
             * Assign capabilities to users
             * Without this, users - also admins - can not see post type.
             */
            $this->assign_capabilities( $args['capabilities'], $fields['custom_caps_users'] );
        }

        register_post_type( $fields['slug'], $args );

    }

    /**
     * Assign capabilities to users
     *
     * @link https://codex.wordpress.org/Function_Reference/register_post_type
     * @link https://typerocket.com/ultimate-guide-to-custom-post-types-in-wordpress/
     */
    public function assign_capabilities( $caps_map, $users  ) {

        foreach ( $users as $user ) {

            $user_role = get_role( $user );

            foreach ( $caps_map as $cap_map_key => $capability ) {

                $user_role->add_cap( $capability );

            }

        }

    }

    /**
     * CUSTOMIZE CUSTOM POST TYPE AS YOU WISH.
     */

    /**
     * Create post types
     */
    public function create_custom_post_type() {

        /**
         * This is not all the fields, only what I find important. Feel free to change this function ;)
         *
         * @link https://codex.wordpress.org/Function_Reference/register_post_type
         *
         * For more info on fields:
         * @link https://github.com/JoeSz/WordPress-Plugin-Boilerplate-Tutorial/blob/9fb56794bc1f8aebfe04e99b15881db0c4bc61bd/plugin-name/includes/class-plugin-name-post_types.php#L230
         */
        $post_types_fields = array(
            array(
                'slug'                  => 'scooters',
                'singular'              => 'Scooter',
                'plural'                => 'Scooters',
                'menu_name'             => 'Scooters',
                'description'           => 'Scooters',
                'has_archive'           => true,
                'hierarchical'          => false,
                'menu_icon'             => 'dashicons-tag',
                'rewrite' => array(
                    'slug'                  => 'scooters',
                    'with_front'            => true,
                    'pages'                 => true,
                    'feeds'                 => true,
                    'ep_mask'               => EP_PERMALINK,
                ),
                'menu_position'         => 21,
                'public'                => true,
                'publicly_queryable'    => true,
                'exclude_from_search'   => true,
                'show_ui'               => true,
                'show_in_menu'          => true,
                'query_var'             => true,
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'supports'              => array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'thumbnail',
                    'comments',
                    'trackbacks',
                    'custom-fields',
                    'revisions',
                    'page-attributes',
                    'post-formats',
                ),
                'custom_caps'           => true,
                'custom_caps_users'     => array(
                    'administrator',
                ),
                'taxonomies'            => array(
                    array(
                        'taxonomy'          => 'scooter_modes',
                        'plural'            => 'Scooter Modes',
                        'single'            => 'Scooter Mode',
                        'post_types'        => array( 'scooters' ),
                    ),

                ),
            ),
        );

        foreach ( $post_types_fields as $fields ) {

            $this->register_single_post_type( $fields );

        }

    }
}
?>