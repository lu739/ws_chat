<script>

    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

    export default {
        name: 'Chats',
        components: {AuthenticatedLayout},

        props: {
            chat: Object,
        },

        data() {
            return {

            }
        },

        methods: {

        }
    }
</script>

<template>
    <authenticated-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Chat {{ chat.title }}
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 w-3/4">
                <div class="flex">
                    <div class="w-3/4 p-2 border border-gray-200">
Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium aspernatur aut beatae dignissimos iusto magni nemo perferendis quos voluptates! Adipisci.
                    </div>
                    <div class="w-1/4 p-2 border border-gray-200">
                        <h3 class="mb-3 mt-2 text-2xl font-bold">
                            Chat users
                        </h3>
                        <ul v-if="chat.users" class="flex flex-col">
                            <li v-for="user in chat.users" :key="user.id"
                                class="p-2 border-t border-gray-300"
                            >
                                {{ user.name }}
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
