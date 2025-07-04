<template>
    <div>
        <!-- Background untuk modal -->
        <div
            @click="emit('close')"
            class="fixed top-0 left-0 right-0 z-50 flex items-center justify-center w-full max-h-full p-4 overflow-x-hidden overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm md:inset-0"
        ></div>

        <!-- Kontainer modal -->
        <div
            class="fixed z-50 w-full max-h-full -translate-x-1/2 -translate-y-1/2 left-1/2 top-1/2 max-w-7xl"
        >
            <!-- Konten Modal -->
            <form class="relative bg-white rounded-xl shadow">
                <!-- Header Modal -->
                <div
                    class="flex items-center justify-between p-4 border-b bg-gradient-to-r from-emerald-500 to-teal-500 rounded-t-xl md:p-5"
                >
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center"
                        >
                            <svg
                                class="w-7 h-7 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-1">
                                <span v-if="!formModal"
                                    >Kirim Email Manual</span
                                >
                                <span
                                    v-else-if="formModal && emailIndex === null"
                                    >Form Tambah Email Baru</span
                                >
                                <span v-else>Form Edit Email</span>
                            </h3>
                            <p class="text-white/80 text-sm">
                                <span v-if="!formModal"
                                    >Tambahkan dan kirim email secara
                                    individual</span
                                >
                                <span
                                    v-else-if="formModal && emailIndex === null"
                                    >Isi form untuk menambahkan email baru</span
                                >
                                <span v-else
                                    >Edit dan simpan perubahan email</span
                                >
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="emit('close')"
                        class="text-white/80 hover:text-white hover:bg-white/20 p-3 rounded-xl transition-all duration-200 focus:outline-none"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <!-- Pesan Error Umum -->
                <div
                    v-if="
                        Object.keys(errorMessage).length > 0 &&
                        formModal === false
                    "
                    class="mx-6 mt-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm"
                    role="alert"
                >
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg
                                class="w-6 h-6 text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
                                />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm text-red-700 space-y-1">
                                <template
                                    v-for="(errs, key) in errorMessage"
                                    :key="key"
                                >
                                    <div
                                        v-for="err in errs"
                                        :key="err"
                                        class="flex items-start space-x-2"
                                    >
                                        <span
                                            class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 flex-shrink-0"
                                        ></span>
                                        <span>{{ err }}</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <button
                            @click="errorMessage = {}"
                            class="flex-shrink-0 ml-4 text-red-400 hover:text-red-600 transition-colors duration-200"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Pesan error untuk secret key -->
                <div
                    v-if="invalidKey && formModal === false"
                    class="mx-6 mt-4 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm"
                    role="alert"
                >
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg
                                class="w-6 h-6 text-red-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"
                                />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-red-700">
                                {{
                                    invalidKey === "Secret key tidak valid"
                                        ? "Secret key yang Anda masukkan tidak valid"
                                        : invalidKey ===
                                          "Status aplikasi disabled"
                                        ? "Status aplikasi saat ini disabled"
                                        : invalidKey ===
                                          "Secret key wajib diisi"
                                        ? "Secret key wajib diisi untuk melanjutkan"
                                        : invalidKey
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Tabel Daftar Email -->
                <div v-show="!formModal" class="relative p-6 overflow-x-auto">
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
                    >
                        <!-- Header Section -->
                        <div
                            class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-7 h-7 text-gray-900"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 6h16M4 10h16M4 14h16M4 18h16"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-lg font-semibold text-gray-900"
                                        >
                                            Daftar Email
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            Email yang ditambahkan akan muncul
                                            di sini
                                        </p>
                                    </div>
                                </div>

                                <!-- Badge jumlah email -->
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="flex items-center space-x-2 bg-emerald-500 text-white px-4 py-2 rounded-xl shadow-sm"
                                    >
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                        <span class="text-sm font-semibold"
                                            >Total
                                            {{ emails.mail.length }} Email</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table Content -->
                        <div class="overflow-x-auto">
                            <table class="w-full table-fixed">
                                <!-- Tambahkan table-fixed -->
                                <thead
                                    class="bg-gray-50/50 border-b border-gray-100"
                                >
                                    <tr>
                                        <th
                                            class="w-16 px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <span>No</span>
                                            </div>
                                        </th>
                                        <th
                                            class="w-1/3 px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <span>Subjek</span>
                                            </div>
                                        </th>
                                        <th
                                            class="w-1/3 px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <span>Kepada</span>
                                            </div>
                                        </th>
                                        <th
                                            class="w-24 px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <span>Prioritas</span>
                                            </div>
                                        </th>
                                        <th
                                            class="w-32 px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                                        >
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <span>Aksi</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    <!-- Baris Email -->
                                    <tr
                                        v-for="(item, index) in emails.mail"
                                        :key="index"
                                        :class="{
                                            'bg-red-50':
                                                Object.keys(errorMessage).some(
                                                    (key) =>
                                                        key.startsWith(
                                                            `mail.${index}.`
                                                        )
                                                ) ||
                                                (invalidKey &&
                                                    emails.mail.length > 0),
                                        }"
                                        class="hover:bg-gray-50/50 transition-colors duration-150"
                                    >
                                        <td class="w-16 px-6 py-4">
                                            <div
                                                class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-sm font-semibold text-emerald-700"
                                            >
                                                {{ index + 1 }}
                                            </div>
                                        </td>
                                        <td class="w-1/3 px-6 py-4">
                                            <div class="flex items-start">
                                                <div>
                                                    <div
                                                        class="text-sm font-medium text-gray-900 line-clamp-2"
                                                    >
                                                        {{
                                                            item.subject ||
                                                            "Tanpa Subjek"
                                                        }}
                                                    </div>
                                                    <div
                                                        v-if="item.content"
                                                        class="text-xs text-gray-500 mt-1 line-clamp-1"
                                                    >
                                                        {{
                                                            item.content.substring(
                                                                0,
                                                                50
                                                            )
                                                        }}{{
                                                            item.content
                                                                .length > 50
                                                                ? "..."
                                                                : ""
                                                        }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="w-1/3 px-6 py-4">
                                            <div
                                                class="flex items-center space-x-2"
                                            >
                                                <div
                                                    class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"
                                                >
                                                    <svg
                                                        class="w-4 h-4 text-blue-600"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                        />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div
                                                        class="text-sm font-medium text-gray-900 truncate max-w-48"
                                                    >
                                                        {{ item.to }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="w-24 px-6 py-4">
                                            <span
                                                :class="{
                                                    'bg-green-100 text-green-800':
                                                        item.priority ===
                                                        'high',
                                                    'bg-yellow-100 text-yellow-800':
                                                        item.priority ===
                                                        'medium',
                                                    'bg-red-100 text-red-800':
                                                        item.priority === 'low',
                                                }"
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                                            >
                                                <svg
                                                    :class="{
                                                        'text-green-500':
                                                            item.priority ===
                                                            'high',
                                                        'text-yellow-500':
                                                            item.priority ===
                                                            'medium',
                                                        'text-red-500':
                                                            item.priority ===
                                                            'low',
                                                    }"
                                                    class="w-3 h-3 mr-1"
                                                    fill="currentColor"
                                                    viewBox="0 0 8 8"
                                                >
                                                    <circle
                                                        cx="4"
                                                        cy="4"
                                                        r="3"
                                                    />
                                                </svg>
                                                {{
                                                    item.priority
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    item.priority.slice(1)
                                                }}
                                            </span>
                                        </td>
                                        <td class="w-32 px-6 py-4">
                                            <div
                                                class="flex items-center space-x-1"
                                            >
                                                <a
                                                    @click="
                                                        (formModal = true),
                                                            detailEmail(index),
                                                            (emailIndex = index)
                                                    "
                                                    href="#"
                                                    class="inline-flex items-center px-2 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-lg transition-colors duration-200 no-underline"
                                                >
                                                    <svg
                                                        class="w-3 h-3 mr-1"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                        />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <a
                                                    @click="removeEmail(index)"
                                                    href="#"
                                                    class="inline-flex items-center px-2 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-lg transition-colors duration-200 no-underline"
                                                >
                                                    <svg
                                                        class="w-3 h-3 mr-1"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />
                                                    </svg>
                                                    Hapus
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="emails.mail.length === 0">
                                        <td
                                            colspan="5"
                                            class="px-6 py-12 text-center"
                                        >
                                            <div
                                                class="flex flex-col items-center justify-center space-y-4"
                                            >
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center"
                                                >
                                                    <svg
                                                        class="w-8 h-8 text-gray-400"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                        />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h3
                                                        class="text-sm font-medium text-gray-900 mb-1"
                                                    >
                                                        Belum ada email
                                                    </h3>
                                                    <p
                                                        class="text-sm text-gray-500 mb-4"
                                                    >
                                                        Mulai dengan menambahkan
                                                        email pertama Anda
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Baris tambah email -->
                                    <tr>
                                        <td
                                            @click="
                                                newEmail(), (formModal = true)
                                            "
                                            class="px-2 py-2 cursor-pointer group bg-emerald-50 hover:bg-emerald-100 transition-all duration-200"
                                            colspan="5"
                                        >
                                            <div
                                                class="mt-2 flex items-center justify-center space-x-4"
                                            >
                                                <svg
                                                    class="w-8 h-8 text-emerald-600 group-hover:text-emerald-700 transition-colors duration-200"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                                    />
                                                </svg>
                                                <div
                                                    class="text-md font-semibold text-emerald-700 group-hover:text-emerald-800 transition-colors duration-200"
                                                >
                                                    Tambah Email Baru
                                                </div>
                                            </div>

                                            <!-- Animated border bottom -->
                                            <div
                                                class="w-0 group-hover:w-full h-0.5 bg-gradient-to-r from-emerald-400 to-teal-400 transition-all duration-300 mx-auto mt-3"
                                            ></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form modal untuk tambah/edit email -->
                <div
                    v-show="formModal"
                    class="flex flex-col p-4 md:p-5 gap-x-5 gap-y-4 max-h-[70vh] overflow-auto"
                >
                    <div>
                        <label
                            for="subject"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Subjek</label
                        >
                        <input
                            type="text"
                            id="subject"
                            v-model="email.subject"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Contoh"
                        />
                    </div>
                    <div>
                        <label
                            for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900"
                        >
                            Kepada <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="first_name"
                            v-model="email.to"
                            @focus="
                                emailIndex !== null
                                    ? clearError('mail.' + emailIndex + '.to')
                                    : null
                            "
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="nama@contoh.com"
                        />
                        <div
                            v-if="
                                emailIndex !== null &&
                                detailErrors &&
                                detailErrors['mail.' + emailIndex + '.to']
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
                                        <span
                                            v-for="toErr in detailErrors[
                                                'mail.' + emailIndex + '.to'
                                            ]"
                                            :key="toErr"
                                        >
                                            {{
                                                toErr.replace(
                                                    `mail.${emailIndex}.`,
                                                    ""
                                                )
                                            }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            for="countries"
                            class="block mb-2 text-sm font-medium text-gray-900"
                        >
                            Pilih Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="countries"
                            v-model="email.priority"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                        <div
                            v-if="
                                emailIndex !== null &&
                                detailErrors['mail.' + emailIndex + '.priority']
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
                                        <span
                                            v-for="priorityErr in detailErrors[
                                                'mail.' +
                                                    emailIndex +
                                                    '.priority'
                                            ]"
                                            :key="priorityErr"
                                        >
                                            {{
                                                priorityErr.replace(
                                                    `mail.${emailIndex}.`,
                                                    ""
                                                )
                                            }}<br />
                                        </span>
                                    </span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            for="secret"
                            class="block mb-2 text-sm font-medium text-gray-900"
                        >
                            Secret key <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="secret"
                            v-model="emails.secret"
                            @focus="clearError('secret')"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Masukkan Secret key"
                            required
                        />
                        <div
                            v-if="invalidKey && formModal"
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
                                    <span>{{ invalidKey }}</span>
                                </div>
                                <div
                                    class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                ></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label
                            for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900"
                            >Lampiran</label
                        >
                        <div class="flex flex-col gap-y-2" v-auto-animate>
                            <div
                                v-for="(item, index) in email.attachment"
                                :key="index"
                                class="flex gap-x-2"
                            >
                                <div class="w-full">
                                    <input
                                        type="text"
                                        v-model="email.attachment[index]"
                                        @focus="
                                            emailIndex !== null
                                                ? clearError(
                                                      'mail.' +
                                                          emailIndex +
                                                          '.attachment.' +
                                                          index
                                                  )
                                                : null
                                        "
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                        placeholder="Lampiran URL..."
                                        required
                                    />
                                    <div
                                        v-if="
                                            emailIndex !== null &&
                                            detailErrors[
                                                'mail.' +
                                                    emailIndex +
                                                    '.attachment.' +
                                                    index
                                            ]
                                        "
                                        class="relative mt-2"
                                    >
                                        <div
                                            class="bg-red-500 text-white text-xs rounded-lg py-2 px-3 relative shadow-lg"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
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
                                                    <span
                                                        v-for="attachmentErr in detailErrors[
                                                            'mail.' +
                                                                emailIndex +
                                                                '.attachment.' +
                                                                index
                                                        ]"
                                                        :key="attachmentErr"
                                                    >
                                                        {{
                                                            attachmentErr.replace(
                                                                `mail.${emailIndex}.`,
                                                                ""
                                                            )
                                                        }}<br />
                                                    </span>
                                                </span>
                                            </div>
                                            <div
                                                class="absolute -top-1 left-4 w-2 h-2 bg-red-500 rotate-45"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                                <button
                                    @click="removeAttachment(index)"
                                    type="button"
                                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2.5 focus:outline-none"
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
                            </div>
                            <button
                                type="button"
                                @click="addAttachment"
                                class="flex items-center justify-center gap-2 text-blue-500 border border-gray-300 bg-blue-50/70 hover:bg-blue-100/50 hover:text-blue-700 font-semibold rounded-lg px-4 py-2 mt-2 transition-all duration-150 shadow-sm w-full"
                            >
                                <svg
                                    class="w-4 h-4"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    viewBox="0 0 24 24"
                                >
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                                Tambah Lampiran
                            </button>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label
                            for="message"
                            class="block mb-2 text-sm font-medium text-gray-900"
                        >
                            Isi</label
                        >
                        <textarea
                            id="message"
                            rows="4"
                            v-model="email.content"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Tulis isi yang akan di kirim..."
                        ></textarea>
                    </div>
                </div>

                <!-- Footer Modal -->
                <div
                    class="flex justify-end space-x-4 pt-8 border-t border-gray-200 px-6 pb-6"
                >
                    <!-- Tombol Batal -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        @click="formModal = false"
                        v-if="formModal"
                        class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 hover:bg-gray-200 hover:border-gray-400 hover:shadow-md rounded-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-gray-200"
                    >
                        Batal
                    </button>

                    <!-- Tombol Tambah Baru -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        v-if="formModal && emailIndex === null"
                        @click="addEmail(email)"
                        class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 rounded-xl transition-all duration-200 flex items-center space-x-2 focus:outline-none focus:ring-4 focus:ring-emerald-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                        <span>Tambah</span>
                    </button>

                    <!-- Tombol Simpan Perubahan Edit -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        @click="saveEdit"
                        v-if="formModal && emailIndex !== null"
                        class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 rounded-xl transition-all duration-200 flex items-center space-x-2 focus:outline-none focus:ring-4 focus:ring-emerald-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                        <span>Simpan</span>
                    </button>

                    <!-- Tombol Kirim Email -->
                    <button
                        data-modal-hide="extralarge-modal"
                        type="button"
                        @click="validateBeforeSubmit() && handleSubmit()"
                        v-if="!formModal"
                        class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 rounded-xl transition-all duration-200 flex items-center space-x-2 focus:outline-none focus:ring-4 focus:ring-emerald-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <svg
                            class="w-5 h-5 transform rotate-90"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                            />
                        </svg>
                        <span>Kirim</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
