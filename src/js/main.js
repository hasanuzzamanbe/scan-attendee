window.scanAttendeeBus = new window.scanAttendee.Vue();

window.scanAttendee.Vue.mixin({
    methods: {
        applyFilters: window.scanAttendee.applyFilters,
        addFilter: window.scanAttendee.addFilter,
        addAction: window.scanAttendee.addFilter,
        doAction: window.scanAttendee.doAction,
        $get: window.scanAttendee.$get,
        $upload: window.scanAttendee.$upload,
        $adminGet: window.scanAttendee.$adminGet,
        $adminPost: window.scanAttendee.$adminPost,
        $post: window.scanAttendee.$post,
        $publicAssets: window.scanAttendee.$publicAssets
    }
});

import {routes} from './routes';

const router = new window.scanAttendee.Router({
    routes: window.scanAttendee.applyFilters('scanAttendee_global_vue_routes', routes),
    linkActiveClass: 'active'
});

import App from './AdminApp';

new window.scanAttendee.Vue({
    el: '#scan-attendee_app',
    render: h => h(App),
    router: router
});

