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
define('DB_NAME', 'xsinvestments_com_invest');

/** MySQL database username */
define('DB_USER', 'xsin_com_invest');

/** MySQL database password */
define('DB_PASSWORD', 'Nzp9Hbkr67BQ');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '9*QB01/#=MHfwe$*z+l5hLo]3V~gaz(2{$[z|aN;fGj_{L4!0H|9}~et1FRvY@%0');
define('SECURE_AUTH_KEY',  '``F5<AmRZ>g/Y0<]bk2R]!qtbj+GAx9xhfvB,i}^Ow0~e5}f*NSUZ~-O`=2?NKZ6');
define('LOGGED_IN_KEY',    'u(@sR5#O,t_C9$`1nD+x(8%nF`wpkvjEr14wkmRwb%iL qFK8GFyZ+8wJHcn_Z=(');
define('NONCE_KEY',        '4+xf53-PdY~cePb:hzNvKF^:B3B&GZ;DAy,VLQU73e?X4g6r=.R$s|D~}25/+h]s');
define('AUTH_SALT',        'W+aPIC`^1:Fc#[nIJ:U|v,`r-ilU{$>YnPqNpgX93Qn/O^Ghq&be|msK~TNX<H +');
define('SECURE_AUTH_SALT', 'X->Ogo_0JWB*/% qH?jA/|&4Xa>mjGd2wuUe~TO(5Yc:,Jj>0yz#+z/8XQEnpQ]>');
define('LOGGED_IN_SALT',   '/(+t7E|I@g+zPF3*9:D^<Y h0uxY-0~9_<92NHGrYAEW/LDKj SI7i+)[6yu!XbV');
define('NONCE_SALT',       '9ZBBCqN2u3r.tL1^-~#!}`<c6}We%(`fNS?jHL$B3IXTK8U/AXOyw2a`4aI&2E~i');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'xsin';

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
