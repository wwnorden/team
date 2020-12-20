<?php

namespace WWN\Team\Extensions;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use WWN\Team\TeamGroup;

/**
 * TeamGroupPageExtension
 *
 * @package wwn-page
 */
class TeamGroupPageExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'EnableTeamGroupExtension' => 'Boolean',
    ];

    /**
     * @var string[]
     */
    private static $many_many = [
        'TeamGroups' => TeamGroup::class,
    ];

    /**
     * @var string[][]
     */
    private static $many_many_extraFields = [
        'TeamGroups' => [
            'Sort' => 'Int',
        ],
    ];

    /**
     * @var false[]
     */
    private static $defaults = [
        'EnableTeamGroupExtension' => false,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID && $this->owner->EnableTeamGroupExtension) {
            $grid = GridField::create(
                'TeamGroups',
                _t('WWN\Team\Extensions\TeamGroupPageExtension.many_many_TeamGroups', 'Groups'),
                $this->owner->TeamGroups(),
                GridFieldConfig_RecordEditor::create()
                    ->addComponent(new GridFieldOrderableRows('Sort'))
            );

            $fields->findOrMakeTab('Root.TeamGroups', _t(
                    'WWN\Team\Extensions\TeamGroupPageExtension.many_many_TeamGroups',
                    'Groups'
                )
            );
            $fields->addFieldToTab(
                'Root.TeamGroups',
                $grid
            );
        } else {
            $fields->removeByName('TeamGroups');
        }
        parent::updateCMSFields($fields);
    }

    /**
     * @param FieldList $fields
     */
    public function updateSettingsFields(FieldList $fields)
    {
        $fields->addFieldsToTab(
            'Root.Settings',
            [
                $group = FieldGroup::create(
                    CheckboxField::create(
                        'EnableTeamGroupExtension',
                        _t(
                            'WWN\Team\Extensions\TeamGroupPageExtension.db_EnableTeamGroupExtension',
                            'Enable team groups on page'
                        )
                    )
                ),
                $group->setTitle(_t(
                    'WWN\Team\Extensions\TeamGroupPageExtension.PLURALNAME',
                    'Team groups'
                )),
            ]
        );
    }
}