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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '2rGOn);0b^>?^#C&0A5qEt}qih9uE5.T?^riVfsZtj`fG24$>vJ`D0Moh/FP(#%H' );
define( 'SECURE_AUTH_KEY',  '3n@!p3x}247c-(Q?O] zS75]dm2L0@q[AZ>jf.r=(o{s+8.R2W&WvnsqTWbCCFw:' );
define( 'LOGGED_IN_KEY',    'Zyd_,5@dEY,TlL Hj7S&^.#@Tn(%`Z4_3a`&Gu_~w!p7xU?1s.FF`ME=Iz`Ej=Qr' );
define( 'NONCE_KEY',        'kUHy 4UyDo&(dK5V_)>zO(6t$Y8Aha~c?=.@Wv;`-fZ@_f_< 0qU@:)GHL+tb4h4' );
define( 'AUTH_SALT',        'rCv^Q?0#/,DawtL=ihdz-%;]M=]Ek8UA.hP]VA(e`o4T&,GjYHECQc.Ruhhds;q_' );
define( 'SECURE_AUTH_SALT', 'M>GEA*hWO8[JoR;yqA^ZthPE@?q&}y&o]HYB.BS]Qox7:4Wn|q+g;+2a:n9hj5%!' );
define( 'LOGGED_IN_SALT',   '9{Q?/k8CE6^h1x-T!EKfBxn2%UL0=MA$j8pq(c&b9Kf8<&>rmwjE<y@0@DvPU}8{' );
define( 'NONCE_SALT',       'V/Z08w [($<_fiqmi4CgIq]mq*HqUkb}CY,z.z9,7i*%_jvk@3+:|;)53@`=wW((' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
