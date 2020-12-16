<?php

namespace WWN\Team;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\Core\Convert;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\Parsers\URLSegmentFilter;
use SilverStripe\View\Requirements;
use WWN\Vehicles\Vehicle;

/**
 * TeamPlatoon
 *
 * @package wwn-team
 */
class TeamPlatoon extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'WWNTeamPlatoon';

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
    ];

    /**
     * @var string[]
     */
    private static $has_many = [
        'Vehicles' => Vehicle::class,
        'Groups' => TeamGroup::class,
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
        $mainFields = [
            'URLSegment' => SiteTreeURLSegmentField::create(
                'URLSegment',
                _t('WWN\Team\TeamPlatoon.db_URLSegment', 'URL-segment')
            ),
        ];
        $fields->addFieldsToTab('Root.Main', $mainFields);
        $fields->removeByName('SortOrder');

        $image = $fields->dataFieldByName('Image');
        $image->setFolderName(
            _t(
                'WWN\Team\Extensions\TeamSiteConfigExtension.Foldername',
                'Foldername'
            ).'/'. str_replace(['/',',','.',' ','_','(',')'],'-',$this->Name)
        );
        
        return $fields;
    }

    /**
     * rewrite URLSegment and SortOrder
     */
    protected function onBeforeWrite()
    {
        if (! $this->SortOrder) {
            $this->SortOrder = TeamPlatoon::get()->max('SortOrder') + 1;
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
     * @param $platoonId
     *
     * @return DataList
     */
    public function SortedGroups($platoonId): DataList
    {
        return TeamGroup::get()
            ->filter(['PlatoonID' => $this->ID])
            ->sort('SortOrder ASC');
    }
}
