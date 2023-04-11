<script setup>
import { ref, onMounted } from 'vue'
import logoPDF from "@/Assets/pdf.svg"
import { useStore } from "vuex"

const props = defineProps({
    lang: Object,
    file: Object,
    field: String
});

const store = useStore();

const fileTypes = Object.keys(props.file.accept)
const fileMaxSizeMb = [...new Set(Object.entries(props.file.accept).map((arr) => {
    return arr[1].maxSizeMb
}))];


const fileTempl = ref(null);
const dropzoneMsg = ref(null);
const empty = ref(null);
const dropzoneFile = ref(null);
const dropzoneLabel = ref(null);
const gallery = ref(null);
const overlay = ref(null);
const btnImageTemplate = ref(null);

let FILES = {};

const validateDropzoneFront = (file) => {
    const allowedFileTypes = Object.values(props.file.accept).map((obj) => { return obj.type });
    if (!allowedFileTypes.includes(file.type)) {
        dropzoneFile.value.value = null;
        dropzoneMsg.value.classList.remove(...dropzoneMsg.value.classList);
        dropzoneMsg.value.classList.add("text-sm", "text-red-600", "animate-pulse");
        setTimeout(() => {
            dropzoneMsg.value.classList.remove(...dropzoneMsg.value.classList);
            dropzoneMsg.value.classList.add("text-xs", "text-gray-500", "dark:text-gray-400");

        }, "3000");
        return false;
    }
    //store.state.item[props.field] = FILES;
    return true;
}


if (store.getters.getAction == 'update') {
    onMounted(() => {
        if (fileTempl.value) {
            btnImageTemplate.value.classList.add('hidden');
            const clone = fileTempl.value.firstChild.cloneNode(true);
            const altName = store.state.item[props.field].split('/');
            clone.querySelector("h1").textContent = altName[1];
            Object.assign(clone.querySelector("img"), {
                src: logoPDF,
                alt: altName[1]
            });
            empty.value.classList.add("hidden");
            gallery.value.prepend(clone);
            disableDropzone();
        }
    });
}

const addFile = (target, file) => {
    const validate = validateDropzoneFront(file);
    if (fileTempl.value && validate) {
        const objectURL = URL.createObjectURL(file);
        const clone = fileTempl.value.firstChild.cloneNode(true);

        clone.querySelector("h1").textContent = file.name;
        clone.value = objectURL;
        clone.querySelector(".delete").dataset.target = objectURL;
        clone.querySelector(".size").textContent =
            file.size > 1024
                ? file.size > 1048576
                    ? Math.round(file.size / 1048576) + "mb"
                    : Math.round(file.size / 1024) + "kb"
                : file.size + "b";

        let srcFileIcon = objectURL;

        switch (file.type) {
            case 'application/pdf': srcFileIcon = logoPDF;
        }

        Object.assign(clone.querySelector("img"), {
            src: srcFileIcon,
            alt: file.name
        });

        empty.value.classList.add("hidden");
        target.value.prepend(clone);
        store.state.item[props.field] = file;
        FILES[objectURL] = file;
    }
    if (validate)
        disableDropzone();
}

const onFileSelected = (e) => {
    if (e.target.files !== null) {
        for (const file of e.target.files) {
            addFile(gallery, file);
        }
    }
}

const disableDropzone = () => {
    if (!dropzoneFile.value.hasAttribute('multiple')) {
        dropzoneLabel.value.classList.add('pointer-events-none');
    }
}

const enableDropzone = () => {
    dropzoneLabel.value.classList.remove('pointer-events-none');
}

const hasFiles = ({ dataTransfer: { types = [] } }) =>
    types.indexOf("Files") > -1;


let counter = 0;

const dropHandler = (e) => {
    e.preventDefault();
    for (const file of e.dataTransfer.files) {
        addFile(gallery, file);
        overlay.value.classList.remove("draggedover");
        counter = 0;
        if (!dropzoneFile.value.hasAttribute('multiple')) {
            break;
        }
    }
}
const dragOverHandler = (e) => {
    if (hasFiles(e)) {
        e.preventDefault();
    }
}

const dragLeaveHandler = () => {
    1 > --counter && overlay.value.classList.remove("draggedover");
}

const dragEnterHandler = (e) => {
    e.preventDefault();
    if (!hasFiles(e)) {
        return;
    }
    ++counter && overlay.value.classList.add("draggedover");
}

const deleteFile = (e) => {
    if (e.target.classList.contains("delete")) {
        if (store.state.action === 'create') {
            e.target.closest('li').remove();
            const blob = e.target.dataset.target;
            delete FILES[blob];
            store.state.item[props.field] = {};
            gallery.value.children.length === 1 && empty.value.classList.remove("hidden");
        }

        if (store.state.action === 'update') {
        }
        enableDropzone();
    }
}

