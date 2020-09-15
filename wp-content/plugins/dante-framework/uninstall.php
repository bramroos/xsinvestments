<?php

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete options
delete_option('dante_framework');

// Delete options in Multisite
delete_site_option('dante_framework');
