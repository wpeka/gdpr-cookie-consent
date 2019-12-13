<?php
/**
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @since       1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @var array    $VARS
 * @var Analytics $as
 */
$as   = analytics( $VARS['id'], $VARS['slug'], $VARS['product_name'], $VARS['version'], $VARS['module_type'] );
$slug = $as->get_slug();

$as->_enqueue_connect_essentials();

$current_user = Analytics::_get_current_wp_user();

$first_name = $current_user->user_firstname;
if ( empty( $first_name ) ) {
    $first_name = $current_user->nickname;
}

$site_url     = get_site_url();
$protocol_pos = strpos( $site_url, '://' );
if ( false !== $protocol_pos ) {
    $site_url = substr( $site_url, $protocol_pos + 3 );
}

$analytics_usage_tracking_url = 'ccreadysites.cyberchimps.com';

$analytics_site_url = $analytics_usage_tracking_url;

$analytics_link = '<a href="' . $analytics_site_url . '" target="_blank" tabindex="1">ccreadysites.cyberchimps.com</a>';

$error = as_request_get( 'error' );

$is_optin_dialog = (
    $as->is_theme() &&
    $as->is_themes_page()
);

if ( $is_optin_dialog ) {
    $previous_theme_activation_url = '';
    $show_close_button = false;
}

$as_user = get_user_by( 'email', $current_user->user_email );

$activate_with_current_user = (
    is_object( $as_user )
);

//$optin_params = $as->get_opt_in_params( array(), false );
//$sites        = isset( $optin_params['sites'] ) ? $optin_params['sites'] : array();

/* translators: %s: name (e.g. Hey John,) */
$hey_x_text = esc_html( sprintf( as_text_x_inline( 'Hey %s,', 'greeting', 'hey-x', $slug ), $first_name ) );

