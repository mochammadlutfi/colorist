import { defineStore } from "pinia";
import Cookies from "js-cookie";
import axios from "axios";
import { updateAbility } from '@/Plugins/ability';

export const useAuthStore = defineStore({
    id: "auth",

    state: () => ({
        user: null,
        token: Cookies.get("token") || null,
        permissions: [],
    }),

    getters: {
        isLoggedIn: (state) => Boolean(state.token),
        userData: (state) => state.user,
        permissionData : (state) => state.permissions,
    },

    actions: {
        async getUser() {
            try {
                const { data } = await axios.get("/profile");
                this.updateUser(data);
                this.setPermissions(data.permissions);

            } catch (error) {
                if(error.status == 401){
                    this.user = null;
                    this.token = null;
                    this.permissions = [];
                    this.$reset();
                    Cookies.remove("token");
                    delete axios.defaults.headers.common["Authorization"];
                    window.location.href = '/login';
                }
                console.error("Failed to fetch user data:", error);
            }
        },

        setPermissions(permissions) {
            this.permissions = permissions;
            updateAbility(permissions);
        },

        updateUser(data) {
            this.user = {
                id: data.id,
                name: data.name,
                email: data.email,
                employee_id: data.employee_id,
                image: data.image,
                image_url: data.image_url,
                role: data.roles?.[0]?.name || null,
            };
        },

        updateToken(token) {
            this.token = token;
            Cookies.set("token", token);
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
        },

        async login({ email, password }) {
            try {
                const { data } = await axios.post("/login", { email, password });
                
                this.updateUser(data.result.user);
                this.setPermissions(data.result.permissions);
                this.updateToken(data.result.access_token);
                
            } catch (error) {
                console.log('Login Error', error);
                throw error;
            }
        },

        async forgotPassword({ email }) {
            try {
                // Perbaikan typo 'web' menjadi 'axios'
                await axios.post("/forgot-password", { email });
            } catch (error) {
                console.error("Forgot password error:", error);
                throw error;
            }
        },

        async logout() {
            try {
                await axios.post("/logout");
            } catch (error) {
                console.warn("Logout request failed, clearing session anyway.");
            }
            this.user = null;
            this.token = null;
            this.permissions = [];
            this.$reset();
            Cookies.remove("token");
            delete axios.defaults.headers.common["Authorization"];
            
            // Gunakan window.location untuk navigasi setelah logout
            window.location.href = '/login';
        },
    },
});