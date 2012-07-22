<?php
/**
 * Default configuration file for the DokuWiki SidebarNG plugin
 */
$conf['pos']         = 'right';
$conf['pagename']    = 'sidebar';
$conf['group_ns']    = 'group';
$conf['user_ns']     = 'user';
$conf['content']     = 'toc,trace,index,toolbox'; // defines the content of the sidebar
$conf['actions']     = 'admin,edit,history,backlink,media,subscription'; // defines the content of the toolbar
$conf['order']       = '';  // defines the order of the left sidebar content
$conf['order_actions']  = '';  // defines the order of the left sidebar content
$conf['main_always'] = 0;
$conf['search']      = 'left';                       // defines the position  of the search form when 2 sidebars are used
$conf['closedwiki']  = 1;                            // don't show sidebars for logged out users at all
$conf['hideactions'] = 1;                            // hide all wiki related actions for non logged in users
// vim:ts=4:sw=4:et:enc=utf-8:
