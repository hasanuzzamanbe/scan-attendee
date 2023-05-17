import Dashboard from './Components/Dashboard';
import Attendees from './Components/StatsInfo';

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
    }
];
