<?php

class backendGroups extends cmsBackend {

    protected $useOptions = true;
    public $useDefaultOptionsAction = true;
    public $useDefaultPermissionsAction = true;
    public $useSeoOptions = true;

    public function routeAction($action_name){
        if($action_name == 'index'){
            return 'fields';
        }
        return $action_name;
    }

    public function getBackendMenu(){
        return array(
            array(
                'title' => LANG_GROUPS_FIELDS,
                'url'   => href_to($this->root_url)
            ),
            array(
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options')
            ),
            array(
                'title' => LANG_PERMISSIONS,
                'url'   => href_to($this->root_url, 'perms', 'groups')
            )
        );
    }

    public function validate_unique_field($value){
        return !$this->cms_core->db->isFieldExists('groups', $value);
    }

}
