import { router } from "@inertiajs/vue3";
import { createToastInterface, globalEventBus } from "vue-toastification";

const DEDUP_MS = 5000;
const recent = new Map();

let toast = null;
let routerRegistered = false;

function pruneRecent(now) {
    for (const [key, at] of recent) {
        if (now - at > DEDUP_MS) {
            recent.delete(key);
        }
    }
}

function shouldShow(type, message) {
    const text =
        typeof message === "string"
            ? message.trim()
            : message != null
              ? String(message).trim()
              : "";

    if (!text) {
        return false;
    }

    const key = `${type}:${text}`;
    const now = Date.now();
    const last = recent.get(key);

    if (last != null && now - last < DEDUP_MS) {
        return false;
    }

    recent.set(key, now);
    pruneRecent(now);
    return true;
}

export function initFlashToasts(options = {}) {
    toast = createToastInterface({
        ...options,
        eventBus: globalEventBus,
        filterBeforeCreate: (toastItem, existing) => {
            const content = toastItem.content;
            const duplicate = existing.some(
                (t) => t.content === content && t.type === toastItem.type,
            );
            if (duplicate) {
                return false;
            }

            if (typeof options.filterBeforeCreate === "function") {
                return options.filterBeforeCreate(toastItem, existing);
            }

            return toastItem;
        },
    });
}

export function showAppToast(type, message) {
    if (!toast || !shouldShow(type, message)) {
        return;
    }

    const text =
        typeof message === "string" ? message.trim() : String(message).trim();

    if (type === "success") {
        toast.success(text);
    } else if (type === "error") {
        toast.error(text);
    } else if (type === "warning") {
        toast.warning(text);
    } else if (type === "info") {
        toast.info(text);
    }
}

export function consumeInertiaFlash(flash) {
    if (!flash) {
        return;
    }

    showAppToast("success", flash.success);
    showAppToast("error", flash.error);
    showAppToast("warning", flash.warning);
}

export function registerFlashRouter() {
    if (routerRegistered) {
        return;
    }

    routerRegistered = true;

    router.on("finish", (event) => {
        consumeInertiaFlash(event.detail?.page?.props?.flash);
    });
}
