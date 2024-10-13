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
                creatingGroupChat: false,
                chosenGroupChatUsers: [],
                groupChatTitle: '',
            }
        },

        methods: {
            store(ids, title = '') {
                this.$inertia.post(route('chats.store', {
                        'title': title,
                        'users': ids,
                    }
                ));
            },
            createGroupChat() {
                this.store(this.chosenGroupChatUsers, this.groupChatTitle);
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
                                #{{ chat.id }} {{ chat.title ?? 'chat with user(s): ' + chat.users
                                                    .filter(user => user.id !== this.$page.props.auth.user.id)
                                                    .map(user => user.name)
                                                    .join(', ')
                                                }}
                            </li>
                        </ul>

                    </div>
                    <div class="w-1/2 p-2 border border-gray-200">
                        <h3 class="mb-3 mt-2 text-2xl font-bold flex">
                            Users
                            <button class="p-2 text-sm rounded-lg ml-auto bg-sky-400 text-white"
                                v-if="!creatingGroupChat"
                                @click="creatingGroupChat=true"
                            >Choose users for group chat</button>
                            <input :disabled="chosenGroupChatUsers.length < 2"
                                    :class="['p-2 text-sm rounded-lg ml-auto bg-sky-100 border border-sky-700',
                                    chosenGroupChatUsers.length < 2 && 'opacity-50 bg-gray-300 border-gray-500']"
                                v-if="creatingGroupChat"
                                v-model="groupChatTitle">
                            <button :disabled="chosenGroupChatUsers.length < 2"
                                    :class="['p-2 text-sm rounded-lg ml-auto bg-sky-700 text-white',
                                    chosenGroupChatUsers.length < 2 && 'opacity-50']"
                                v-if="creatingGroupChat"
                                @click="createGroupChat()"
                            >Create group chat</button>
                            <button class="p-2 text-sm rounded-lg ml-2 bg-gray-700 text-white"
                                v-if="creatingGroupChat"
                                @click="creatingGroupChat=false; chosenGroupChatUsers=[]"
                            >Cancel</button>
                        </h3>
                        <ul v-if="users" class="flex flex-col">
                            <li v-for="user in users" :key="user.id"
                                class="p-2 border-t border-gray-300"
                            >
                                <input type="checkbox" class="mr-3"
                                       v-if="creatingGroupChat"
                                       v-model="chosenGroupChatUsers"
                                       :value="user.id">
                                <span @click="store([user.id])" class="p-2 rounded-lg cursor-pointer hover:bg-gray-200">
                                    #{{ user.id }} {{ user.name }}
                                </span>
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
