<?php
/**
 * Default configuration file for the DokuWiki SidebarNG plugin
 */
$conf['pos']         = 'left';
$conf['pagename']    = 'sidebar';
$conf['group_ns']    = 'group';
$conf['user_ns']     = 'user';
$conf['content']     = 'main'; // defines the content of the sidebar
$conf['order']       = 'main,namespace,user,group';  // defines the order of the left sidebar content
$conf['main_always'] = 1;
$conf['search']                     = 'left';                       // defines the position  of the search form when 2 sidebars are used
$conf['closedwiki']                 = 0;                            // don't show sidebars for logged out users at all
$conf['hideactions']                = 0;                            // hide all wiki related actions for non logged in users
// vim:ts=4:sw=4:et:enc=utf-8:
