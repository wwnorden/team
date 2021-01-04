<?php

namespace WWN\Team;

use SilverStripe\Assets\Image;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\ManyManyList;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;

/**
 * TeamMember
 *
 * @package wwn-team
 * @property string $Grade
 * @property string $FirstName
 * @property string $LastName
 * @property string $Email
 * @property string $Position
 * @property bool   $ShowMailOnSite
 * @property bool   $ShowInContactForm
 * @property int    $SortOrder
 * @method ManyManyList Pages()
 * @method ManyManyList Groups()
 */
class TeamMember extends DataObject
{
    /**
     * @var string
     */
    private static $table_name = 'WWNTeamMember';

    /**
     * @var string[]
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
     * @var string[]
     */
    private static $belongs_many_many = [
        'Pages' => SiteTree::class,
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
    private static $many_many = [
        'Groups' => TeamGroup::class,
    ];

    /**
     * @var \string[][]
     */
    private static $many_many_extraFields = [
        'Groups' => [
            'Sort' => 'Int',
        ],
    ];

    /**
     * @var string[]
     */
    private static $owns = [
        'Image',
    ];

    /**
     * Standardsortierung
     *
     * @var string[]
     */
    private static $default_sort = [
        'LastName' => 'ASC',
    ];

    /**
     * @var string[]
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
     * @var string[]
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->FirstName.' '.$this->LastName;
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
            ).'/'.
            _t(
                'WWN\Team\TeamMember.PLURALNAME',
                'PLURALNAME'
            ).'/'.
            str_replace(['/', ',', '.', ' ', '_', '(', ')'], '-', $this->FirstName.' '.$this->LastName)
        );

        // first, member must exist
        if ($this->exists()) {
            // sorting Pages
            $pages = GridField::create(
                'Pages',
                _t('WWN\Team\TeamMember.belongs_many_many_Pages', 'Pages'),
                $this->Pages(),
                GridFieldConfig::create()->addComponents(
                    new GridFieldToolbarHeader(),
                    new GridFieldAddNewButton('toolbar-header-right'),
                    new GridFieldDetailForm(),
                    new GridFieldDataColumns(),
                    new GridFieldEditButton(),
                    new GridFieldDeleteAction('unlinkrelation'),
                    new GridFieldDeleteAction(),
                    new GridFieldOrderableRows(),
                    new GridFieldTitleHeader(),
                    new GridFieldAddExistingAutocompleter('before', ['Title'])
                )
            );
            $fields->addFieldsToTab('Root.Pages', [$pages]);

            // sorting Groups
            $groups = GridField::create(
                'Groups',
                _t('WWN\Team\TeamMember.many_many_Groups', 'Groups'),
                $this->Groups(),
                GridFieldConfig::create()->addComponents(
                    new GridFieldToolbarHeader(),
                    new GridFieldAddNewButton('toolbar-header-right'),
                    new GridFieldDetailForm(),
                    new GridFieldDataColumns(),
                    new GridFieldEditButton(),
                    new GridFieldDeleteAction('unlinkrelation'),
                    new GridFieldDeleteAction(),
                    new GridFieldOrderableRows(),
                    new GridFieldTitleHeader(),
                    new GridFieldAddExistingAutocompleter('before', ['Name'])
                )
            );
            $fields->addFieldsToTab('Root.Groups', [$groups]);
        } else {
            $message = _t('WWN\Team\TeamMember.PagesGroupsMessage', 'PagesGroupsMessage');
            $field = FieldGroup::create(LiteralField::create('PagesGroupsMessage', $message));
            $fields->insertBefore('Grade', $field);
        }

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

    /**
     * @return DataList|ManyManyList
     */
    public function getSortedTeamGroups()
    {
        return $this->Groups()->sort('Sort');
    }
}
