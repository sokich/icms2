<?php

class actionAdminCtypesDatasetsAdd extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        $form = $this->getForm('ctypes_dataset', array('add', $ctype['id']));

        $fields  = $content_model->getContentFields($ctype['name']);

        $fields = cmsEventsManager::hook('ctype_content_fields', $fields);

        $cats = $content_model->getCategoriesTree($ctype['name'], false);

        $cats_list = array();

        if ($cats){
            foreach($cats as $c){
                $cats_list[$c['id']] = str_repeat('-- ', $c['ns_level']-1).' '.$c['title'];
            }
        }

		$dataset = array('sorting' => array(array('by'=>'date_pub', 'to'=>'desc')));

        if ($this->request->has('submit')){

			$dataset = $form->parse($this->request, true);

            $dataset['filters'] = $this->request->get('filters');
            $dataset['sorting'] = $this->request->get('sorting');

            $errors = $form->validate($this,  $dataset);

            if (!$errors){

                $dataset_id = $content_model->addContentDataset($dataset, $ctype);

                if ($dataset_id){ cmsUser::addSessionMessage(sprintf(LANG_CP_DATASET_CREATED, $dataset['title']), 'success'); }

                $this->redirectToAction('ctypes', array('datasets', $ctype['id']));

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return $this->cms_template->render('ctypes_dataset', array(
            'do'      => 'add',
            'ctype'   => $ctype,
            'dataset' => $dataset,
            'fields'  => $fields,
            'cats'    => $cats_list,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        ));

    }

}
