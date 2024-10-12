<script>

    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

    export default {
        name: 'Chats',
        components: {AuthenticatedLayout},

        props: {
            users: Object,
            chats: Object
        },

        data() {
            return {

            }
        },

        methods: {
            store(id) {
                this.$inertia.post(route('chats.store', {
                        'title': '',
                        'users': [id],
                    }
                ));
            }
        }
    }
</script>

<template>
    <authenticated-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Chats
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 w-3/4">
                <div class="flex">
                    <div class="w-1/2 p-2 border border-gray-200">

                        <h3 class="mb-3 mt-2 text-2xl font-bold">
                            Chats
                        </h3>
                        <ul v-if="chats" class="flex flex-col">
                            <li v-for="chat in chats" :key="chat.id"
                                @click="this.$inertia.get(route('chats.show', chat.id))"
                                class="p-2 border-t border-gray-300 cursor-pointer hover:bg-gray-100"
                            >
                                #{{ chat.id }} {{ chat.title }}
                            </li>
                        </ul>

                    </div>
                    <div class="w-1/2 p-2 border border-gray-200">
                        <h3 class="mb-3 mt-2 text-2xl font-bold">
                            Users
                        </h3>
                        <ul v-if="users" class="flex flex-col">
                            <li v-for="user in users" :key="user.id"
                                @click="store(user.id)"
                                class="p-2 border-t border-gray-300 cursor-pointer hover:bg-gray-100"
                            >
                                #{{ user.id }} {{ user.name }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </authenticated-layout>
</template>

<style scoped>

</style>
