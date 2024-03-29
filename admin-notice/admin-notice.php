<?php

namespace Automattic\VIP\Admin_Notice;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once __DIR__ . '/class-admin-notice.php';
require_once __DIR__ . '/class-admin-notice-controller.php';
require_once __DIR__ . '/conditions/interface-condition.php';
require_once __DIR__ . '/conditions/class-expression-condition.php';
require_once __DIR__ . '/conditions/class-date-condition.php';
require_once __DIR__ . '/conditions/class-capability-condition.php';
require_once __DIR__ . '/conditions/class-wp-version-condition.php';

$admin_notice_controller = new Admin_Notice_Controller();

add_action( 'admin_notices', [ $admin_notice_controller, 'display_notices' ] );
add_action( 'admin_enqueue_scripts', [ $admin_notice_controller, 'enqueue_scripts' ] );
add_action( 'wp_ajax_dismiss_vip_notice', [ $admin_notice_controller, 'dismiss_vip_notice' ] );
add_action( 'admin_init', [ $admin_notice_controller, 'maybe_clean_stale_dismissed_notices' ] );
add_action(
	'init',
	function() use ( $admin_notice_controller ) {
		do_action( 'vip_admin_notice_init', $admin_notice_controller );
	}
);

// PHP 8 migration nudge on non-prods
add_action(
	'vip_admin_notice_init',
	function( $admin_notice_controller ) {
		$message = 'PHP 7.4 will <a href="https://href.li/?https://www.php.net/supported-versions.php" target="_blank"> stop receiving security updates</a> on November 28, 2022. Please upgrade your application to be PHP 8.0 compatible. To learn more, please see <a href="https://lobby.vip.wordpress.com/2022/07/06/working-together-the-path-to-php-8-0/" target="_blank">the Lobby announcement</a> and <a href="https://docs.wpvip.com/how-tos/code-scanning-for-php-upgrade/">the guide on how to prepare your application for a PHP version upgrade</a>.';

		$admin_notice_controller->add(
			new Admin_Notice(
				$message,
				[
					new Expression_Condition( defined( 'VIP_GO_APP_ENVIRONMENT' ) && 'production' !== VIP_GO_APP_ENVIRONMENT ),
					new Expression_Condition( version_compare( PHP_VERSION, '8.0', '<' ) ),
				],
				'php8-migrations-2',
				'info'
			)
		);
	}
);

// Old WP version w/o pinned Jetpack version
add_action(
	'vip_admin_notice_init',
	function( $admin_notice_controller ) {
		global $wp_version;
		$message = "We've noticed that you are running WordPress {$wp_version}, which is an outdated version. This prevents you from running the latest version of Jetpack, as the current version of Jetpack only supports 5.9 and up. Please upgrade to the most recent WordPress version to use the latest features of Jetpack.";

		$admin_notice_controller->add(
			new Admin_Notice(
				$message,
				[
					new Capability_Condition( 'administrator' ),
					new Expression_Condition( version_compare( $wp_version, '5.9', '<' ) ),
					new Expression_Condition( ! defined( 'VIP_JETPACK_PINNED_VERSION' ) ),
				],
				'old-wp-versions',
				'error'
			)
		);
	}
);
