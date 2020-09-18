<?php

namespace WWN\Team;

use http\Client\Request;
use Page;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataList;
use SilverStripe\View\ArrayData;

/**
 * TeamPage
 *
 * @package wwn-team
 */
class TeamPage extends Page
{
    public function Children()
    {
        $groups = TeamGroup::get()->sort('SortOrder ASC');
        $children = ArrayList::create();
        foreach ($groups as $group) {
            $children->push(
                new ArrayData(
                    [
                        'Link' => $this->URLSegment.'/'.$group->URLSegment,
                        'MenuTitle' => $group->Name,
                        'LinkingMode' => $group->LinkingMode(),
                    ]
                )
            );
        }

        return $children;
    }
}