?>
<?php
if ( $is_optin_dialog ) { ?>
<div id="as_theme_connect_wrapper">
    <?php
    if ( $show_close_button ) { ?>
        <button class="close dashicons dashicons-no"><span class="screen-reader-text">Close connect dialog</span>
        </button>
        <?php
    }
    ?>
    <?php
    }
    ?>
    <div id="as_connect"
         class="wrap">
        <div class="as-visual">
            <b class="as-site-icon"><i class="dashicons dashicons-wordpress"></i></b>
        </div>
        <div class="as-content">
            <?php if ( ! empty( $error ) ) : ?>
                <p class="as-error"><?php echo esc_html( $error ) ?></p>
            <?php endif ?>
            <p><?php
                $button_label = as_text_inline( 'Allow & Continue', 'opt-in-connect', $slug );
                $message = '';

                        $default_optin_message = as_text_inline( 'Never miss an important update - opt in to our security & feature updates notifications, and non-sensitive diagnostic tracking. If you skip this, that\'s okay! %1$s will still work just fine.', 'connect-message_on-update', $slug );

                        $filter = 'connect_message_on_update';
                    $message = $as->apply_filters(
                        $filter,
                        ( /* translators: %s: name (e.g. Hey John,) */
                            $hey_x_text . '<br>'
                        ) .
                        sprintf(
                            esc_html( $default_optin_message ),
                            '<b>' . esc_html( $as->get_plugin_name() ) . '</b>',
                            '<b>' . $current_user->user_login . '</b>',
                            '<a href="' . $site_url . '" target="_blank">' . $site_url . '</a>',
                            $analytics_link
                        ),
                        $first_name,
                        $as->get_plugin_name(),
                        $current_user->user_login,
                        '<a href="' . $site_url . '" target="_blank">' . $site_url . '</a>',
                        $analytics_link,
                        false
                    );

                    //need to change
                echo  $message;
                ?></p>
        </div>
        <div class="as-actions">
            <?php if ( $activate_with_current_user ) : ?>
                <form action="" method="POST">
                    <input type="hidden" name="as_action"
                           value="<?php echo $as->get_unique_affix() ?>_activate_existing">
                    <button class="button button-primary" tabindex="1"
                            type="submit"><?php echo esc_html( $button_label ) ?></button>
                </form>
            <?php endif ?>
        </div><?php

        // Set core permission list items.
        $permissions = array(
            'profile' => array(
                'icon-class' => 'dashicons dashicons-admin-users',
                'label'      => $as->get_text_inline( 'Profile', 'permissions-profile' ),
                'desc'       => $as->get_text_inline( 'Name and email address', 'permissions-profile_desc' ),
                'priority'   => 5,
            ),
            'site'    => array(
                'icon-class' => 'dashicons dashicons-admin-settings',
                'label'      => $as->get_text_inline( 'WebSite', 'permissions-site' ),
                'desc'       => $as->get_text_inline( 'Site URL, WP version, PHP info', 'permissions-site_desc' ),
                'priority'   => 10,
            ),
            'events'  => array(
                'icon-class' => 'dashicons dashicons-admin-plugins',
                'label'      => sprintf( $as->get_text_inline( 'Events', 'permissions-events' ), ucfirst( 'free' ) ),
                'desc'       => $as->get_text_inline( 'Deactivation', 'permissions-events_desc' ),
                'priority'   => 20,
            ),
        );

        // Allow filtering of the permissions list.
        $permissions = $as->apply_filters( 'permission_list', $permissions );

        // Sort by priority.
        uasort( $permissions, 'as_sort_by_priority' );

        if ( ! empty( $permissions ) ) : ?>
            <div class="as-permissions">
                <a class="as-trigger" href="#" tabindex="1"><?php as_esc_html_echo_inline( 'What permissions are being granted?', 'what-permissions', $slug ) ?></a>
                <ul><?php
                    foreach ( $permissions as $id => $permission ) : ?>
                        <li id="as-permission-<?php echo esc_attr( $id ); ?>"
                            class="as-permission as-<?php echo esc_attr( $id ); ?>">
                            <i class="<?php echo esc_attr( $permission['icon-class'] ); ?>"></i>

                            <div>
                                <span><?php echo esc_html( $permission['label'] ); ?></span>

                                <p><?php echo esc_html( $permission['desc'] ); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif ?>
        <div class="as-terms">
            <a href="" target="_blank"
               tabindex="1"><?php as_esc_html_echo_inline( 'Privacy Policy', 'privacy-policy', $slug ) ?></a>
            &nbsp;&nbsp;-&nbsp;&nbsp;
            <a href="" target="_blank" tabindex="1"><?php as_echo_inline( 'Terms of Service', 'tos', $slug ) ?></a>
        </div>
    </div>
    <?php
    if ( $is_optin_dialog ) { ?>
</div>
<?php
}
?>
<script type="text/javascript">
    (function ($) {
        var $html = $('html');

        $html.attr('as-optin-overflow', $html.css('overflow'));

        var $primaryCta          = $('.as-actions .button.button-primary'),
            $form                = $('.as-actions form'),

            setLoadingMode = function () {
                $(document.body).css({'cursor': 'wait'});
            };

        $('.as-actions .button').on('click', function () {
            setLoadingMode();
        });


        $form.on('submit', function () {
            var action   = null,
                security = null;

            $('.as-error').remove();

            /**
             * Use the AJAX opt-in when license key is required to potentially
             * process the after install failure hook.
             *
             */
            var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
            $.ajax(
                {
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'set-user-consent'
                    },
                }
            )
                .done(
                    function ( data ){
                        window.location.href = "<?php echo esc_url( admin_url() ) ?>";
                    }
                );
        });

        $primaryCta.on('click', function () {
            $(this).addClass('as-loading');
            $(this).html('<?php echo esc_js(
                as_text_x_inline( 'Allowing', 'user permissions', 'activating', $slug )
            ) ?>...');
        });

        $('.as-permissions .as-trigger').on('click', function () {
            $('.as-permissions').toggleClass('as-open');

            return false;
        });
    })(jQuery);
</script>