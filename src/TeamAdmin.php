<?php

namespace WWN\Team;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

/**
 * TeamAdmin
 *
 * @package wwn-team
 */
class TeamAdmin extends ModelAdmin
{
    /**
     * @var string
     */
    private static $menu_icon_class = 'font-icon-torso';

    /**
     * @var string
     */
    private static $menu_title = 'Team';

    /**
     * @var string
     */
    private static $url_segment = 'team';

    /**
     * @var array
     */
    private static $managed_models = [
        'WWN\Team\TeamMember',
        'WWN\Team\TeamGroup',
        'WWN\Team\TeamPlatoon',
    ];

    /**
     * @param null $id
     * @param null $fields
     *
     * @return Form
     */
    public function getEditForm($id = null, $fields = null): Form
    {
        $form = parent::getEditForm($id, $fields);
        $model = singleton($this->modelClass);

        if (class_exists(GridFieldOrderableRows::class)
            && $model->hasField('SortOrder')
        ) {
            $gridField = $form->Fields()
                ->dataFieldByName($this->sanitiseClassName($this->modelClass));
            if ($gridField instanceof GridField) {
                $gridField->getConfig()
                    ->addComponent(new GridFieldOrderableRows('SortOrder'));
            }
        }

        return $form;
    }
}
