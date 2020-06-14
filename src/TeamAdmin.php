<?php

namespace WWN\Team;

use SilverStripe\Admin\ModelAdmin;

/**
 * TeamAdmin
 *
 * @package wwn-team
 */
class TeamAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    private static $menu_icon_class = 'font-icon-torso';

    /**
     * @var string
     */
    private static $menu_title = 'Team';

    /**
     * @var string
     */
    private static $url_segment = 'team';

    /**
     * @var array
     */
    private static $managed_models = [
        'WWN\Team\TeamMember',
        'WWN\Team\TeamGroup',
    ];
}
