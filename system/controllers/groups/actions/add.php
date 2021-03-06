<?php

class actionGroupsAdd extends cmsAction {

    public function run(){

        if (!cmsUser::isAllowed('groups', 'add')) { cmsCore::error404(); }

        $form = $this->getGroupForm();

        $fields = $this->getGroupsFields();

        $is_submitted = $this->request->has('submit');

        $group = $form->parse($this->request, $is_submitted);

        $group['ctype_name'] = 'groups';

        // Заполняем поля значениями по-умолчанию, взятыми из профиля пользователя
        // (для тех полей, в которых это включено)
        foreach($fields as $field){
            if (!empty($field['options']['profile_value'])){
                $group[$field['name']] = $this->cms_user->{$field['options']['profile_value']};
            }
        }

        if ($is_submitted){

            $errors = $form->validate($this, $group);

            if (!$errors){

                $group['owner_id'] = $this->cms_user->id;

                $id = $this->model->addGroup($group);

                $group = $this->model->getGroup($id);

                $content = cmsCore::getController('content', $this->request);

                $parents = $content->model->getContentTypeParents(null, 'groups');

                if($parents){
                    $content->bindItemToParents(array('id' => null, 'name' => 'groups'), $group, $parents);
                }

                $this->redirectToAction($group['slug']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        $page_title = LANG_GROUPS_ADD;

        $this->cms_template->setPageTitle($page_title);

        $this->cms_template->addBreadcrumb(LANG_GROUPS, href_to('groups'));
        $this->cms_template->addBreadcrumb($page_title);

        return $this->cms_template->render('group_edit', array(
            'do'         => 'add',
            'page_title' => $page_title,
            'group'      => $group,
            'form'       => $form,
            'errors'     => isset($errors) ? $errors : false
        ));

    }

}
