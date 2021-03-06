<?php

namespace WWN\Team\Extensions;

use SilverStripe\ORM\DataExtension;
use WWN\Team\TeamGroup;
use WWN\Team\TeamPlatoon;

/**
 * VehicleExtension
 *
 * @package wwn-team
 */
class VehicleExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_one = [
        'TeamGroup' => TeamGroup::class,
        'TeamPlatoon' => TeamPlatoon::class,
    ];
}
