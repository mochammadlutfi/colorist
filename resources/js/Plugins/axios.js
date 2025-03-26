import axios from 'axios'
import { useAuthStore } from '@/Stores/auth.js'
import router from '@/Router'

// Request interceptor
axios.defaults.baseURL = '/api/';

axios.interceptors.request.use(request => {
    const auth = useAuthStore();
    if (auth.token) {
        request.headers.Authorization = `Bearer ${auth.token}`
    } else {
        request.headers.Authorization = ""
    }

    return request
});

axios.interceptors.response.use(function (response) {
    return response;
}, function (err) {
    const error = {
        status: err.response?.status,
        original: err,
        validation: {},
        message: null,
    };
    switch (err.response?.status) {
        case 422:
            for (let field in err.response.data.result) {
                error.validation[field] = err.response.data.result[field][0];
            }
            break;
        case 403:
            error.message = "You're not allowed to do that.";
            break;
        case 401:
            error.message = "Please re-login.";
            break;
        case 404:
            router.push({ name: 'not-found' });
            break;
        case 500:
            error.message = "Something went really bad. Sorry.";
            break;
        default:
            error.message = "Something went wrong. Please try again later.";
    }
    return Promise.reject(error);
});