onMounted(() => {
    document.addEventListener('change', onFileSelected);
    document.addEventListener('ondrop', dropHandler);
    document.addEventListener('ondragover', dragOverHandler);
    document.addEventListener('ondragleave', dragLeaveHandler);
    document.addEventListener('ondragenter', dragEnterHandler);
});

</script>
<template>
    <section class="h-full overflow-auto pt-2 px-8 pb-8 w-full h-full flex flex-col">

        <div id="dropzoneDiv" class="relative h-full flex flex-col bg-white shadow-xl rounded-md">
            <div id="overlay" ref="overlay"
                class="w-full h-full absolute top-0 left-0 pointer-events-none z-50 flex flex-col items-center justify-center rounded-md">
                <i>
                    <svg aria-hidden="true" class="mb-3 w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                </i>
                <p class="text-lg text-gray-700">{{ translate('Drop files to upload') }} </p>
            </div>
            <label ref="dropzoneLabel" for="dropzoneFile" @drop="dropHandler" @dragover="dragOverHandler"
                @dragleave="dragLeaveHandler" @dragenter="dragEnterHandler"
                class="flex flex-col justify-center items-center w-full h-64 bg-gray-50 rounded-lg border-2 border-gray-300 border-dashed cursor-pointer hover:bg-gray-100">
                <div class="flex flex-col justify-center items-center pt-5 pb-6">
                    <svg aria-hidden="true" class="mb-3 w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">
                            {{ translate('Click to upload') }}</span> {{ translate('or drag and drop') }}</p>
                    <p ref="dropzoneMsg" class="text-xs text-gray-500 dark:text-gray-400">
                        {{ fileTypes.join(' - ') }} (MAX. {{ fileMaxSizeMb.join(' - ') }} MB)
                    </p>
                </div>
                <input id="dropzoneFile" ref="dropzoneFile" type="file" class="hidden" />
            </label>
        </div>

        <ul id="gallery" ref="gallery" @click="deleteFile" class="pt-8 pb-2 flex flex-1 flex-wrap -m-1">
            <li ref="empty" class="h-full w-full text-center flex flex-col items-center justify-center items-center">
                <img class="mx-auto w-32"
                    src="https://user-images.githubusercontent.com/507615/54591670-ac0a0180-4a65-11e9-846c-e55ffce0fe7b.png"
                    alt="no data" />
                <span class="text-small text-gray-500">{{ translate('No file selected') }}</span>
            </li>
        </ul>
    </section>

    <template ref="fileTempl" id="file-template">
        <li class="block my-1">
            <article tabindex="0"
                class="group hasImage w-full h-full rounded-md focus:outline-none focus:shadow-outline bg-gray-100 relative text-transparent hover:text-white shadow-sm">
                <img alt="upload preview" class="img-preview w-full h-full sticky object-cover rounded-md bg-fixed" />

                <section
                    class="flex flex-col rounded-md text-xs break-words w-full h-full z-20 absolute top-0 py-2 px-3">
                    <h1 class="flex-1"></h1>
                    <div class="flex">
                        <span class="p-1">
                            <i>
                                <svg class="fill-current w-4 h-4 ml-auto pt-" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" viewBox="0 0 24 24">
                                    <path
                                        d="M5 8.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5c0 .829-.672 1.5-1.5 1.5s-1.5-.671-1.5-1.5zm9 .5l-2.519 4-2.481-1.96-4 5.96h14l-5-8zm8-4v14h-20v-14h20zm2-2h-24v18h24v-18z" />
                                </svg>
                            </i>
                        </span>

                        <p class="p-1 size text-xs"></p>
                        <button ref="btnImageTemplate"
                            class="btnDelete delete ml-auto focus:outline-none hover:bg-gray-300 p-1 rounded-md">
                            <svg class="pointer-events-none fill-current w-4 h-4 ml-auto"
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                <path class="pointer-events-none"
                                    d="M3 6l3 18h12l3-18h-18zm19-4v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711z" />
                            </svg>
                        </button>
                    </div>
                </section>
            </article>
        </li>
    </template>
</template>

<style>
.hasImage:hover section {
    background-color: rgba(5, 5, 5, 0.4);
}

.hasImage:hover button:hover {
    background: rgba(5, 5, 5, 0.45);
}

#overlay p,
i {
    opacity: 0;
}

#overlay.draggedover {
    background-color: rgba(255, 255, 255, 0.7);
}

#overlay.draggedover p,
#overlay.draggedover i {
    opacity: 1;
}

.group:hover .group-hover\:text-blue-800 {
    color: #2b6cb0;
}
</style>