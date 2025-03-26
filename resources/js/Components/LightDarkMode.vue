// Import useDark dan ref dari VueUse
import { useDark } from '@vueuse/core';

export default {
  setup() {
    const darkMode = useDark();
    return { darkMode };
  },
};
