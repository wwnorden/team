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
use WWN\Team\TeamPlatoon;

/**
 * TeamPlatoonPageExtension
 *
 * @package wwn-page
 */
class TeamPlatoonPageExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'EnableTeamPlatoonExtension' => 'Boolean',
    ];

    /**
     * @var string[]
     */
    private static $many_many = [
        'TeamPlatoons' => TeamPlatoon::class,
    ];

    /**
     * @var string[][]
     */
    private static $many_many_extraFields = [
        'TeamPlatoons' => [
            'Sort' => 'Int',
        ],
    ];

    /**
     * @var false[]
     */
    private static $defaults = [
        'EnableTeamPlatoonExtension' => false,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID && $this->owner->EnableTeamPlatoonExtension) {
            $grid = GridField::create(
                'TeamPlatoons',
                _t('WWN\Team\Extensions\TeamPlatoonPageExtension.many_many_TeamPlatoons', 'Platoons'),
                $this->owner->TeamPlatoons(),
                GridFieldConfig_RecordEditor::create()
                    ->addComponent(new GridFieldOrderableRows('Sort'))
            );

            $fields->findOrMakeTab('Root.TeamPlatoons', _t(
                    'WWN\Team\Extensions\TeamPlatoonPageExtension.many_many_TeamPlatoons',
                    'Platoons'
                )
            );
            $fields->addFieldToTab(
                'Root.TeamPlatoons',
                $grid
            );
        } else {
            $fields->removeByName('TeamPlatoons');
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
                $platoon = FieldGroup::create(
                    CheckboxField::create(
                        'EnableTeamPlatoonExtension',
                        _t(
                            'WWN\Team\Extensions\TeamPlatoonPageExtension.db_EnableTeamPlatoonExtension',
                            'Enable team platoons on page'
                        )
                    )
                ),
                $platoon->setTitle(_t(
                    'WWN\Team\Extensions\TeamPlatoonPageExtension.PLURALNAME',
                    'Team platoons'
                )),
            ]
        );
    }
}