<?php

namespace WWN\Team;

use SilverStripe\Assets\Image;
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
     * @return object
     */
    public function getCMSValidator()
    {
        return new RequiredFields(
            [
                'Grade',
                'FirstName',
                'LastName',
                'Email',
                'Position',
            ]
        );
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