// Import modul dan library yang diperlukan
import { ref, onMounted, watch } from "vue";
import { vAutoAnimate } from "@formkit/auto-animate";
import * as IntegrasiAPI from "../api/IntegrasiAPI";

// Base URL untuk API
const baseUrl = import.meta.env.VITE_APP_URL;

// State untuk mengontrol modal form
const formModal = ref(false);

// Emit event ke parent component
const emit = defineEmits(["close", "notification"]);

// State untuk menyimpan data email yang akan dikirim
const emails = ref({
    secret: "",
    mail: [],
});

// State untuk menyimpan data email yang sedang diedit atau ditambahkan
const email = ref({
    subject: "",
    to: "",
    priority: "low",
    content: "",
    attachment: [],
});

// State untuk pesan sukses, error, dan validasi
const successMessage = ref("");
const errorMessage = ref({});
const invalidKey = ref("");
const detailErrors = ref({});
const emailIndex = ref(null);

// Lifecycle hook untuk mengambil data email dari localStorage
onMounted(() => {
    const storedEmails = localStorage.getItem("emails");
    if (storedEmails) {
        emails.value = JSON.parse(storedEmails);
    }
});

// Watcher untuk menyimpan perubahan data emails ke localStorage
watch(
    emails,
    (newEmails) => {
        localStorage.setItem("emails", JSON.stringify(newEmails));
    },
    { deep: true }
);

