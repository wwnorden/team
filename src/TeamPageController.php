<?php

namespace WWN\Team;

use Exception;
use PageController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPResponse;
use SilverStripe\Core\Convert;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\PaginatedList;
use SilverStripe\View\ArrayData;

/**
 * TeamPage Controller
 *
 * @package wwn-team
 */
class TeamPageController extends PageController
{
    private static $allowed_actions = [
        'showGroup',
    ];

    private static $url_handlers = [
        '$URLSegment!' => 'showGroup',
    ];

    /**
     * @var int
     */
    public $groupId = 0;

    /**
     * @return PaginatedList
     * @throws Exception
     */
    public function TeamGroups()
    {
        return TeamGroup::get()->sort('SortOrder ASC');
    }

    /**
     * @return PaginatedList
     * @throws Exception
     */
    public function TeamPlatoons()
    {
        return TeamPlatoon::get()->sort('SortOrder ASC');
    }

    /**
     * Detail view
     *
     * @return DBHTMLText|HTTPResponse
     * @throws Exception
     */
    public function showGroup()
    {
        $name = Convert::raw2sql($this->getRequest()->param('URLSegment'));
        $filter = [
            'URLSegment' => $name,
        ];
        $group = TeamGroup::get()->filter($filter)->first();
        if (! $group) {
            $url = explode('/', $this->getRequest()->getURL());

            return $this->redirect($url[0].'/');
        } else {
            $this->groupId = $group->ID;
            $customise = [
                'Group' => $group,
                'ExtraBreadcrumb' => ArrayData::create(
                    [
                        'Title' => $group->Name,
                        'Link' => $this->Link($name),
                    ]
                ),
                'Vehicles' => $group->Vehicles()->sort('Sort ASC'),
                'Members' => $group->TeamMembers()->sort('SortOrder ASC'),
            ];

            $renderWith = [
                'WWN/Team/GroupPage',
                'Page',
            ];

            return $this->customise($customise)->renderWith($renderWith);
        }
    }
}
