<template>
    <!-- Modal utama -->
    <div
        v-show="show"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="closeModal"
    >
        <div
            class="w-full max-w-4xl transform transition-all duration-300 mt-16 mb-4"
        >
            <!-- Konten Modal -->
            <div
                class="bg-white rounded-2xl shadow-2xl overflow-hidden"
                ref="modalExcel"
            >
                <!-- Modal header -->
                <div
                    class="px-8 py-6 bg-gradient-to-r from-emerald-500 to-teal-500 relative overflow-hidden"
                >
                    <div class="flex items-center justify-between relative">
                        <div class="flex items-center space-x-3">
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
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">
                                    Import Excel
                                </h3>
                                <p class="text-white/80 text-sm">
                                    Impor data dari file Excel Anda
                                </p>
                            </div>
                        </div>
                        <button
                            @click="closeModal"
                            class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-xl transition-all duration-200 focus:outline-none"
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
                </div>

                <!-- Modal body -->
                <div class="p-8">
                <div v-if="generalError"
                    class="w-full mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm"
                    role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm text-red-700 space-y-1">
                                <div class="flex items-start space-x-2">
                                    <span class="w-1.5 h-1.5 bg-red-400 rounded-full mt-2 flex-shrink-0"></span>
                                    <span>{{ generalError }}</span>
                                </div>
                            </div>
                        </div>
                        <button @click="generalError = ''"
                            class="flex-shrink-0 ml-4 text-red-400 hover:text-red-600 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                    <!-- Two Column Layout -->
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Left Column - Download Template -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 mb-6">
                                <div
                                    class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-sm font-bold"
                                >
                                    1
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">
                                    Download Template
                                </h4>
                            </div>
                            <div
                                v-if="downloadMessage"
                                class="mb-4 px-4 py-3 rounded-xl flex items-center justify-center gap-3 font-semibold border shadow-sm"
                                :class="{
                                    'bg-gradient-to-r from-emerald-50 to-emerald-100 text-emerald-800 border-emerald-300':
                                        downloadMessage.includes('berhasil'),
                                    'bg-gradient-to-r from-red-50 to-red-100 text-red-800 border-red-300':
                                        downloadMessage.includes('kesalahan'),
                                }"
                            >
                                <!-- Icon Success -->
                                <div
                                    v-if="downloadMessage.includes('berhasil')"
                                    class="w-8 h-8 bg-emerald-500 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
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
                                </div>

                                <!-- Icon Error -->
                                <div
                                    v-else
                                    class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center"
                                >
                                    <svg
                                        class="w-5 h-5 text-white"
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
                                </div>

                                <span class="text-sm flex-1">{{
                                    downloadMessage
                                }}</span>
                            </div>
                            <div
                                class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl p-6"
                            >
                                <div class="text-center space-y-4">
                                    <div
                                        class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto"
                                    >
                                        <svg
                                            class="w-8 h-8 text-emerald-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                            />
                                        </svg>
                                    </div>

                                    <div>
                                        <h5
                                            class="text-lg font-semibold text-gray-900 mb-2"
                                        >
                                            Template Excel
                                        </h5>
                                        <p
                                            class="text-sm text-gray-600 mb-6 leading-relaxed"
                                        >
                                            Download template untuk memastikan
                                            format data Anda sesuai dengan
                                            sistem kami
                                        </p>
                                    </div>

                                    <button
                                        @click="downloadTemplate"
                                        class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-emerald-200 hover:shadow-lg transform hover:-translate-y-0.5"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                            />
                                        </svg>
                                        Download Template
                                    </button>
                                </div>
                            </div>

                            <!-- Tips Section -->
                            <div
                                class="bg-blue-50 border border-blue-200 rounded-xl p-4"
                            >
                                <div class="flex items-start space-x-3">
                                    <svg
                                        class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    <div>
                                        <h6
                                            class="text-sm font-semibold text-blue-900 mb-1"
                                        >
                                            Tips:
                                        </h6>
                                        <ul
                                            class="text-xs text-blue-700 space-y-1"
                                        >
                                            <li>
                                                • Jangan mengubah header kolom
                                            </li>
                                            <li>
                                                • Pastikan format data sesuai
                                                template
                                            </li>
                                            <li>
                                                • Kosongkan baris pertama untuk
                                                header
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Upload File -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-3 mb-6">
                                <div
                                    class="w-8 h-8 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center text-sm font-bold"
                                >
                                    2
                                </div>
                                <h4 class="text-lg font-semibold text-gray-900">
                                    Upload File Excel
                                </h4>
                            </div>

                            <!-- File Preview (when file is selected) -->
                            <div v-if="selectedFile" class="mb-6">
                                <div
                                    class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-4 overflow-hidden flex-1"
                                        >
                                            <div
                                                class="flex-shrink-0 p-3 bg-emerald-100 rounded-xl"
                                            >
                                                <svg
                                                    class="w-8 h-8 text-emerald-600"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                    />
                                                </svg>
                                            </div>
                                            <div class="flex-1 overflow-hidden">
                                                <p
                                                    class="text-sm font-semibold text-emerald-800 truncate"
                                                >
                                                    {{ selectedFile.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-emerald-600 mt-1"
                                                >
                                                    {{
                                                        (
                                                            selectedFile.size /
                                                            1024 /
                                                            1024
                                                        ).toFixed(2)
                                                    }}
                                                    MB
                                                </p>
                                            </div>
                                        </div>
                                        <button
                                            @click="cancelFileUpload"
                                            class="flex-shrink-0 ml-3 p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition-colors"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Messages (show only if file selected) -->
                            <div
                                v-if="
                                    selectedFile && (fileError || formatError)
                                "
                                class="mt-2 mb-6 p-6 bg-red-50 border border-red-200 rounded-2xl"
                            >
                                <div class="flex items-center space-x-3 mb-4">
                                    <div
                                        class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"
                                    >
                                        <svg
                                            class="w-5 h-5 text-red-500"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <h6 class="font-semibold text-red-800">
                                        Harap perbaiki kesalahan berikut:
                                    </h6>
                                </div>
                                <div
                                    class="max-h-64 overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-red-300 scrollbar-track-red-100 scrollbar-thumb-rounded"
                                >
                                    <ul v-if="fileError" class="ml-6 space-y-2">
                                        <li
                                            v-for="err in fileError"
                                            class="text-sm text-red-600 list-disc"
                                        >
                                            {{ err }}
                                        </li>
                                    </ul>
                                    <div v-if="formatError" class="space-y-4">
                                        <div
                                            v-for="err in formatError"
                                            class="ml-2"
                                        >
                                            <p
                                                class="text-sm font-medium text-red-700 mb-2"
                                            >
                                                Kesalahan excel pada nomor baris
                                                {{ err.row }}
                                            </p>
                                            <ul class="ml-6 space-y-1">
                                                <li
                                                    v-for="message in err.errors"
                                                    class="text-sm text-red-600 list-disc"
                                                >
                                                    {{ message }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Area -->
                            <div
                                v-if="!selectedFile"
                                class="bg-gradient-to-br from-teal-50 to-cyan-50 border border-teal-200 rounded-2xl p-8"
                            >
                                <div class="text-center space-y-4">
                                    <div
                                        class="w-16 h-16 bg-teal-100 rounded-2xl flex items-center justify-center mx-auto"
                                    >
                                        <svg
                                            class="w-8 h-8 text-teal-600"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                            />
                                        </svg>
                                    </div>

                                    <div>
                                        <h5
                                            class="text-lg font-semibold text-gray-900 mb-2"
                                        >
                                            Upload File Excel
                                        </h5>
                                        <p class="text-sm text-gray-600 mb-4">
                                            Unggah file Excel Anda sesuai dengan
                                            format yang didukung: .xlsx dan .xls
                                        </p>
                                    </div>

                                    <label
                                        for="file_input"
                                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-semibold rounded-xl cursor-pointer transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-teal-200 hover:shadow-lg transform hover:-translate-y-0.5"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2"
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
                                        Pilih File Excel
                                    </label>
                                    <input
                                        @change="handleFileUpload"
                                        class="hidden"
                                        id="file_input"
                                        type="file"
                                        ref="fileInput"
                                        accept=".xlsx, .xls, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                                    />
                                </div>
                            </div>

                            <!-- File Format Info -->
                            <div
                                v-if="!fileError && !formatError"
                                class="bg-amber-50 border border-amber-200 rounded-xl p-4"
                            >
                                <div class="flex items-start space-x-3">
                                    <svg
                                        class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L1.732 15.5c-.77.833.192 2.5 1.732 2.5z"
                                        />
                                    </svg>
                                    <div>
                                        <h6
                                            class="text-sm font-semibold text-amber-900 mb-1"
                                        >
                                            Catatan Penting:
                                        </h6>
                                        <p class="text-xs text-amber-700">
                                            Pastikan file Excel Anda menggunakan
                                            template yang telah didownload untuk
                                            menghindari error saat import.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="flex justify-end space-x-4 pt-4 border-t border-gray-200 mt-6"
                    >
                        <button
                            @click="uploadFile"
                            :disabled="!selectedFile || isLoading"
                            class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center space-x-2 focus:outline-none focus:ring-4 focus:ring-emerald-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none disabled:hover:shadow-lg"
                        >
                            <svg
                                v-if="isLoading"
                                class="animate-spin h-5 w-5 text-white"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            <svg
                                v-else
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                />
                            </svg>
                            <span>{{
                                isLoading ? "Mengupload..." : "Upload File"
                            }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
// Import library Vue
import { ref, watch } from "vue";
import * as IntegrasiAPI from "../api/IntegrasiAPI";

// Props untuk menerima data dari parent component
const props = defineProps({
    show: Boolean,
});

// Emit untuk mengirim event ke parent component
const emit = defineEmits(["close", "notification"]);

// State untuk menyimpan file, error, dan elemen input
const selectedFile = ref(null);
const fileInput = ref(null);
const fileError = ref(null);
const formatError = ref(null);
const error = ref("");
const modalExcel = ref(null);
const isLoading = ref(false);
const generalError = ref("");

// URL API dari environment variable
const baseUrl = import.meta.env.VITE_APP_URL;

// Fungsi untuk menangani download template
const downloadMessage = ref("");

function downloadTemplate() {
    try {
        const templateUrl = `${baseUrl}/template/template_import.xlsx`;

        // Membuat element anchor untuk download
        const link = document.createElement("a");
        link.href = templateUrl;
        link.download = "template_import.xlsx";
        link.target = "_blank";

        // Menambahkan ke DOM, klik, dan hapus
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Tampilkan pesan sukses di modal
        downloadMessage.value = "Template Excel berhasil diunduh!";
        setTimeout(() => (downloadMessage.value = ""), 3000);
    } catch (error) {
        console.error("Error downloading template:", error);
        downloadMessage.value = "Terjadi kesalahan saat mengunduh template.";
        setTimeout(() => (downloadMessage.value = ""), 3000);
    }
}

// Fungsi untuk menangani upload file
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const fileExtension = file.name.split(".").pop().toLowerCase();
        const isExcel = ["xlsx", "xls"].includes(fileExtension);
        if (!isExcel) {
            fileError.value = [
                "Hanya file Excel (.xlsx, .xls) yang diizinkan.",
            ];
            if (fileInput.value) {
                fileInput.value.value = null;
            }
            return;
        }
        fileError.value = null;
        selectedFile.value = file;
    }
}

// Fungsi untuk membatalkan upload file
function cancelFileUpload() {
    selectedFile.value = null;
    if (fileInput.value) {
        fileInput.value.value = null;
    }
    fileError.value = null;
    formatError.value = null;
}

// Fungsi untuk mengunggah file ke server
async function uploadFile() {
    if (!selectedFile.value) {
        error.value = "Silakan pilih file terlebih dahulu.";
        return;
    }
    isLoading.value = true;
    const formData = new FormData();
    formData.append("excel_file", selectedFile.value);
    try {
        const response = await IntegrasiAPI.uploadExcel(formData);
        emit("notification", "success", "Berhasil!", response.data.message);
        selectedFile.value = null;
        emit("close");
        if (fileInput.value) {
            fileInput.value.value = null;
        }
        generalError.value = ""; 
    } catch (error) {
        fileError.value = null;
        formatError.value = null;
        generalError.value = ""; 

        try {
            const errMessage = JSON.parse(error.request.response);
            if (
                errMessage.original &&
                errMessage.original.messages &&
                errMessage.original.messages.excel_file
            ) {
                fileError.value = errMessage.original.messages.excel_file;
            } else if (errMessage.messages) {
                formatError.value = errMessage.messages;
            } else {
                generalError.value =
                    errMessage.message ||
                    "Terjadi kesalahan saat mengunggah berkas.";
            }
        } catch (parseError) {
            if (
                error.response &&
                (error.response.status === 500 || error.response.status === 503)
            ) {
                generalError.value =
                    error.response.data?.message ||
                    "Terjadi kesalahan pada server (RabbitMQ).";
            } else {
                generalError.value = "Terjadi kesalahan saat mengunggah berkas.";
            }
        }
    } finally {
        isLoading.value = false;
    }
}

// Fungsi untuk menutup modal
function closeModal() {
    emit("close");
}

// Watcher untuk reset state saat modal ditutup
watch(
    () => props.show,
    (newValue) => {
        if (!newValue) {
            fileError.value = null;
            formatError.value = null;
            error.value = "";
            generalError.value = "";
            selectedFile.value = null;
            isLoading.value = false;
            if (fileInput.value) {
                fileInput.value.value = null;
            }
        }
    }
);
</script>
