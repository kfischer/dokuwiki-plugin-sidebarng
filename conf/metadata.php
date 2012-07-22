<?php
/**
 * Configuration metadata for the SidebarNG plugin
 */
$meta['pos']         = array('multichoice', '_choices' => array('left', 'right', 'off' ));
$meta['pagename']    = array('string', '_pattern' => '#[a-z0-9]*#');
$meta['user_ns']     = array('string', '_pattern' => '#^[a-z:]*#');
$meta['group_ns']    = array('string', '_pattern' => '#^[a-z:]*#');
$meta['order']       = array('string', '_pattern' => '#[a-z0-9,]*#');
$meta['content']     = array('multicheckbox', '_choices' => array('toc','trace','index','toolbox','user','group','namespace','main','extra'));
$meta['order_actions']       = array('string', '_pattern' => '#[a-z0-9,]*#');
$meta['actions']     = array('multicheckbox', '_choices' => array('admin', 'edit', 'history', 'revert', 'backlink', 'media', 'subscription', 'index', 'recent', 'login', 'profile', 'top', 'showpage' ));
$meta['main_always'] = array('onoff');
$meta['search']                   = array('multichoice', '_choices' => array('left', 'right'));
$meta['closedwiki']               = array('onoff');
$meta['hideactions']              = array('onoff');
// vim:ts=4:sw=4:et:enc=utf-8:
