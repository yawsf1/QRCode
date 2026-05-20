<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";

const page = usePage();
const open = ref(false);

const items = computed(() => page.props.notifications?.items ?? []);
const count = computed(() => page.props.notifications?.count ?? 0);

const toggle = () => {
    open.value = !open.value;
};

const markRead = (id) => {
    router.post(route("admin.notifications.read", id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
    });
};

const markAllRead = () => {
    router.post(route("admin.notifications.read-all"), {}, {
        preserveScroll: true,
        onSuccess: () => {
            open.value = false;
        },
    });
};

const typeIcon = (type) => {
    if (type === "retard") return "schedule";
    if (type === "absence") return "event_busy";
    return "info";
};
</script>

<template>
    <div class="notifWrap">
        <button type="button" class="notifBtn" aria-label="Notifications" @click="toggle">
            <span class="material-symbols-rounded">notifications</span>
            <span v-if="count > 0" class="notifBadge">{{ count > 9 ? "9+" : count }}</span>
        </button>

        <div v-if="open" class="notifPanel">
            <div class="notifPanelHead">
                <h4>Alertes équipe</h4>
                <button
                    v-if="count > 0"
                    type="button"
                    class="markAll"
                    @click="markAllRead"
                >
                    Tout marquer lu
                </button>
            </div>

            <ul v-if="items.length" class="notifList">
                <li
                    v-for="item in items"
                    :key="item.id"
                    class="notifItem"
                    :class="item.type"
                >
                    <span class="material-symbols-rounded notifIcon">{{
                        typeIcon(item.type)
                    }}</span>
                    <div class="notifBody">
                        <p>{{ item.content }}</p>
                        <span class="notifTime">{{ item.created_at }}</span>
                    </div>
                    <button
                        type="button"
                        class="notifMark"
                        title="Marquer comme lu"
                        @click="markRead(item.id)"
                    >
                        <span class="material-symbols-rounded">done</span>
                    </button>
                </li>
            </ul>
            <p v-else class="notifEmpty">Aucune alerte en attente.</p>
        </div>
    </div>
</template>

<style scoped lang="scss">
.notifWrap {
    position: relative;
}

.notifBtn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(255, 255, 255, 0.04);
    color: #8888aa;
    cursor: pointer;
    transition: all 0.15s;

    &:hover {
        color: #f0f0f8;
        border-color: rgba(79, 124, 255, 0.35);
    }

    span.material-symbols-rounded {
        font-size: 20px;
    }
}

.notifBadge {
    position: absolute;
    top: -4px;
    right: -4px;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 9px;
    background: #ff6b6b;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

.notifPanel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: min(360px, calc(100vw - 32px));
    max-height: 400px;
    overflow-y: auto;
    background: #16161f;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
    z-index: 100;
}

.notifPanelHead {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);

    h4 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #f0f0f8;
    }
}

.markAll {
    border: none;
    background: transparent;
    color: #4f7cff;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

.notifList {
    list-style: none;
    margin: 0;
    padding: 8px;
}

.notifItem {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 4px;

    &.retard {
        background: rgba(255, 107, 107, 0.08);
    }
    &.absence {
        background: rgba(245, 166, 35, 0.08);
    }
    &.info {
        background: rgba(79, 124, 255, 0.08);
    }
}

.notifIcon {
    font-size: 18px;
    color: #8888aa;
    flex-shrink: 0;
}

.notifBody {
    flex: 1;
    min-width: 0;

    p {
        margin: 0 0 4px;
        font-size: 12px;
        color: #f0f0f8;
        line-height: 1.4;
    }
}

.notifTime {
    font-size: 10px;
    color: #55556a;
}

.notifMark {
    border: none;
    background: transparent;
    color: #4f7cff;
    cursor: pointer;
    padding: 4px;

    span {
        font-size: 18px;
    }
}

.notifEmpty {
    padding: 24px 16px;
    text-align: center;
    font-size: 12px;
    color: #55556a;
    margin: 0;
}
</style>
