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
define( 'DB_NAME', 'CCo10ryiHjLuJh' );

/** MySQL database username */
define( 'DB_USER', 'CCo10ryiHjLuJh' );

/** MySQL database password */
define( 'DB_PASSWORD', 'cPAEki10ujwHta' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '}>!w#<VYHZ.g(T}H~7vk. 8?9O@T4Fu%w&-M7/P`!|w}7RLH?kVipQdTm[ueeD$i' );
define( 'SECURE_AUTH_KEY',   '|=H*5A=Raf<WAg0^bRqU:.fD8{=fG[C1s(~|AtTab?9B$Cfj.1:7(XSrjvD9>|2R' );
define( 'LOGGED_IN_KEY',     '0h9(>Vtyd[bP:M6Bi},E3^K@t~qhpr<Xy:LFABO#q2hJIVH*:`f2,Ofq02fTe2-!' );
define( 'NONCE_KEY',         'xme4M_OUL&j63Cs.INDhad/Rb8NbkN 9/ .Jk|iQfK4RTGFM*K9crm*3u5xl+~X<' );
define( 'AUTH_SALT',         'j^qq)B]x&&x5%.^t-J>zEsf]T#kd/>>{E)g{a<[Qcn8GH$b*?GL04=DPMHAA[^x:' );
define( 'SECURE_AUTH_SALT',  '.hX)$C8<>`*mm-9K;^?2!)0>v>d`G+^u(]Kr>~gtAlCa2u`I}w*W$g?CXz.O6f=r' );
define( 'LOGGED_IN_SALT',    '_r6P4yW^-319]]:Q}*0!Z!IQ]/~IiI0VI1u6rF)bCGg<gOc$WnX}7]`qEb3>T5Xj' );
define( 'NONCE_SALT',        'J|E/=gZp7wEuxUGw@<H?1uA8Xr^PC^Xf]a]Q$2;<bh^O+:5v=(&0a/p#fX-_bKz,' );
define( 'WP_CACHE_KEY_SALT', ',[CDlv Ve<m{w!8gJLHc2(eP:aM^6Kvo[bTB-?c_-iRmNz6(iO1pJ~.*u6J!Jfb0' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
