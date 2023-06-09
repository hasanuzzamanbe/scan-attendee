import Vue from './elements';
import Router from 'vue-router';
Vue.use(Router);

import { applyFilters, addFilter, addAction, doAction } from '@wordpress/hooks';

export default class scanAttendee {
    constructor() {
        this.applyFilters = applyFilters;
        this.addFilter = addFilter;
        this.addAction = addAction;
        this.doAction = doAction;
        this.Vue = Vue;
        this.Router = Router;
    }

    $publicAssets(path){
        return (window.scanAttendeeAdmin.assets_url + path);
    }

    $get(options) {
        return window.jQuery.get(window.scanAttendeeAdmin.ajaxurl, options);
    }

    $adminGet(options) {
        options.action = 'scan_attendee_admin_ajax';
        return window.jQuery.get(window.scanAttendeeAdmin.ajaxurl, options);
    }

    $post(options) {
        return window.jQuery.post(window.scanAttendeeAdmin.ajaxurl, options);
    }

    $adminPost(options) {
        options.action = 'scan_attendee_admin_ajax';
        return window.jQuery.post(window.scanAttendeeAdmin.ajaxurl, options);
    }

    $upload(options, data) {
        options.data = data;
        options.action = 'scan_attendee_admin_ajax';
        return window.jQuery.post(window.scanAttendeeAdmin.ajaxurl, options);

        console.log(options);

        // let data = {
        //     type: "POST",
        //     data: options.data,
        //     route: options.route,
        //     contentType: false,
        //     cache: false,
        //     processData: false
        // }

        return window.jQuery.post(window.scanAttendeeAdmin.ajaxurl, options);
    }

    $getJSON(options) {
        return window.jQuery.getJSON(window.scanAttendeeAdmin.ajaxurl, options);
    }
}
