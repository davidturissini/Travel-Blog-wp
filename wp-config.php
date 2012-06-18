<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'bytemagik_travel');

/** MySQL database username */
define('DB_USER', 'bytemagik_travel');

/** MySQL database password */
define('DB_PASSWORD', 'm3lissa');

/** MySQL hostname */
define('DB_HOST', 'mysql.travel.bytemagik.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'VVoAA4N*RvrXtS#G7W^i/gi)^uK%;/v$;0aWBnQ(%SNwZ:rli26)ZKrJxNtPqPEs');
define('SECURE_AUTH_KEY',  '9+%/cde@8%wCzOQ4M""|F@B?CH*KHqx1iOX@^~|3aww_6?XlfXiT0|Uzy69L4q(p');
define('LOGGED_IN_KEY',    'xcL`cN0kd7wFhYCbLNoIYla+p*t8o$mj#/P&xBCJcH%mDrOENYy1dnTbt_@/ddco');
define('NONCE_KEY',        'o@l&bF"0+w(zUht3_Qa^fky1JkO5wV`JVRdz&"/7Nq/26@"LcNaYjg&Yc;pJCKa#');
define('AUTH_SALT',        'qgp:I@WYtr)8hl8`Q@lLYImII6^@BH6aNV2a8yfhAKpC%$I3@hCB4Er"sq42|`5:');
define('SECURE_AUTH_SALT', 'oz9!RqGLCXgsEoIJ|0zsYImP8y+XBC^3!$sf9(ps(#Jrj&XmZ9?($m)(Noj&mBF"');
define('LOGGED_IN_SALT',   '261AFZ9D~AyBM$UL0DThP:rcBC:^F/DXwCgQZ9V%K^Rgar7(s&tZu!Fu$ztv)*ZM');
define('NONCE_SALT',       'NbrC+5Eo77xPPuX(/^)jqY0f1#hh*Z(SN9M8b8#RY`e^%W3Vwjf922u1yijwJgJy');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_w88tdj_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

