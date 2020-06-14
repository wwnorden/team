<?php

namespace WWN\Team\Extensions;

use SilverStripe\Assets\Folder;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\DataExtension;

/**
 * TeamSiteConfigExtension
 *
 * @package wwn-team
 */
class TeamSiteConfigExtension extends DataExtension
{
    /**
     * @var array
     */
    private static $has_one = array(
        'TeamImageUploadFolder' => Folder::class,
    );

    /**
     * Set upload folder for team
     *
     * @param FieldList
     */
    public function updateCMSFields(FieldList $fields)
    {
        Folder::find_or_make(
            _t(
                'WWN\Team\Extensions\TeamSiteConfigExtension.Foldername',
                'Foldername'
            )
        );

        $fields->findOrMakeTab(
            'Root.Uploads',
            _t(
                'WWN\Team\Extensions\TeamSiteConfigExtension.SITECONFIGMENUTITLE',
                'Uploads'
            )
        );
        $teamFields = array(
            'TeamImageUploadFolderID' => TreeDropdownField::create(
                'TeamImageUploadFolderID',
                _t(
                    'WWN\Team\Extensions\TeamSiteConfigExtension.has_one_TeamImageUploadFolder',
                    'Images'
                ),
                Folder::class
            ),
        );
        $fields->addFieldsToTab('Root.Uploads', $teamFields);
        $teamHeaders = array(
            'TeamImageUploadFolderID' => _t(
                'WWN\Team\Extensions\TeamSiteConfigExtension.UploadFolders',
                'UploadFolders'
            ),
        );
        foreach ($teamHeaders as $insertBefore => $header) {
            $fields->addFieldToTab(
                'Root.Uploads',
                HeaderField::create($insertBefore.'Header', $header),
                $insertBefore
            );
        }
    }
}
