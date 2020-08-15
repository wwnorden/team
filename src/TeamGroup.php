<?php

namespace WWN\Team;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\View\Requirements;
use WWN\Vehicles\Vehicle;

/**
 * TeamGroup
 *
 * @package wwn-team
 */
class TeamGroup extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'WWNTeamGroup';

    /**
     * @var array
     */
    private static $db = [
        'Name' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
        'Platoon' => 'Varchar(255)',
        'Content' => 'HTMLText',
    ];

    /**
     * @var array
     */
    private static $has_one = [
        'Image' => Image::class,
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'Vehicles' => Vehicle::class,
    ];

    /**
     * @var array
     */
    private static $belongs_many_many = [
        'TeamMembers' => TeamMember::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'Image',
    ];

    /**
     * @var string
     */
    private static $default_sort = [
        'Name' => 'ASC',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Name',
        'URLSegment',
        'Platoon',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Title',
    ];

    /**
     * @return RequiredFields
     */
    public function getCMSValidator()
    {
        return new RequiredFields('Name');
    }

    /**
     * @return FieldList $fields
     */
    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        // remove undefined string from urlsegment in backend
        Requirements::javascript('wwnorden/team:client/dist/js/urlsegmentfield.js');

        // Url segment
        $mainFields = array(
            'URLSegment' => SiteTreeURLSegmentField::create(
                'URLSegment',
                _t('WWN\Team\TeamGroup.db_URLSegment', 'URL-segment')
            ),
        );
        $fields->addFieldsToTab('Root.Main', $mainFields);

        return $fields;
    }

    /**
     * rewrite urlsegment
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (! $this->URLSegment
            || ($this->URLSegment
                && $this->isChanged('URLSegment'))
        ) {
            $urlFilter = URLSegmentFilter::create();
            $filteredTitle = $urlFilter->filter($this->Title);

            // check if duplicate
            $filter['URLSegment'] = Convert::raw2sql($filteredTitle);
            $filter['ID:not'] = $this->ID;
            $object = DataObject::get($this->getClassName())->filter($filter)
                ->first();
            if ($object) {
                $filteredTitle .= '-'.$this->ID;
            }

            // Fallback to generic page name if path is empty (= no valid, convertable characters)
            if (! $filteredTitle || $filteredTitle == '-'
                || $filteredTitle == '-1'
            ) {
                $filteredTitle = "group-$this->ID";
            }
            $this->URLSegment = $filteredTitle;
        }
    }

    /**
     * publish images after save to db
     */
    public function onAfterWrite()
    {
        if ($this->owner->ImageID) {
            $this->owner->Image()->publishSingle();
        }
        parent::onAfterWrite();
    }
}
