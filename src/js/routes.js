import Dashboard from './Components/Dashboard';
import Settings from './Components/Settings';
import Attendees from './Components/StatsInfo';

export const routes = [
    {
        path: '/',
        name: 'dashboard',
        component: Dashboard
    },
    {
        path: '/settings',
        name: 'settings',
        component: Settings
    },
    {
        path: '/attendees',
        name: 'attendees',
        component: Attendees
    }
];
