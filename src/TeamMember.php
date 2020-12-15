<?php

namespace WWN\Team;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

/**
 * TeamMember
 *
 * @package wwn-team
 */
class TeamMember extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'WWNTeamMember';

    /**
     * @var array
     */
    private static $db = [
        'Grade' => 'Varchar(255)',
        'FirstName' => 'Varchar(255)',
        'LastName' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'Position' => 'Varchar(255)',
        'ShowMailOnSite' => 'Boolean',
        'ShowInContactForm' => 'Boolean',
        'SortOrder' => 'Int',
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
    private static $many_many = [
        'Groups' => TeamGroup::class,
    ];

    /**
     * @var array
     */
    private static $owns = [
        'Image',
    ];

    /**
     * Standardsortierung
     *
     * @var string
     */
    private static $default_sort = [
        'LastName' => 'ASC',
    ];

    /**
     * @var array
     */
    private static $summary_fields = [
        'Grade',
        'FirstName',
        'LastName',
        'Email',
        'Position',
        'ShowMailOnSiteFormatted',
        'ShowInContactFormFormatted',
    ];

    /**
     * @var array
     */
    private static $searchable_fields = [
        'Grade',
        'FirstName',
        'LastName',
        'Email',
        'Position',
    ];

    /**
     * @param bool $includerelations
     *
     * @return array
     */
    public function fieldLabels($includerelations = true): array
    {
        $labels = parent::fieldLabels(true);
        $labels['ShowMailOnSiteFormatted'] =
            _t('WWN\Team\TeamMember.db_ShowMailOnSite', 'Show mail on site');
        $labels['ShowInContactFormFormatted'] =
            _t('WWN\Team\TeamMember.db_ShowInContactForm',
                'Show in contact form');

        return $labels;
    }

    /**
     * @return false|string
     */
    public function getShowMailOnSiteFormatted(): ?string
    {
        return $this->dbObject('ShowMailOnSite')
            ->getValue()
            ?
            _t('WWN\Team\TeamMember.Yes', 'Yes')
            :
            _t('WWN\Team\TeamMember.No', 'No');
    }

    /**
     * @return false|string
     */
    public function ShowInContactFormFormatted(): ?string
    {
        return $this->dbObject('ShowInContactForm')
            ->getValue()
            ?
            _t('WWN\Team\TeamMember.Yes', 'Yes')
            :
            _t('WWN\Team\TeamMember.No', 'No');
    }

    /**
     * Overwrite Getter for gridfield_relation search
     * 
     * @return DataObject|\SilverStripe\ORM\FieldType\DBField|string|null
     */
    public function getTitle()
    {
        return $this->FirstName. ' ' .$this->LastName;
    }

    /**
     * @return FieldList $fields
     */
    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('SortOrder');

        $image = $fields->dataFieldByName('Image');
        $image->setFolderName(
            _t(
                'WWN\Team\Extensions\TeamSiteConfigExtension.Foldername',
                'Foldername'
            ).'/'. str_replace('/','-',$this->FirstName . ' ' . $this->LastName)
        );
        
        return $fields;
    }

    /**
     * Increment SortOrder on save
     */
    public function onBeforeWrite()
    {
        if (! $this->SortOrder) {
            $this->SortOrder = TeamMember::get()->max('SortOrder') + 1;
        }
        parent::onBeforeWrite();
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
