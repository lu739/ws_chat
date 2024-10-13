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
                chatUsers: this.chat.users
                    .filter(user => user.id !== this.$page.props.auth.user.id)
                    .map(user => user.name)
                    .join(', '),

                body: '',
            }
        },

        methods: {
            store() {
                axios.post(route('messages.store', {
                    'body': this.body,
                    'chat_id': this.chat.id,
                })).then(res => {
                    this.body = '';
                    this.chat.messages.push(res.data);
                });
            }
        }
    }
</script>

<template>
    <authenticated-layout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Chat {{ chat.title ?? ' with user(s): ' + chatUsers }}
            </h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 w-3/4">
                <div class="flex">
                    <div class="w-3/4 p-2 border border-gray-200">
                        <div class="p-2 bg-white rounded-lg mb-4 flex flex-col items-start">
                            <span v-for="message in chat.messages" :key="message.id"
                                 :class="[
                                     'px-3 py-2 rounded-lg mb-2',
                                     message.is_owner ? 'bg-sky-300 self-end' : 'bg-gray-100 self-start border border-sky-200'
                                 ]">
                               <span class="flex text-sm font-bold mb-1">
                                    {{ message.user.name }}
                               </span>
                                <span>{{ message.body }}</span>
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <input
                                class="border border-gray-100 rounded-lg"
                                type="text" v-model="this.body">
                            <button
                                class="ms-auto w-1/3 mt-3 py-3 text-white text-2xl font-bold border border-gray-100 bg-gray-800 rounded-lg"
                                @click="store()"
                            >
                                Send message
                            </button>
                        </div>
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
