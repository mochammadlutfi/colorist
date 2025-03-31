import { defineStore } from 'pinia'
import Cookies from 'js-cookie';
import axios from 'axios';
const { locale, locales } = window.config;

const helpers = {
    getWindowWidth () {
      return window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth
    },

    getCurrentYear () {
      return new Date().getFullYear()
    },
}
const APP = "APP";


export const useAppBaseStore  = defineStore({
    id : 'appBase',
    state: () => {
        return {
            initialized : false,
            loading: false,
            app: {
                app_name: "",
                date_format: "",
                time_format: "",
                timezone: "",
                logo_light: null,
                logo_dark: null,
                locale: null,
                locales: [],
                currency: null,
            },
            layout: {
              header: true,
              sidebar: true,
              sideOverlay: true,
              footer: true
            },
            settings: {
              colorTheme: '',
              sidebarLeft: true,
              sidebarMini: false,
              sidebarDark: true,
              sidebarVisibleDesktop: true,
              sidebarVisibleMobile: false,
              sideOverlayVisible: false,
              sideOverlayHoverable: false,
              pageOverlay: true,
              headerFixed: true,
              headerDark: false,
              headerSearch: false,
              headerLoader: false,
              pageLoader: false,
              rtlSupport: false,
              sideTransitions: true,
              mainContent: '' // 'boxed', ''narrow'
            }
        }
    },
    
    getters: {
        isInitialized(state){
            return state.app.app_name !== "" && state.initialized;
        },
        locales :(state) => {
            return state.app.locales;
        },
        locale : (state) => {
            return state.app.locale;
        },
    },

    actions : {
        async initApp() {
            this.loading = true;
            const cookieData = Cookies.get(APP);
            if (cookieData) {
                this.app = JSON.parse(cookieData);
                this.initialized = true;
            } else {
                await this.fetchSettings();
                this.initialized = true;
            }
            this.loading = false;
        },
        async fetchSettings() {
            try {
                const response = await axios.get("/base");
                this.app = response.data;
                this.saveSettingsToCookie();
            } catch (error) {
                console.error("Failed to load settings from API:", error);
            }
        },
        async reinitApp() {
            this.initialized = false;
            Cookies.remove('APP');
            await this.fetchSettings();
        },
        saveSettingsToCookie() {
            Cookies.set(APP, JSON.stringify(this.app), {
                expires: 7
            });
        },
        setLocale(locale) {
            this.app.locale = locale
        },
        // Sets the layout, useful for setting different layouts (under layouts/variations/) 
        setLayout (payload) {
          state.layout.header = payload.header
          state.layout.sidebar = payload.sidebar
          state.layout.sideOverlay = payload.sideOverlay
          state.layout.footer = payload.footer
        },
        // Sets sidebar visibility (open, close, toggle)
        sidebar (mode) {
          if (helpers.getWindowWidth() > 991) {
            if (mode === 'open') {
              this.settings.sidebarVisibleDesktop = true
            } else if (mode === 'close') {
              this.settings.sidebarVisibleDesktop = false
            } else if (mode === 'toggle') {
              this.settings.sidebarVisibleDesktop = !this.settings.sidebarVisibleDesktop
            }
          } else {
            if (mode === 'open') {
              this.settings.sidebarVisibleMobile = true
            } else if (mode === 'close') {
              this.settings.sidebarVisibleMobile = false
            } else if (mode === 'toggle') {
              this.settings.sidebarVisibleMobile = !this.settings.sidebarVisibleMobile
            }
          }
        },
    
        // Sets sidebar mini mode (on, off, toggle)
        sidebarMini(mode) {
            // console.log('Sidebar Mini :', mode, this.settings.sidebarMini);
            if (helpers.getWindowWidth() > 991) {
            if (mode === 'on') {
                this.settings.sidebarMini = true
            } else if (mode === 'off') {
                this.settings.sidebarMini = false
            } else if (mode === 'toggle') {
                this.settings.sidebarMini = !this.settings.sidebarMini
                }
            }
        },

        // Sets sidebar position (left, right, toggle)
        sidebarPosition (mode) {
          if (mode === 'left') {
            this.settings.sidebarLeft = true
          } else if (mode === 'right') {
            this.settings.sidebarLeft = false
          } else if (mode === 'toggle') {
            this.settings.sidebarLeft = !this.settings.sidebarLeft
          }
        },

        // Sets sidebar style (dark, light, toggle)
        sidebarStyle (state, payload) {
          if (payload.mode === 'dark') {
            state.settings.sidebarDark = true
          } else if (payload.mode === 'light') {
            state.settings.sidebarDark = false
          } else if (payload.mode === 'toggle') {
            state.settings.sidebarDark = !state.settings.sidebarDark
          }
        },
        // Sets page loader visibility (on, off, toggle)
        pageLoader (state, payload) {
          if (payload.mode === 'on') {
            state.settings.pageLoader = true
          } else if (payload.mode === 'off') {
            state.settings.pageLoader = false
          } else if (payload.mode === 'toggle') {
            state.settings.pageLoader = !state.settings.pageLoader
          }
        },
        // Sets main content mode (full, boxed, narrow)
        mainContent (state, payload) {
          if (payload.mode === 'full') {
            state.settings.mainContent = ''
          } else if (payload.mode === 'boxed') {
            state.settings.mainContent = 'boxed'
          } else if (payload.mode === 'narrow') {
            state.settings.mainContent = 'narrow'
          }
        },

        async updateApp(){
            
        }
    }
})