// Fungsi untuk menghapus error ketika input diklik/difocus
function clearError(field) {
    // Pastikan field adalah string yang valid
    if (field && typeof field === "string" && detailErrors.value[field]) {
        delete detailErrors.value[field];
    }
    if (field === "secret" && invalidKey.value) {
        invalidKey.value = "";
    }
}

// Fungsi untuk mengirim email
async function handleSubmit() {
    if (!validateBeforeSubmit()) {
        return;
    }
    invalidKey.value = "";
    try {
        const response = await IntegrasiAPI.retrySendEmail(emails.value);
        emit("notification", "success", "Berhasil!", response.data.message);
        emit("close");
    } catch (error) {
        if (
            error.response &&
            error.response.data &&
            error.response.data.errors
        ) {
            errorMessage.value = error.response.data.errors;
        } else if (error.response && error.response.data) {
            if (error.response.status === 422) {
                invalidKey.value =
                    error.response.data.error || error.response.data.message;
            }
            else if (
                error.response.status === 500 ||
                error.response.status === 503
            ) {
                errorMessage.value = {
                    general: [
                        error.response.data.message ||
                        "Terjadi kesalahan pada server (RabbitMQ)."
                    ],
                };
                emit(
                    "notification",
                    "danger",
                    "Gagal!",
                    error.response.data.message ||
                    "Terjadi kesalahan pada server (RabbitMQ)."
                );
            } else {
                errorMessage.value = {
                    general: [
                        error.response.data.error ||
                        error.response.data.message ||
                        "Terjadi kesalahan yang tidak diketahui"
                    ],
                };
                emit(
                    "notification",
                    "danger",
                    "Gagal!",
                    error.response.data.error ||
                    error.response.data.message ||
                    "Terjadi kesalahan yang tidak diketahui"
                );
            }
        } else {
            errorMessage.value = {
                general: ["Terjadi kesalahan yang tidak diketahui"],
            };
            emit(
                "notification",
                "danger",
                "Gagal!",
                "Terjadi kesalahan yang tidak diketahui"
            );
        }
    }
}

