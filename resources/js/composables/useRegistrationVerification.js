import { ref } from "vue";

export function useRegistrationVerification({ sendRoute, registerRoute }) {
    const showModal = ref(false);
    const modalError = ref("");

    function closeModal(form) {
        showModal.value = false;
        modalError.value = "";
        if (form) {
            form.verification_code = "";
        }
    }

    function startVerification(form) {
        modalError.value = "";
        showModal.value = false;

        form.transform((data) => {
            const { verification_code: _code, ...payload } = data;

            return payload;
        }).post(sendRoute, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = true;
            },
            onError: () => {
                showModal.value = false;
            },
            onFinish: () => {
                form.transform((data) => data);
            },
        });
    }

    function confirmRegistration(form) {
        modalError.value = "";

        form.post(registerRoute, {
            preserveScroll: true,
            onSuccess: () => {
                closeModal(form);
                form.reset();
            },
            onError: (errors) => {
                if (errors.verification_code) {
                    modalError.value = errors.verification_code;
                    showModal.value = true;

                    return;
                }

                closeModal(form);
            },
        });
    }

    return {
        showModal,
        modalError,
        startVerification,
        closeModal,
        confirmRegistration,
    };
}
