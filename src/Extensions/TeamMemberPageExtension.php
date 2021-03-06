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
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\ManyManyList;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use WWN\Team\TeamMember;

/**
 * TeamMemberPageExtension
 *
 * @package wwn-page
 * @property bool $EnableTeamMemberExtension
 * @method ManyManyList TeamMembers()
 */
class TeamMemberPageExtension extends DataExtension
{
    /**
     * @var string[]
     */
    private static $db = [
        'EnableTeamMemberExtension' => 'Boolean',
    ];

    /**
     * @var string[]
     */
    private static $many_many = [
        'TeamMembers' => TeamMember::class,
    ];

    /**
     * @var string[][]
     */
    private static $many_many_extraFields = [
        'TeamMembers' => [
            'Sort' => 'Int',
        ],
    ];

    /**
     * @var false[]
     */
    private static $defaults = [
        'EnableTeamMemberExtension' => false,
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if ($this->owner->ID && $this->owner->EnableTeamMemberExtension) {
            $grid = GridField::create(
                'TeamMembers',
                _t('WWN\Team\Extensions\TeamMemberPageExtension.many_many_TeamMembers', 'Members'),
                $this->owner->TeamMembers(),
                GridFieldConfig_RecordEditor::create()
                    ->addComponent(new GridFieldOrderableRows('Sort'))
            );

            $fields->findOrMakeTab('Root.TeamMembers', _t(
                    'WWN\Team\Extensions\TeamMemberPageExtension.many_many_TeamMembers',
                    'Members'
                )
            );
            $fields->addFieldToTab(
                'Root.TeamMembers',
                $grid
            );
        } else {
            $fields->removeByName('TeamMembers');
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
                        'EnableTeamMemberExtension',
                        _t(
                            'WWN\Team\Extensions\TeamMemberPageExtension.db_EnableTeamMemberExtension',
                            'Enable team members on page'
                        )
                    )
                ),
                $group->setTitle(_t(
                    'WWN\Team\Extensions\TeamMemberPageExtension.PLURALNAME',
                    'Team members'
                )),
            ]
        );
    }

    /**
     * @return DataList|ManyManyList
     */
    public function getSortedTeamMembers()
    {
        return $this->owner->TeamMembers()->sort('Sort');
    }
}
