<?php

namespace scanAttendee\Classes;

class ImportData
{
    public function import()
    {
        if (!isset($_FILES['import_file'])) {
            throw new \Exception('No file found');
        }

        $importFile = $_FILES['import_file'];
        $tmpName =  $importFile['tmp_name'];

        $form = json_decode(file_get_contents($tmpName), true);

        dd($form);

        $form = apply_filters('wpm_scan_attendee/import_form_json_data', $form);

        // validate the $form and it's content
        if (
            !is_array($form) ||
            !isset($form['post_title']) ||
            $form['post_type'] != 'wp_payform'
        ) {
            throw new \Exception('Invalid FIle, Please upload right json file');
        }

        do_action('wpm_scan_attendee/before_form_json_import', $form);

        // $newForm = $this->createFormFromData($form);
        // $editUrl = admin_url("admin.php?page=wpm_scan_attendee.php#/edit-form/$newForm->ID/form-builder");

        // do_action('wpm_scan_attendee/form_json_imported', $newForm);

        return array(
            'message' => 'Form successfully imported',
            'form' => $newForm,
            'edit_url' =>  $editUrl
        );
    }
}
