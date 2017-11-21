<?php
/*
Plugin Name:  ACF Sync Warning
Description:  Show a warning in development mode to say acf fields need to be synced
Version:      1.0.0
*/

function acf_sync_warning()
{
    if (defined('WP_ENV')) {
        if (WP_ENV == 'development') {
            // Taken from ACF Pro plugin
            $groups = acf_get_field_groups();

            // bail early if no field groups
            if (empty($groups)) return;


            // find JSON field groups which have not yet been imported
            foreach ($groups as $group) {

                // vars
                $sync = array();
                $local = acf_maybe_get($group, 'local', false);
                $modified = acf_maybe_get($group, 'modified', 0);
                $private = acf_maybe_get($group, 'private', false);


                // ignore DB / PHP / private field groups
                if ($local !== 'json' || $private) {

                    // do nothing

                } elseif (!$group['ID']) {

                    $sync[$group['key']] = $group;

                } elseif ($modified && $modified > get_post_modified_time('U', true, $group['ID'], true)) {

                    $sync[$group['key']] = $group;

                }

            }

            if(empty($sync)) return;

            add_action('admin_notices', function () {
                echo '<div class="notice notice-error"><p><strong> *** ACF Needs Syncing! *** </strong> <a href="' . site_url() . '/wp-admin/edit.php?post_type=acf-field-group&post_status=sync">Sync here now</a></p></div>';
            });

            if (!is_admin() && is_user_logged_in()) {
                echo '<div style="position:fixed; bottom:0; left:0; right:0; top:auto; padding:15px; border-top:3px solid red; background-color:white; color:red!important; font-size:24px; text-align:center; margin:0 auto; z-index:9999;"><p style="margin:0;font-size:24px!important; color:red;"><strong>*** ACF Needs Syncing! ***</strong> <a style="text-decoration: underline;" href="' . site_url() . '/wp-admin/edit.php?post_type=acf-field-group&post_status=sync">Sync here now</a></p></div>';
            }
        }
    }
}

add_action('init', 'acf_sync_warning');

?>