<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import { ref, h } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import TableComponent from "@/Components/Table.vue";
import {
    RestFilled,
    EditFilled,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import dayjs from "dayjs";
import { composables } from "@/Composables/index.js";
import { Modal } from "ant-design-vue";
import { watchDebounced } from "@vueuse/core";
import { onMounted, watch } from "vue";
const [modal] = Modal.useModal();

const props = defineProps({
    employees: Object,
});

const { sections, grades } = composables();
const form = useForm({
    last_name: null,
    name: null,
    middle_name: null,
    suffix_name: null,
    lrn: null,
    birthday: null,
    contact_number: null,
    gender: null,
    address: {
        province: null,
        municipality: null,
        barangay: null,
    }
});

const columns = ref([
    {
        title: "Last Name",
        dataIndex: "last_name",
        key: "last_name",
    },
    {
        title: "First Name",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Middle Name",
        dataIndex: "middle_name",
        key: "middle_name",
    },

    {
        title: "Suffix Name",
        dataIndex: "suffix_name",
        key: "suffix_name",
    },
    {
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const search = ref(null);

watchDebounced(
    [search],
    (data) => {
        router.get(
            window.location.pathname,
            {
                search: search.value,
            },
            {
                preserveScroll: true,
                replace: true,
                preserveState: true,
                onStart: () => {
                    loading.value = true;
                },
                onFinish: () => {
                    loading.value = false;
                },
            }
        );
    },
    {
        debounce: 300,
    }
);

watch(
    () => form.contact_number,
    (newValue, oldValue) => {
        // Remove non-numeric characters from the input
        const numericValue = newValue.replace(/\D/g, '');

        // Check if the resulting value is an integer
        if (!Number.isInteger(Number(numericValue))) {
            // If it's not an integer, it's an invalid input
            console.error('Error: Contact number must be an integer.');
            // Optionally, reset the input to its previous value
            form.contact_number = oldValue;
            return; // Exit the watcher
        }
        
        // Check if the length exceeds 11 digits
        if (numericValue.length > 11) {
            console.error('Error: Contact number cannot exceed 11 digits.');
            // Truncate the input to 11 digits
            form.contact_number = numericValue.slice(0, 11);
        } else {
            // Update the form data with the cleaned numeric value
            form.contact_number = numericValue;
        }
    },
    { deep: true }
);

watch(
    () => form.id_number,
    (newValue, oldValue) => {
        // Remove non-numeric characters from the input
        const numericValue = newValue.replace(/\D/g, '');

        // Check if the resulting value is an integer
        if (!Number.isInteger(Number(numericValue))) {
            // If it's not an integer, it's an invalid input
            console.error('Error: ID number must be an integer.');
            // Optionally, reset the input to its previous value
            form.id_number = oldValue;
            return; // Exit the watcher
        }
        
        // Update the form data with the cleaned numeric value
        form.id_number = numericValue;
    },
    { deep: true }
);


watch(
    () => form.name,
    (newValue, oldValue) => {
        // Check if the new value is empty (allows deletion)
        if (newValue === '') {
            // Update the form data with the empty value
            form.name = newValue;
            return;
        }

        // Check if the new value contains only alphabetical characters
        if (!/^[a-zA-Z\s]+$/.test(newValue)) {
            // If it contains non-alphabetical characters, it's an invalid input
            console.error('Error: First name cannot contain numeric characters.');
            // Reset the input to its previous value
            form.name = oldValue;
        }
    },
    { deep: true }
);

watch(
    () => form.last_name,
    (newValue, oldValue) => {
        // Check if the new value is empty (allows deletion)
        if (newValue === '') {
            // Update the form data with the empty value
            form.last_name = newValue;
            return;
        }

        // Check if the new value contains only alphabetical characters
        if (!/^[a-zA-Z\s]+$/.test(newValue)) {
            // If it contains non-alphabetical characters, it's an invalid input
            console.error('Error: First name cannot contain numeric characters.');
            // Reset the input to its previous value
            form.last_name = oldValue;
        }
    },
    { deep: true }
);

const handleAdd = () => {
    showModal.value = true;
    isEditing.value = false;
};

const handleCancel = () => {
    form.reset();
    form.errors = {};
    showModal.value = false;
};

const handleEdit = (val) => {
    const data = dayjs(val.birthday, "YYYY/MM/DD");
    Object.entries(val).forEach(([key, value]) => {
        form[key] = value;
    });
    form.password = null;
    form.birthday = data;

    showModal.value = true;
    isEditing.value = true;
};

const submit = () => {
    form.post(route("employees.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const update = () => {
    form.put(route("employees.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const handleDelete = (val) => {
    Modal.confirm({
        title: "Are you sure to move this to archive?",
        icon: h(ExclamationCircleFilled),
        content: "archived employees can be accessed in Archives.",
        okText: "OK",
        cancelText: "Cancel",
    });
};

const refresh = () => {
    router.reload({
        onStart: () => {
            loading.value = true;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};

const age= ref(null);

const calculateAge = () => {
    const currentDate = new Date();

    const diffTime = currentDate - new Date(form.birthday);
    const totalDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    let years = Math.floor(totalDays / 365.25);
    // let months = Math.floor((totalDays % 365.25) / 30.4375);
    // let days = Math.floor((totalDays % 365.25) % 30.4375);

   age.value = years + " ";
};

</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Collectors</div>

            <div>
                <TableComponent
                    :dataSource="props.employees.data"
                    :columns="columns"
                    :isLoading="loading"
                    :paginationData="props.employees.meta"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div class="flex space-x-4">
                                <div class="w-auto">
                                    <a-input-search
                                        v-model:value="search"
                                        placeholder="Search Collector"
                                        allowClear
                                    />
                                </div>
                                <a-button @click="refresh()">Refresh</a-button>
                            </div>
                            <div class="justify-end">
                                <a-button type="primary" @click="handleAdd">
                                    Add Collector
                                </a-button>
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div class="flex space-x-4">
                                <div @click="handleEdit(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Edit</span>
                                        </template>
                                        <a-button><EditFilled /></a-button>
                                    </a-tooltip>
                                </div>
                                <div @click="handleDelete(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Archive</span>
                                        </template>
                                        <a-button><RestFilled /></a-button>
                                    </a-tooltip>
                                </div>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
            <a-modal
                v-model:open="showModal"
                :title="isEditing ? 'Edit Employee' : 'Add Employee'"
                width="1000px"
                :footer="null"
                :afterClose="handleCancel"
            >
                <a-form :model="form" name="basic" layout="vertical">
                    <a-card title="Personal Details" class="bg-gray-200">
                        <div class="flex justify-between mx-auto space-x-4">
                            <a-form-item
                                required
                                label="First Name"
                                name="name"
                            >
                                <a-input v-model:value="form.name" />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.name"
                                />
                            </a-form-item>
                            <a-form-item
                               
                                label="Middle Name"
                                name="middle_name"
                            >
                                <a-input v-model:value="form.middle_name" />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.middle_name"
                                />
                            </a-form-item>
                            <a-form-item
                                required
                                label="Last Name"
                                name="last_name"
                            >
                                <a-input v-model:value="form.last_name" />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.last_name"
                                />
                            </a-form-item>
                            <a-form-item label="Suffix Name" name="suffix_name">
                                <a-select v-model:value="form.suffix_name">
                                <a-select-option value="Jr">Jr</a-select-option>
                                <a-select-option value="Sr">Sr</a-select-option>
                                <a-select-option value="II">II</a-select-option>
                                <a-select-option value=""></a-select-option>
                                <!-- Add more options as needed -->
                                </a-select>
                                     <InputError
                                class="mt-2"
                                :message="form.errors.suffix_name"
                                     />
                            </a-form-item>
                        </div>
                        <div class="flex justify-between mx-auto space-x-4">
                            <a-form-item
                                required
                                label="Date of Birth"
                                name="birthday"
                            >
                                <a-date-picker
                                    v-model:value="form.birthday"
                                    format="YYYY/MM/DD"
                                    @change="calculateAge"
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.birthday"
                                />
                            </a-form-item>
                            <a-form-item label="Age" name="age">
                                <a-input v-model:value="age" disabled />
                                
                            </a-form-item>
                            <a-form-item
                                required
                                label="Contact Number"
                                name="contact_number"
                            >
                                <a-input v-model:value="form.contact_number" />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.contact_number"
                                />
                            </a-form-item>
                            <a-form-item required label="Gender" name="gender">
                                <a-select
                                    ref="select"
                                    v-model:value="form.gender"
                                >
                                    <a-select-option value="Male"
                                        >Male</a-select-option
                                    >
                                    <a-select-option value="Female"
                                        >Female</a-select-option
                                    >
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.gender"
                                />
                            </a-form-item>
                        </div>
                    </a-card>

                    <a-card title="Address" class="mt-4 bg-gray-200">
                        <div class="flex justify-between mx-auto space-x-4">
                            <a-form-item required label="Province">
                                <a-select
                                    v-model:value="form.address.province"
                                    style="width: 200px"
                                >
                                    <a-select-option value="Bohol"
                                        >Bohol</a-select-option
                                    >
                                    <!-- Add other provinces as needed -->
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors['address.province']"
                                />
                            </a-form-item>
                            <a-form-item required label="Municipality">
                                <a-select
                                    v-model:value="form.address.municipality"
                                    style="width: 200px"
                                    allowClear
                                >
                                    <a-select-option value="Guindulman"
                                        >Guindulman</a-select-option
                                    >
                                    <!-- Add other municipalities in Bohol -->
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors['address.municipality']"
                                />
                            </a-form-item>
                            <a-form-item required label="Barangay">
                                <a-select
                                    v-model:value="form.address.barangay"
                                    style="width: 200px"
                                    allowClear
                                >
                                    <a-select-option value="Basdio"
                                        >Basdio</a-select-option
                                    >
                                    <a-select-option value="Bato"
                                        >Bato</a-select-option
                                    >
                                    <a-select-option value="Bayong"
                                        >Bayong</a-select-option
                                    >
                                    <a-select-option value="Biabas"
                                        >Biabas</a-select-option
                                    >
                                    <a-select-option value="Bulawan"
                                        >Bulawan</a-select-option
                                    >
                                    <a-select-option value="Cabantian"
                                        >Cabantian</a-select-option
                                    >
                                    <a-select-option value="Canhaway"
                                        >Canhaway</a-select-option
                                    >
                                    <a-select-option value="Cansiwang"
                                        >Cansiwang</a-select-option
                                    >
                                    <a-select-option value="Catungawan Norte"
                                        >Catungawan Norte</a-select-option
                                    >
                                    <a-select-option value="Catungawan Sur"
                                        >Catungawan Sur</a-select-option
                                    >
                                    <a-select-option value="Guinacot"
                                        >Guinacot</a-select-option
                                    >
                                    <a-select-option value="Guio‑ang"
                                        >Guio‑ang</a-select-option
                                    >
                                    <a-select-option value="Lombog"
                                        >Lombog</a-select-option
                                    >
                                    <a-select-option value="Mayuga"
                                        >Mayuga</a-select-option
                                    >
                                    <a-select-option value="Sawang "
                                        >Sawang
                                    </a-select-option>
                                    <a-select-option value="Tabajan "
                                        >Tabajan
                                    </a-select-option>
                                    <a-select-option value="Tabunok"
                                        >Tabunok</a-select-option
                                    >
                                    <a-select-option value="Trinidad"
                                        >Trinidad</a-select-option
                                    >

                                    <!-- Add other barangays in Guindulman -->
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors['address.barangay']"
                                />
                            </a-form-item>
                        </div>
                        <a-form-item required label="ID">
                            <a-input v-model:value="form.lrn" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.lrn"
                            />
                        </a-form-item>
                    </a-card>
                    <div class="flex justify-end mt-5">
                        <a-button class="mr-2" @click.prevent="handleCancel"
                            >Cancel</a-button
                        >
                        <a-button
                            type="primary"
                            :loading="form.processing"
                            @click.prevent="isEditing ? update() : submit()"
                            >{{ isEditing ? "Update" : "Submit" }}</a-button
                        >
                    </div>
                </a-form>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
