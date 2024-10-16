<script>

    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import axios from "axios";
    axios.defaults.headers.common['X-Socket-ID'] = Echo.socketId();

    export default {
        name: 'Chats',
        components: {AuthenticatedLayout},

        props: {
            chat: Object,
            messages: Object,
            current_page: Number,
        },

        data() {
            return {
                chatUsers: this.chat.users
                    .filter(user => user.id !== this.$page.props.auth.user.id)
                    .map(user => user.name)
                    .join(', '),

                body: '',

                reverseMessages: this.messages.reverse(),

                currentPage: this.current_page ?? 1,

                isLastPage: this.messages.length < 5,
            }
        },

        methods: {
            store() {
                axios.post(route('messages.store', {
                    'body': this.body,
                    'chat_id': this.chat.id,
                })).then(res => {
                    this.body = '';
                    this.reverseMessages.push(res.data);
                });
            },
            loadMore() {
                axios.get(route('chats.show', {
                    'chat': this.chat.id,
                    'page': +this.currentPage + 1,
                    'load_more': true,
                })).then(res => {
                    let {messages, current_page, last_page} = res.data;
                    let reversed = messages.reverse();
                    this.reverseMessages = reversed.concat(this.reverseMessages);
                    this.currentPage = current_page;

                    if (current_page === last_page) {
                        this.isLastPage = true;
                    }
                });
            },
            readByUser() {
                axios.patch(route('messages.chat.readByUser', {
                    'chat': this.chat,
                })).then(res => {
                    // обновить вид прочитанных этим юзером сообщений в этом чате
                });
            }
        },

        created() {
            // Echo.channel(`store-message${this.chat.id}`)
            Echo.private(`chat.${this.chat.id}`)
                .listen('.storeMessage', (e) => {
                    this.reverseMessages.push(e.message);
                    this.readByUser();
                });
        },

        unmounted() {
            Echo.leave(`chat.${this.chat.id}`);
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
                        <button v-if="!isLastPage"
                            class="ms-auto w-1/3 mt-3 py-3 text-white text-2xl font-bold border border-gray-100 bg-gray-800 rounded-lg"
                            @click="loadMore()"
                        >
                            Load more...
                        </button>
                        <div class=" max-h-96 overflow-auto p-2 bg-white rounded-lg mb-4 flex flex-col items-start">
                            <span v-for="message in reverseMessages" :key="message.id"
                                 :class="[
                                     'px-3 py-2 rounded-lg mb-2',
                                     message.is_owner ? 'bg-sky-300 self-end' : 'bg-gray-100 self-start border border-sky-200'
                                 ]">
                               <span class="flex text-sm font-bold mb-1">
                                    {{ message.user?.name }}
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
