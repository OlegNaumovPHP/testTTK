import VueRouter from "vue-router";
import Home from "../components/Home";
import Register from "../components/Register";
import Login from "../components/Login";
import Logout from "../components/Logout";
import Books from "../components/Books";
import Admin from "../components/Admin";

export default new VueRouter({
    routes: [
        {
            path: '/',
            component: Home
        },
        {
            path: '/register',
            component: Register
        },
        {
            path: '/login',
            component: Login
        },
        {
            path: '/logout',
            component: Logout
        },
        {
            path: '/books',
            component: Books
        },
        {
            path: '/admin',
            component: Admin
        },
    ],
    mode: 'history'
});
