import { defineStore } from "pinia";
import { ref } from "vue";

export const useUiStore = defineStore("ui", () => {
    const mobileSidebarOpen = ref(false);
    const globalLoading = ref(false);

    const openMobileSidebar = () => {
        mobileSidebarOpen.value = true;
        document.body.style.overflow = "hidden";
    };

    const closeMobileSidebar = () => {
        mobileSidebarOpen.value = false;
        document.body.style.overflow = "";
    };

    const toggleMobileSidebar = () => {
        if (mobileSidebarOpen.value) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    };

    const setLoading = (value) => {
        globalLoading.value = value;
    };

    return {
        mobileSidebarOpen,
        globalLoading,
        openMobileSidebar,
        closeMobileSidebar,
        toggleMobileSidebar,
        setLoading,
    };
});
