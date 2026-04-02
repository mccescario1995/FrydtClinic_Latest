<?php

namespace App;

trait PageTemplates
{
    /*
    |--------------------------------------------------------------------------
    | Page Templates for Backpack\PageManager
    |--------------------------------------------------------------------------
    |
    | Each page template has its own method, that define what fields should show up using the Backpack\CRUD API.
    | Use snake_case for naming and PageManager will make sure it looks pretty in the create/update form
    | template dropdown.
    |
    | Any fields defined here will show up after the standard page fields:
    | - select template
    | - page name (only seen by admins)
    | - page title
    | - page slug
    */

    private function services()
    {
        $this->crud->addField([   // CustomHTML
            'name' => 'metas_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>'.trans('backpack::pagemanager.metas').'</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'meta_title',
            'label' => trans('backpack::pagemanager.meta_title'),
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([
            'name' => 'meta_description',
            'label' => trans('backpack::pagemanager.meta_description'),
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([
            'name' => 'meta_keywords',
            'type' => 'textarea',
            'label' => trans('backpack::pagemanager.meta_keywords'),
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([   // CustomHTML
            'name' => 'content_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>'.trans('backpack::pagemanager.content').'</h2><hr>',
        ]);
        $this->crud->addField([
            'name' => 'content',
            'label' => trans('backpack::pagemanager.content'),
            'type' => 'summernote',
            'placeholder' => trans('backpack::pagemanager.content_placeholder'),
        ]);
    }

    private function about_us()
    {
        $this->crud->addField([
            'name' => 'content',
            'label' => trans('backpack::pagemanager.content'),
            'type' => 'summernote',
            'placeholder' => trans('backpack::pagemanager.content_placeholder'),
        ]);
    }

    private function contact_us()
    {
        $this->crud->addField([   // CustomHTML
            'name' => 'contents_separator',
            'type' => 'custom_html',
            'value' => '<br><h2>Contents</h2><hr class="mb-2">',
        ]);
        $this->crud->addField([
            'name' => 'address',
            'label' => 'Address',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Address',
            ],
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([
            'name' => 'phone',
            'label' => 'Phone',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Phone Number',
            ],
            'fake' => true,
            'store_in' => 'extras',

        ]);
        $this->crud->addField([
            'name' => 'email',
            'label' => 'Email',
            'type' => 'email',
            'attributes' => [
                'placeholder' => 'Email',
            ],
            'fake' => true,
            'store_in' => 'extras',

        ]);
        $this->crud->addField([
            'name' => 'maps',
            'label' => 'Google Maps link',
            'type' => 'textarea',
            'attributes' => [
                'placeholder' => 'Maps URL',
            ],
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([   // CustomHTML
            'name' => 'social_separator',
            'type' => 'custom_html',
            'value' => '<br><h3>Socials</h3><hr class="mb-1">',
        ]);
        $this->crud->addField([
            'name' => 'facebook',
            'label' => 'Facebook URL',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Facebook URL',
            ],
            'fake' => true,
            'store_in' => 'extras',
        ]);
        $this->crud->addField([
            'name' => 'twitter',
            'label' => 'X(Twitter) URL',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'X(Twitter) URL',
            ],
            'fake' => true,
            'store_in' => 'extras',
        ]);

    }
}
