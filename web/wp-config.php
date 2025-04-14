<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_9lz4u' );

/** Database username */
define( 'DB_USER', 'wp_nx2dp' );

/** Database password */
define( 'DB_PASSWORD', 'OT7slv33g#l@hwNc' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'OpAoll|Ucm81=E<^ 9j{?*69&IIAsZ_W<8Vo0CwN#>}9LkmD^$m)}YEh5<co(8E!' );
define( 'SECURE_AUTH_KEY',  'CaA9F,cY&$LyCamz.05N,e!r}gKA]:d QD)7^#y9++Og]u5_uhAc/rW1<<#S8=lI' );
define( 'LOGGED_IN_KEY',    'z[$lf6N!yN+jgL1|tSOp~p0+N:GR;xvK]Q!>@L%Ew-8rfcW4KA!Ha8DbKOZdSa{9' );
define( 'NONCE_KEY',        '=?TH9RDO[3kuE5=$g^6^B?5qmC1K^R~g8&sj!g74zMo4f!oB/bH7p|):NK%/zYmU' );
define( 'AUTH_SALT',        'X@Vflkv`JXc9>=n7Ei,{ti[-Mv$l`k=A?qzL8z|!`.BV!6yB26P7#<BJJFI[pybc' );
define( 'SECURE_AUTH_SALT', 'xo4>EM@%_~P%4|X/($yu+<~E:,QS;|lLi8og5x>=p982=SS&?KxTq->`.~`xc~Vu' );
define( 'LOGGED_IN_SALT',   'M1Ql]0]I9(yEY8ZeF_AAKm9k>;oAx^szkU]`$_:=.w[v&[})eriqDLlaq-/v4lTb' );
define( 'NONCE_SALT',       'XK$~x{-fp7NDih:&on9^F8EhKbgr/ycg?uZj6}6F9dSOZ#riFO;2!.I8X1]NxA?R' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/** Esto hace que wordpress no se actualice automaticamente **/
/*define('AUTOMATIC_UPDATER_DISABLED', true);*/
