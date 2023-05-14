import Dashboard from './Components/Dashboard';
import Settings from './Components/Settings';
import Stats from './Components/Stats';

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
        path: '/stats',
        name: 'stats',
        component: Stats
    }
];
