import axios from 'axios';

const http = axios.create({
    baseURL: window.location.origin,
});

// Intercept all response and error
http.interceptors.response.use(
    // return success response
    (response) => {
        return response;
    },
    (error) => {
        // Check if 401 then redirect user to login page
        if (error.response && error.response.status === 401) {
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export default http;