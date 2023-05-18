import Dashboard from './Components/Dashboard';
import Attendees from './Components/StatsInfo';
import Permissions from './Components/Permissions.vue';
import Help from './Components/Help';

export const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/attendees',
        name: 'attendees',
        component: Attendees
    },
    {
        path: '/permissions',
        name: 'permissions',
        component: Permissions
    },
    {
        path: '/help',
        name: 'help',
        component: Help
    }
];
