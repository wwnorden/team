<?php

namespace WWN\Team;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Control\Controller;
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
     * @var string[]
     */
    private static $db = [
        'Name' => 'Varchar(255)',
        'URLSegment' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'SortOrder' => 'Int',
    ];

    /**
     * @var string[]
     */
    private static $has_one = [
        'Image' => Image::class,
        'Platoon' => TeamPlatoon::class,
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Vehicles' => Vehicle::class,
    ];

    /**
     * @var string[]
     */
    private static $belongs_many_many = [
        'Pages' => SiteTree::class,
        'TeamMembers' => TeamMember::class,
    ];

    /**
     * @var string[]
     */
    private static $owns = [
        'Image',
    ];

    /**
     * @var string
     */
    private static $default_sort = 'SortOrder';

    /**
     * @var string[]
     */
    private static $summary_fields = [
        'Name',
        'URLSegment',
    ];

    /**
     * @var string[]
     */
    private static $searchable_fields = [
        'Name',
    ];

    /**
     * @return RequiredFields
     */
    public function getCMSValidator(): RequiredFields
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
        $fields->removeByName('SortOrder');

        $image = $fields->dataFieldByName('Image');
        $image->setFolderName(
            _t(
                'WWN\Team\Extensions\TeamSiteConfigExtension.Foldername',
                'Foldername'
            ).'/'.
            _t(
                'WWN\Team\TeamGroup.PLURALNAME',
                'PLURALNAME'
            ).'/'.
            str_replace(['/',',','.',' ','_','(',')'],'-',$this->Name)
        );
        
        return $fields;
    }

    /**
     * rewrite URLSegment and SortOrder
     */
    protected function onBeforeWrite()
    {
        if (! $this->SortOrder) {
            $this->SortOrder = TeamGroup::get()->max('SortOrder') + 1;
        }

        parent::onBeforeWrite();

        if (! $this->URLSegment
            || ($this->URLSegment
                && $this->isChanged('URLSegment'))
        ) {
            $urlFilter = URLSegmentFilter::create();
            $filteredName = $urlFilter->filter($this->Name);

            // check if duplicate
            $filter['URLSegment'] = Convert::raw2sql($filteredName);
            $filter['ID:not'] = $this->ID;
            $object = DataObject::get($this->getClassName())->filter($filter)
                ->first();
            if ($object) {
                $filteredName .= '-'.$this->ID;
            }

            // Fallback to generic name
            if (! $filteredName || $filteredName == '-'
                || $filteredName == '-1'
            ) {
                $filteredName = "group-$this->ID";
            }
            $this->URLSegment = $filteredName;
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

    /**
     * @return string
     */
    public function LinkingMode(): string
    {
        $group = TeamGroup::get()
            ->filter(['URLSegment' => $this->URLSegment])
            ->first();

        return ($group->ID == TeamPageController::curr()->groupId) ? 'current'
            : 'link';
    }
}
