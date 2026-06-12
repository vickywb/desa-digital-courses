<script setup>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/auth";

const router = useRouter();
const authStore = useAuthStore();
const loading = ref(false);

const form = reactive({
    identifier: "",
    password: "",
    role: "",
});

const errors = reactive({
    identifier: "",
    password: "",
    role: "",
    general: "",
});

async function handleLogin() {
    if (!form.role) {
        errors.role = "Pilih role terlebih dahulu";
        return;
    }

    loading.value = true;
    errors.identifier = "";
    errors.password = "";
    errors.role = "";
    errors.general = "";

    const isEmail = form.identifier.includes("@");

    try {
        const credentials = isEmail
            ? { email: form.identifier, password: form.password }
            : { username: form.identifier, password: form.password };

        const user = await authStore.login(credentials);
        const staffRoles = ["admin", "staff", "head_village"];
        const isStaffVillage = staffRoles.includes(user.role);

        if (form.role === "staff_vilage" && !isStaffVillage) {
            errors.general = "Maaf, Anda bukan perangkat desa.";
            await authStore.logout();
            return;
        }

        if (form.role === "head_of_family" && isStaffVillage) {
            errors.general = "Maaf, Anda adalah perangkat desa.";
            await authStore.logout();
            return;
        }

        const target = isStaffVillage ? "/staff/dashboard" : "/warga/dashboard";
        router.push(target);
    } catch (err) {
        if (err.response?.status === 422) {
            const validation = err.response.data.errors;
            errors.identifier =
                validation?.email?.[0] ?? validation?.username?.[0] ?? "";
            errors.password = validation?.password?.[0] ?? "";
        } else {
            errors.general =
                err.response?.data?.message ??
                "Login gagal. Silakan coba lagi.";
        }
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <main class="flex min-h-screen">
        <form
            @submit.prevent="handleLogin"
            class="flex items-center flex-1 pl-[calc(((100%-1280px)/2)+75px)]"
        >
            <div
                class="flex flex-col h-fit w-[486px] shrink-0 rounded-3xl p-[32px] gap-[32px] bg-white"
            >
                <header class="flex flex-col gap-[32px] items-center">
                    <img
                        src="/desa-digital/src/assets/images/logos/logo-text.svg"
                        alt="icon"
                        class="shrink-0 h-[38px] w-[197px]"
                    />
                    <div class="flex flex-col gap-2">
                        <h1
                            class="font-semibold text-[24px] leading-[30px] text-center"
                        >
                            Halo🙌🏻 , Selamat Datang!
                        </h1>
                        <p
                            class="font-medium leading-5 text-desa-secondary text-center"
                        >
                            Silahkan masuk untuk melanjutkan
                        </p>
                    </div>
                </header>

                <section id="Select" class="grid grid-cols-2 gap-6">
                    <div
                        class="group relative flex items-center justify-between p-5 rounded-2xl bg-white ring-[1px] ring-desa-background cursor-pointer transition-all duration-300"
                        :class="
                            form.role === 'staff_vilage'
                                ? 'bg-desa-foreshadow ring-desa-dark-green'
                                : 'hover:bg-desa-foreshadow'
                        "
                    >
                        <input
                            id="Kepala-Desa"
                            value="staff_vilage"
                            v-model="form.role"
                            type="radio"
                            class="peer absolute left-0 right-0 top-0 bottom-0 z-50 opacity-0 cursor-pointer"
                        />
                        <p
                            class="font-medium leading-5 transition-all duration-300"
                            :class="
                                form.role === 'staff_vilage'
                                    ? 'text-desa-dark-green'
                                    : 'text-desa-secondary group-hover:text-desa-dark-green'
                            "
                        >
                            Kepala Desa
                        </p>
                        <div class="relative">
                            <img
                                src="/desa-digital/src/assets/images/icons/crown-secondary-green.svg"
                                alt="icon"
                                class="shrink-0 h-[24px] w-[24px] absolute transition-all duration-300"
                                :class="
                                    form.role === 'staff_vilage'
                                        ? 'opacity-0'
                                        : ''
                                "
                            />
                            <img
                                src="/desa-digital/src/assets/images/icons/crown-dark-green.svg"
                                alt="icon"
                                class="shrink-0 h-[24px] w-[24px] transition-all duration-300"
                                :class="
                                    form.role === 'staff_vilage'
                                        ? 'opacity-100'
                                        : 'opacity-0'
                                "
                            />
                        </div>
                    </div>

                    <div
                        class="group relative flex items-center justify-between p-5 rounded-2xl bg-white ring-[1px] ring-desa-background cursor-pointer transition-all duration-300"
                        :class="
                            form.role === 'head_of_family'
                                ? 'bg-desa-foreshadow ring-desa-dark-green'
                                : 'hover:bg-desa-foreshadow'
                        "
                    >
                        <input
                            id="Kepala-Rumah"
                            value="head_of_family"
                            v-model="form.role"
                            type="radio"
                            class="peer absolute left-0 right-0 top-0 bottom-0 z-50 opacity-0 cursor-pointer"
                        />
                        <p
                            class="font-medium leading-5 transition-all duration-300"
                            :class="
                                form.role === 'head_of_family'
                                    ? 'text-desa-dark-green'
                                    : 'text-desa-secondary group-hover:text-desa-dark-green'
                            "
                        >
                            Kepala Rumah
                        </p>
                        <div class="relative">
                            <img
                                src="/desa-digital/src/assets/images/icons/profile-circle-secondary-green.svg"
                                alt="icon"
                                class="shrink-0 h-[24px] w-[24px] absolute transition-all duration-300"
                                :class="
                                    form.role === 'head_of_family'
                                        ? 'opacity-0'
                                        : ''
                                "
                            />
                            <img
                                src="/desa-digital/src/assets/images/icons/profile-circle-dark-green.svg"
                                alt="icon"
                                class="shrink-0 h-[24px] w-[24px] transition-all duration-300"
                                :class="
                                    form.role === 'head_of_family'
                                        ? 'opacity-100'
                                        : 'opacity-0'
                                "
                            />
                        </div>
                    </div>
                </section>
                <p v-if="errors.role" class="text-sm text-desa-red -mt-4">
                    {{ errors.role }}
                </p>

                <section id="Inputs" class="flex flex-col gap-[32px]">
                    <div id="Email-Address" class="flex flex-col gap-4">
                        <h2 class="font-medium leading-5 text-desa-secondary">
                            Email Address
                        </h2>
                        <div class="relative">
                            <input
                                v-model="form.identifier"
                                placeholder="Masukan Email Kamu"
                                type="text"
                                class="peer w-full h-[56px] rounded-2xl pl-[48px] pr-4 border-[1.5px] border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none placeholder:leading-5 placeholder:text-desa-secondary placeholder:font-medium transition-all duration-303"
                            />
                            <img
                                src="/desa-digital/src/assets/images/icons/user-secondary-green.svg"
                                alt="icon"
                                class="absolute shrink-0 size-6 top-1/2 left-4 -translate-y-1/2 opacity-0 peer-placeholder-shown:opacity-100 transition-all duration-300"
                            />
                            <img
                                src="/desa-digital/src/assets/images/icons/user-black.svg"
                                alt="icon"
                                class="absolute shrink-0 size-6 top-1/2 left-4 -translate-y-1/2 opacity-100 peer-placeholder-shown:opacity-0 transition-all duration-300"
                            />
                        </div>
                        <p
                            v-if="errors.identifier"
                            class="text-sm text-desa-red"
                        >
                            {{ errors.identifier }}
                        </p>
                    </div>

                    <div id="Password" class="flex flex-col gap-4">
                        <h2 class="font-medium leading-5 text-desa-secondary">
                            Password
                        </h2>
                        <div class="relative">
                            <input
                                v-model="form.password"
                                placeholder="Ketik Password Kamu"
                                type="password"
                                class="peer w-full h-[56px] rounded-2xl pl-[48px] pr-4 border-[1.5px] border-desa-background font-medium leading-5 focus:ring-[1.5px] focus:ring-desa-dark-green focus:outline-none placeholder:leading-5 placeholder:text-desa-secondary placeholder:font-medium transition-all duration-300 tracking-[0.25rem] placeholder-shown:tracking-normal"
                            />
                            <img
                                src="/desa-digital/src/assets/images/icons/key-secondary-green.svg"
                                alt="icon"
                                class="absolute shrink-0 size-6 top-1/2 left-4 -translate-y-1/2 opacity-0 peer-placeholder-shown:opacity-100 transition-all duration-300"
                            />
                            <img
                                src="/desa-digital/src/assets/images/icons/key-black.svg"
                                alt="icon"
                                class="absolute shrink-0 size-6 top-1/2 left-4 -translate-y-1/2 opacity-100 peer-placeholder-shown:opacity-0 transition-all duration-300"
                            />
                        </div>
                        <p v-if="errors.password" class="text-sm text-desa-red">
                            {{ errors.password }}
                        </p>
                    </div>
                </section>

                <p v-if="errors.general" class="text-sm text-desa-red">
                    {{ errors.general }}
                </p>

                <button
                    type="submit"
                    :disabled="loading"
                    class="py-[18px] flex justify-center items-center bg-desa-dark-green rounded-2xl font-medium leading-5 text-white disabled:opacity-50"
                >
                    {{ loading ? "Memproses..." : "Masuk" }}
                </button>
            </div>
        </form>

        <section id="Banner" class="relative flex w-full max-w-[634px]">
            <div
                class="fixed top-0 h-screen w-full max-w-[634px] overflow-hidden pr-3 py-3"
            >
                <div
                    class="h-full w-[622px] rounded-3xl gradient-vertical pt-[59px] pb-[60px]"
                >
                    <img
                        src="/desa-digital/src/assets/images/backgrounds/bg-signin.png"
                        class="h-full w-[542px] object-contain mx-auto"
                        alt="banner"
                    />
                </div>
            </div>
        </section>
    </main>
</template>
