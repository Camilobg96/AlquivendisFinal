<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'alquivendis' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'IARk0`H6s~qHdI:2?Lu=p#jHkCTnzf`X]J!`UB|DS|Z:9tNZh4,_`S_<7Zo)dG(6' );
define( 'SECURE_AUTH_KEY',  '5?Ig &>d84ARqN%LV=]MDZ.0u%UdWt#Is@jW^$A&:&g_=duo4jc`L{{2rBz`#>2I' );
define( 'LOGGED_IN_KEY',    '>.{Wc%y,+0JfLK`C=*58|Da.]wNEVw ]qnN`?zMk@<&> WkJs$SU0 ,;Gt=gQ:]p' );
define( 'NONCE_KEY',        'p/9h2VE&,!T.}jQ6%He^2|![dpPW)U0DunD,aZ1ZH$AZ#?/O:>6K%:mDDYKND5}J' );
define( 'AUTH_SALT',        'UA.r8alu[Bo(y(;ed9^MTMxTt|+OVhiQPz-,I9Py;)u~WGYg@w]A&Iat,CxyMau8' );
define( 'SECURE_AUTH_SALT', 'on},5<{,G$BP]) KwdTQ93w@tNJnp?s|N+[T~cSu8fXh78bbdrpJhMKSsGkOGneX' );
define( 'LOGGED_IN_SALT',   '=L/`D%62uC?`1k#bV=ZYL`jYfzq{rV:qT7,bW]6V k8>B.~Orh@,[u~2?(Yoe4-A' );
define( 'NONCE_SALT',       ']yeRi)7Z`b9ZVE;<B#1,r.E}Wp:G3Hes`MYRELkAc<LphoU(k@2SA-CvOo,QTJ}]' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