// Fungsi untuk validasi data email
function validateEmail() {
    let isValid = true;
    const tempErrors = {};
    const priorityList = ["low", "medium", "high"];

    // Pastikan emailIndex tidak null saat validasi
    const currentIndex =
        emailIndex.value !== null ? emailIndex.value : emails.value.mail.length;

    // Validasi secret key
    if (!emails.value.secret) {
        invalidKey.value = "Secret key wajib diisi";
        isValid = false;
    } else {
        invalidKey.value = "";
    }

    // Validasi alamat email penerima
    if (!email.value.to) {
        if (!tempErrors[`mail.${currentIndex}.to`]) {
            tempErrors[`mail.${currentIndex}.to`] = [];
        }
        tempErrors[`mail.${currentIndex}.to`].push(
            'Email penerima ("kepada") wajib diisi'
        );
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.to)) {
        if (!tempErrors[`mail.${currentIndex}.to`]) {
            tempErrors[`mail.${currentIndex}.to`] = [];
        }
        tempErrors[`mail.${currentIndex}.to`].push(
            'Format email tidak valid untuk kolom "kepada"'
        );
        isValid = false;
    }

    // Validasi prioritas
    if (!email.value.priority || !priorityList.includes(email.value.priority)) {
        if (!tempErrors[`mail.${currentIndex}.priority`]) {
            tempErrors[`mail.${currentIndex}.priority`] = [];
        }
        tempErrors[`mail.${currentIndex}.priority`].push(
            "Prioritas wajib diisi dan harus salah satu dari: low, medium, high"
        );
        isValid = false;
    }

    if (
        email.value.attachment &&
        Array.isArray(email.value.attachment) &&
        email.value.attachment.length > 0
    ) {
        for (let i = 0; i < email.value.attachment.length; i++) {
            const url = email.value.attachment[i];
            // Pastikan url tidak null/undefined dan tidak kosong
            if (
                url &&
                url.trim() !== "" &&
                !/^https?:\/\/[^\s$.?#].[^\s]*$/.test(url)
            ) {
                if (!tempErrors[`mail.${currentIndex}.attachment.${i}`]) {
                    tempErrors[`mail.${currentIndex}.attachment.${i}`] = [];
                }
                tempErrors[`mail.${currentIndex}.attachment.${i}`].push(
                    "Setiap attachment harus berupa URL yang valid."
                );
                isValid = false;
                break;
            }
        }
    }

    detailErrors.value = tempErrors;
    return isValid;
}

// Fungsi untuk menambahkan email baru
function addEmail(newEmail) {
    // Pastikan emailIndex untuk email baru
    emailIndex.value = emails.value.mail.length;

    // Pastikan attachment adalah array
    if (!newEmail.attachment || !Array.isArray(newEmail.attachment)) {
        newEmail.attachment = [];
    }

    // Filter attachment yang kosong
    newEmail.attachment = newEmail.attachment.filter(
        (attachment) => attachment && attachment.trim() !== ""
    );

    if (validateEmail()) {
        emails.value.mail.push(newEmail);
        formModal.value = false;
        errorMessage.value = {};
        invalidKey.value = "";
        detailErrors.value = {};
    }
}

// Fungsi untuk melihat detail email
function detailEmail(index) {
    // Validasi index
    if (
        index < 0 ||
        index >= emails.value.mail.length ||
        !emails.value.mail[index]
    ) {
        console.error("Invalid email index:", index);
        return;
    }

    // Reset error terlebih dahulu
    detailErrors.value = {};

    // Ambil error yang relevan
    const relevantErrors = Object.keys(errorMessage.value || {})
        .filter((key) => key.startsWith(`mail.${index}.`))
        .reduce((acc, key) => {
            acc[key] = errorMessage.value[key];
            return acc;
        }, {});

    detailErrors.value = relevantErrors;

    // Deep copy email data dengan validasi
    const emailData = emails.value.mail[index];
    email.value = {
        subject: emailData.subject || "",
        to: emailData.to || "",
        priority: emailData.priority || "low",
        content: emailData.content || "",
        attachment: Array.isArray(emailData.attachment)
            ? [...emailData.attachment]
            : [],
    };

    emailIndex.value = index;
}

