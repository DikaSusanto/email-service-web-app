<template>
    <!-- Notification Toast -->
    <NotificationToast
        :notification="notification"
        @close="notification.show = false"
    />

    <!-- Tabel -->
    <table class="w-full text-sm text-left text-gray-500 border rounded-2xl">
        <thead class="text-xs text-gray-700 uppercase border-b bg-gray-50">
            <tr>
                <th
                    scope="col"
                    class="px-6 py-3"
                    v-for="item in thead"
                    :key="item"
                >
                    {{ item }}
                </th>
            </tr>
        </thead>
        <tbody>
            <!-- Tampilan Loading -->
            <tr v-if="fetch">
                <td colspan="7" class="px-6 py-4 text-lg font-bold text-center">
                    <div role="status">
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Memuat...</span>
                    </div>
                </td>
            </tr>
            <!-- Tampilan Tidak Ada Data -->
            <tr
                v-if="
                    (!props.logs.data || props.logs.data.length === 0) && !fetch
                "
            >
                <td colspan="7" class="px-6 py-4 text-lg font-bold text-center">
                    <div class="flex flex-col items-center justify-center">
                        <img
                            :src="'/NotFound.png'"
                            alt="Tidak ada data"
                            class="w-80 h-44 mb-3"
                        />
                        <p class="text-lg font-bold text-gray-500">
                            Tidak ada data yang ditemukan!
                        </p>
                        <p class="text-sm text-gray-400">
                            Belum ada data yang ditambahkan.
                        </p>
                    </div>
                </td>
            </tr>
            <!-- Tampilan Data -->
            <tr
                v-for="(item, index) in logs.data"
                :key="item.id"
                v-if="!fetch"
                class="hover:bg-gray-50"
            >
                <td class="px-6 py-4">
                    <input
                        type="checkbox"
                        :value="item.id"
                        v-model="checked"
                        class="w-4 h-4 mt-1.5 text-red-600 bg-red-100 border-red-300 rounded focus:ring-red-500 focus:ring-2 accent-red-600"
                    />
                </td>
                <td class="px-6 py-4">
                    {{ item.application.name }}
                </td>
                <td class="px-6 py-4">
                    {{ JSON.parse(item.request).to }}
                </td>
                <td class="px-6 py-4">
                    {{ JSON.parse(item.request).subject }}
                </td>
                <td class="px-6 py-4">
                    <div
                        :class="
                            item.status &&
                            item.status.toLowerCase() === 'success'
                                ? 'bg-green-200 text-green-900'
                                : item.status &&
                                  item.status.toLowerCase() === 'pending'
                                ? 'bg-orange-200 text-orange-800'
                                : 'bg-red-200 text-red-900'
                        "
                        class="p-1 text-center rounded-full"
                    >
                        {{ item.status }}
                    </div>
                </td>
                <td class="px-6 py-4">
                    {{ new Date(item.updated_at).toLocaleString() }}
                </td>
                <td class="flex gap-1 px-6 py-4">
                    <Tippy content="Detail Email">
                        <button
                            @click="
                                showDetailModal = true;
                                getDetail(item.id);
                            "
                            type="button"
                            class="p-1 text-sm font-medium text-white bg-blue-700 rounded-lg focus:outline-none hover:bg-blue-800 focus:ring-4 focus:ring-blue-300"
                        >
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="m21 21-3.5-3.5M10 7v6m-3-3h6m4 0a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <Tippy content="Kirim Ulang" v-if="item.status == 'failed'">
                        <button
                            type="button"
                            @click="
                                showEditModal = true;
                                getEdit(item.id);
                            "
                            class="p-1 text-sm font-medium text-white bg-[#FFC107] rounded-lg focus:outline-none hover:bg-[#F39C12] focus:ring-4 focus:ring-green-300"
                        >
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-width="2"
                                    d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                    <Tippy content="Hapus">
                        <button
                            type="button"
                            @click="
                                showDeleteModal = true;
                                deleteOne = [item.id];
                            "
                            class="p-1 text-sm font-medium text-white bg-red-700 rounded-lg focus:outline-none hover:bg-red-800 focus:ring-4 focus:ring-red-300"
                        >
                            <svg
                                class="w-5 h-5"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z"
                                />
                            </svg>
                        </button>
                    </Tippy>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Modal Kirim Ulang Email -->
    <div
        v-if="showEditModal"
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm md:inset-0"
    >
        <div class="relative w-full max-w-xl max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow" ref="detailModal">
                <div
                    class="flex items-center justify-between p-4 border-b rounded-t md:p-5"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Kirim Ulang Email
                    </h3>
                    <button
                        @click="showEditModal = false"
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto"
                        data-modal-toggle="crud-modal"
                    >
                        <svg
                            class="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 14 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Keluar</span>
                    </button>
                </div>
                <form class="p-4 md:p-5">
                    <div v-show="editFetch" class="grid grid-cols-2 gap-4 mb-4">
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                            >
                                Kepada <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="hidden"
                                name="secret"
                                id="name"
                                v-model="formRetry.secret"
                                @focus="clearRetryError('secret')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Secret"
                                required=""
                            />
                            <div
                                v-if="validationErrors.secret"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            {{ validationErrors.secret }}
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                v-model="formRetry.mail[0].to"
                                @focus="clearRetryError('mail.0.to')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Email penerima"
                                required=""
                            />
                            <div
                                v-if="validationErrors['mail.0.to']"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            {{ validationErrors["mail.0.to"] }}
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                            >
                                Subjek
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                v-model="formRetry.mail[0].subject"
                                @focus="clearRetryError('mail.0.subject')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Subjek email"
                                required=""
                            />
                            <div
                                v-if="validationErrors['mail.0.subject']"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            {{
                                                validationErrors[
                                                    "mail.0.subject"
                                                ]
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Isi</label
                            >
                            <textarea
                                id="description"
                                rows="4"
                                v-model="formRetry.mail[0].content"
                                @focus="clearRetryError('mail.0.content')"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Tulis yang akan dikirim disini"
                            ></textarea>
                            <div
                                v-if="validationErrors['mail.0.content']"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            {{
                                                validationErrors[
                                                    "mail.0.content"
                                                ]
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-span-2"
                            v-for="(item, index) in formRetry.mail[0]
                                .attachment"
                            :key="index"
                        >
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                            >
                                Lampiran
                                {{ index + 1 }}
                            </label>
                            <input
                                type="text"
                                name="name"
                                :id="'name-' + index"
                                v-model="formRetry.mail[0].attachment[index]"
                                @focus="
                                    clearRetryError(
                                        `mail.0.attachment.${index}`
                                    )
                                "
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="URL Lampiran..."
                                required=""
                            />
                            <div
                                v-if="
                                    validationErrors[
                                        `mail.0.attachment.${index}`
                                    ]
                                "
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            {{
                                                validationErrors[
                                                    `mail.0.attachment.${index}`
                                                ]
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <label
                                for="priority"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Pilih Prioritas
                                <span class="text-red-500">*</span></label
                            >
                            <select
                                id="priority"
                                v-model="formRetry.mail[0].priority"
                                @focus="clearRetryError('mail.0.priority')"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            >
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                            <div
                                v-if="validationErrors['mail.0.priority']"
                                class="relative mt-2"
                            >
                                <div
                                    class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                >
                                    <div class="flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 flex-shrink-0"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path
                                                d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"
                                            ></path>
                                            <line
                                                x1="12"
                                                y1="9"
                                                x2="12"
                                                y2="13"
                                            ></line>
                                            <line
                                                x1="12"
                                                y1="17"
                                                x2="12.01"
                                                y2="17"
                                            ></line>
                                        </svg>
                                        <span>
                                            {{
                                                validationErrors[
                                                    "mail.0.priority"
                                                ]
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-2 flex justify-end">
                            <button
                                @click="handleRetry"
                                type="button"
                                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5"
                            >
                                Kirim
                            </button>
                        </div>
                    </div>
                    <div
                        v-show="!editFetch"
                        role="status"
                        class="flex justify-center"
                    >
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Memuat...</span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail Email -->
    <div
        v-if="showDetailModal"
        id="crud-modal"
        tabindex="-1"
        aria-hidden="true"
        class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full overflow-x-hidden overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm md:inset-0"
    >
        <div class="relative w-full max-w-xl max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow" ref="detailModal">
                <div
                    class="flex items-center justify-between p-4 border-b rounded-t md:p-5"
                >
                    <h3 class="text-lg font-semibold text-gray-900">
                        Detail Log Email
                    </h3>
                    <button
                        @click="showDetailModal = false"
                        type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto"
                        data-modal-toggle="crud-modal"
                    >
                        <svg
                            class="w-3 h-3"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 14 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Keluar</span>
                    </button>
                </div>
                <form class="p-4 md:p-5">
                    <div
                        v-show="detailFetch"
                        class="grid grid-cols-2 gap-4 mb-4"
                    >
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                            >
                                Kepada
                            </label>
                            <input
                                readonly
                                disabled
                                type="text"
                                name="name"
                                id="name"
                                :value="logDetail.to"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Email penerima"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                            >
                                Subjek
                            </label>
                            <input
                                readonly
                                disabled
                                type="text"
                                name="name"
                                id="name"
                                :value="logDetail.subject"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="Subjek email"
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Isi</label
                            >
                            <textarea
                                readonly
                                disabled
                                id="description"
                                rows="4"
                                :value="logDetail.content"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Isi dari email yang dikirim..."
                            ></textarea>
                        </div>
                        <div
                            class="col-span-2"
                            v-for="(item, index) in logDetail.attachment"
                        >
                            <label
                                for="name"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Lampiran {{ index + 1 }}</label
                            >
                            <input
                                readonly
                                disabled
                                type="text"
                                name="name"
                                id="name"
                                :value="item"
                                class="bg-gray-50 cursor-not-allowed border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="URL Lampiran..."
                                required=""
                            />
                        </div>
                        <div class="col-span-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Status</label
                            >
                            <textarea
                                readonly
                                disabled
                                id="description"
                                rows="4"
                                :value="logDetail.status"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Status pengiriman"
                            ></textarea>
                        </div>
                        <div class="col-span-2 mt-2">
                            <label
                                for="description"
                                class="block mb-2 text-sm font-medium text-gray-900"
                                >Pesan Error</label
                            >
                            <textarea
                                readonly
                                disabled
                                id="description"
                                rows="4"
                                :value="logDetail.error_message"
                                class="block cursor-not-allowed p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="-"
                            ></textarea>
                        </div>
                    </div>
                    <div
                        v-show="!detailFetch"
                        role="status"
                        class="flex justify-center"
                    >
                        <svg
                            aria-hidden="true"
                            class="inline w-8 h-8 text-gray-200 animate-spin fill-green-500"
                            viewBox="0 0 100 101"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"
                            />
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"
                            />
                        </svg>
                        <span class="sr-only">Memuat...</span>
                    </div>
                </form>
            </div>
            <br />
        </div>
    </div>

    <!-- Modal Hapus Per Log Email -->
    <div
        tabindex="-1"
        v-show="showDeleteModal"
        class="overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex overflow-x-hidden fixed top-0 right-0 left-0 bottom-0 z-50 justify-center items-center w-full md:inset-0"
    >
        <div class="relative w-full max-w-md max-h-full p-4">
            <div class="relative bg-white rounded-lg shadow" ref="deleteModal">
                <button
                    @click="showDeleteModal = false"
                    type="button"
                    class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                    data-modal-hide="popup-modal"
                >
                    <svg
                        class="w-3 h-3"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 14 14"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                        />
                    </svg>
                    <span class="sr-only">Keluar</span>
                </button>
                <div class="p-4 text-center md:p-5">
                    <svg
                        class="w-12 h-12 mx-auto mb-4 text-red-700"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 20 20"
                    >
                        <path
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">
                        Anda yakin ingin menghapus log pengiriman email ini?
                    </h3>
                    <button
                        @click="deleteLog"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center"
                    >
                        Ya, Saya yakin
                    </button>
                    <button
                        @click="showDeleteModal = false"
                        data-modal-hide="popup-modal"
                        type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-gray-950 focus:z-10 focus:ring-4 focus:ring-gray-100"
                    >
                        Tidak, Batalkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Import library dan komponen yang diperlukan
import { watch, ref } from "vue";
import { Tippy } from "vue-tippy";
import NotificationToast from "./NotificationToast.vue";
import { onClickOutside } from "@vueuse/core";
import { useForm, usePage } from "@inertiajs/vue3";
import * as IntegrasiAPI from "../api/IntegrasiAPI";

// Props yang diterima dari parent
const props = defineProps({
    thead: Array,
    fetch: Boolean,
    logs: Object,
    search: String,
});

// Base URL untuk API
const baseUrl = import.meta.env.VITE_APP_URL;

// State untuk modal hapus
const deleteModal = ref(null);
const showDeleteModal = ref(false);
const deleteOne = ref([]);

// State untuk modal detail
const detailModal = ref(null);
const showDetailModal = ref(false);
const detailFetch = ref(false);
onClickOutside(detailModal, () => {
    showDetailModal.value = false;
});
const logDetail = ref({});

// State untuk modal edit
const editModal = ref(null);
const showEditModal = ref(false);
const editFetch = ref(false);
const logEdit = ref({});

// State untuk pesan sukses dan error
const successMessage = ref("");
const errorMessage = ref("");

// State untuk error validasi
const validationErrors = ref({});

// Emit event ke parent
const emit = defineEmits(["checkbox", "refresh", "notification"]);

// State untuk checkbox
const checked = ref([]);
watch(checked, (newChecked) => {
    checked.value = newChecked;
    emit("checkbox", checked.value);
});

// State untuk form hapus
const pageInertia = usePage();
const form = useForm({
    ids: [],
    _token: pageInertia.props.csrf_token,
});

// State untuk notifikasi
const notification = ref({
    show: false,
    type: "",
    message: "",
    description: "",
});

// Fungsi untuk menghapus log
async function deleteLog() {
    form.ids = deleteOne.value;
    showDeleteModal.value = false;
    try {
        await IntegrasiAPI.deleteEmailLogs(form.ids, baseUrl);
        emit("notification", "success", "Berhasil!", "Log berhasil dihapus.");
        emit("refresh");
        form.reset();
    } catch (error) {
        emit(
            "notification",
            "danger",
            "Gagal!",
            error?.response?.data?.message || "Gagal menghapus log."
        );
    }
}

// Fungsi untuk mengambil detail log
async function getDetail(id) {
    detailFetch.value = false;
    logDetail.value = {};
    try {
        const response = await IntegrasiAPI.extractEmailLog(id);
        // Ambil data dari response.data.data.original.data
        let data = response?.data?.data?.original?.data;
        if (!data) {
            logDetail.value = {};
            detailFetch.value = true;
            return;
        }
        logDetail.value = data;
        detailFetch.value = true;
    } catch (error) {
        detailFetch.value = false;
    }
}

// State untuk form retry
const formRetry = useForm({
    id: 0,
    secret: "",
    mail: [
        {
            to: "",
            subject: "",
            content: "",
            attachment: [],
            priority: 0,
        },
    ],
    _token: pageInertia.props.csrf_token,
});

// Fungsi untuk mengambil data log yang akan diedit
async function getEdit(id) {
    editFetch.value = false;
    logEdit.value = {};
    try {
        const response = await IntegrasiAPI.extractEmailLog(id);
        const newData = response?.data?.data?.original?.data;
        if (newData) {
            formRetry.id = id;
            formRetry.mail[0].to = newData.to;
            formRetry.mail[0].subject = newData.subject;
            formRetry.mail[0].content = newData.content;
            formRetry.mail[0].attachment = newData.attachment || [];
            formRetry.mail[0].priority = newData.priority;
            formRetry.secret = newData.secret;
            editFetch.value = true;
        }
    } catch (error) {
        editFetch.value = false;
    }
}

// Fungsi untuk validasi form retry
function validateRetryForm() {
    validationErrors.value = {};
    let isValid = true;

    // Validasi secret
    if (!formRetry.secret) {
        validationErrors.value.secret = "Secret key wajib diisi.";
        isValid = false;
    }

    // Validasi mail array
    if (
        !formRetry.mail ||
        !Array.isArray(formRetry.mail) ||
        formRetry.mail.length === 0
    ) {
        validationErrors.value.mail = 'Data email ("mail") wajib diisi.';
        isValid = false;
        return isValid;
    }

    // Validasi setiap mail
    formRetry.mail.forEach((mail, idx) => {
        // to
        if (!mail.to) {
            validationErrors.value[`mail.${idx}.to`] =
                'Email penerima ("kepada") wajib diisi.';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail.to)) {
            validationErrors.value[`mail.${idx}.to`] =
                'Format email pada kolom "kepada" tidak valid.';
            isValid = false;
        }
        // content
        if (mail.content && typeof mail.content !== "string") {
            validationErrors.value[`mail.${idx}.content`] =
                'Isi email ("content") harus berupa teks.';
            isValid = false;
        }
        // subject
        if (mail.subject && typeof mail.subject !== "string") {
            validationErrors.value[`mail.${idx}.subject`] =
                'Subjek email ("subject") harus berupa teks.';
            isValid = false;
        }
        // priority
        if (!mail.priority) {
            validationErrors.value[`mail.${idx}.priority`] =
                "Prioritas email wajib diisi.";
            isValid = false;
        } else if (typeof mail.priority !== "string") {
            validationErrors.value[`mail.${idx}.priority`] =
                "Prioritas email harus berupa teks.";
            isValid = false;
        } else if (!["low", "medium", "high"].includes(mail.priority)) {
            validationErrors.value[`mail.${idx}.priority`] =
                "Prioritas email harus salah satu dari: low, medium, high.";
            isValid = false;
        }
        // attachment
        if (mail.attachment && !Array.isArray(mail.attachment)) {
            validationErrors.value[`mail.${idx}.attachment`] =
                "Attachment harus berupa array.";
            isValid = false;
        }
        if (Array.isArray(mail.attachment)) {
            mail.attachment.forEach((att, attIdx) => {
                if (
                    att &&
                    typeof att === "string" &&
                    !/^https?:\/\/[^\s$.?#].[^\s]*$/.test(att)
                ) {
                    validationErrors.value[`mail.${idx}.attachment.${attIdx}`] =
                        "Setiap attachment harus berupa URL yang valid.";
                    isValid = false;
                }
            });
        }
    });

    return isValid;
}

// Fungsi untuk mengirim ulang email
async function handleRetry() {
    if (!validateRetryForm()) {
        return;
    }
    try {
        const response = await IntegrasiAPI.retrySendEmail(formRetry);
        if (response.data.success === true) {
            emit(
                "notification",
                "success",
                "Berhasil!",
                response.data.message || "Email masuk kedalam antrian"
            );
        } else {
            emit(
                "notification",
                "danger",
                "Gagal!",
                response.data.message || "Email gagal dikirim"
            );
        }
    } catch (error) {
        if (
            error.response &&
            (error.response.status === 500 || error.response.status === 503)
        ) {
            validationErrors.value.general =
                error.response.data.message ||
                "Terjadi kesalahan pada server (RabbitMQ).";
        }
        emit(
            "notification",
            "danger",
            "Gagal!",
            error?.response?.data?.message || "Terjadi kesalahan"
        );
    } finally {
        showEditModal.value = false;
        formRetry.reset();
        emit("refresh");
    }
}

function clearRetryError(field) {
    if (validationErrors.value[field]) {
        validationErrors.value[field] = "";
    }
}
</script>
