<?php

namespace WWN\Team;

use Exception;
use PageController;
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
     * Return groups
     *
     * @return PaginatedList
     * @throws Exception
     */
    public function TeamGroups()
    {
        $groups =
            DataObject::get(
                TeamGroup::class
            );

        return new PaginatedList($groups, $this->getRequest());
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
            $customise = [
                'Group' => $group,
                'ExtraBreadcrumb' => ArrayData::create(
                    [
                        'Title' => $group->Name,
                        'Link' => $this->Link($name),
                    ]
                ),
                'Vehicles' => $group->Vehicles(),
                'Members' => $group->TeamMembers(),
            ];

            $renderWith = [
                'WWN/Team/GroupPage',
                'Page',
            ];

            return $this->customise($customise)->renderWith($renderWith);
        }
    }
}