// Fungsi untuk membuat email baru
function newEmail() {
    detailErrors.value = {};
    email.value = {
        subject: "",
        to: "",
        priority: "low",
        content: "",
        attachment: [],
    };
    invalidKey.value = "";
    emailIndex.value = null;
}

// Fungsi untuk menyimpan perubahan pada email yang diedit
function saveEdit() {
    if (validateEmail()) {
        emails.value.mail[emailIndex.value] = email.value;
        formModal.value = false;
        errorMessage.value = Object.keys(errorMessage.value || {})
            .filter((key) => !key.startsWith(`mail.${emailIndex.value}.`))
            .reduce((acc, key) => {
                acc[key] = errorMessage.value[key];
                return acc;
            }, {});
        invalidKey.value = "";
        detailErrors.value = {};
    }
}

// Fungsi untuk menghapus email dari daftar
function removeEmail(index) {
    if (index >= 0 && index < emails.value.mail.length) {
        emails.value.mail.splice(index, 1);

        // Bersihkan error yang terkait dengan email yang dihapus
        const keysToRemove = Object.keys(errorMessage.value || {}).filter(
            (key) => key.startsWith(`mail.${index}.`)
        );

        keysToRemove.forEach((key) => {
            delete errorMessage.value[key];
        });

        // Update index untuk email yang tersisa
        const updatedErrors = {};
        Object.keys(errorMessage.value || {}).forEach((key) => {
            const match = key.match(/^mail\.(\d+)\./);
            if (match) {
                const emailIdx = parseInt(match[1]);
                if (emailIdx > index) {
                    const newKey = key.replace(
                        `mail.${emailIdx}.`,
                        `mail.${emailIdx - 1}.`
                    );
                    updatedErrors[newKey] = errorMessage.value[key];
                } else if (emailIdx < index) {
                    updatedErrors[key] = errorMessage.value[key];
                }
            } else {
                updatedErrors[key] = errorMessage.value[key];
            }
        });
        errorMessage.value = updatedErrors;
    }
}

// Fungsi untuk menambahkan lampiran
function addAttachment() {
    // Pastikan email.value.attachment adalah array
    if (!Array.isArray(email.value.attachment)) {
        email.value.attachment = [];
    }
    email.value.attachment.push("");
}

// Fungsi untuk menghapus lampiran
function removeAttachment(index) {
    if (
        Array.isArray(email.value.attachment) &&
        index >= 0 &&
        index < email.value.attachment.length
    ) {
        email.value.attachment.splice(index, 1);
    }
}

// Fungsi untuk validasi sebelum mengirim email
function validateBeforeSubmit() {
    // Cek jika belum ada email yang ditambahkan
    if (emails.value.mail.length === 0) {
        invalidKey.value = "Tambahkan minimal satu email";
        return false;
    }
    return true;
}
</script>
