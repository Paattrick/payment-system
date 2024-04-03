<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import TableComponent from "@/Components/Table.vue";
import { ref, h } from "vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import {
    RestFilled,
    EditFilled,
    ExclamationCircleFilled,
} from "@ant-design/icons-vue";
import dayjs from "dayjs";
import { composables } from "@/Composables/index.js";
import { Modal, notification } from "ant-design-vue";
import { watchDebounced } from "@vueuse/core";
import { onMounted } from "vue";
import moment from "moment";
import "moment/dist/locale/zh-cn";
const [modal] = Modal.useModal();

const props = defineProps({
    students: Object,
    fees: Object,
});

const { sections, grades, strands } = composables();

const page = usePage();
const form = useForm({
    last_name: null,
    name: null,
    middle_name: null,
    suffix_name: null,
    lrn: null,
    birthday: null,
    age: null,
    contact_number: null,
    gender: null,
    grade: null,
    section: null,
    province: null,
    municipality: null,
    barangay: null,
    id_number: null,
    password: null,
    confirmation: null,
    meta: { ...props.fees.data },
});

const search = ref(null);
const loading = ref(false);
const grade = ref(null);
const section = ref(null);

watchDebounced(
    [search, grade, section],
    (data) => {
        router.get(
            window.location.pathname,
            {
                search: search.value,
                grade: grade.value,
                section: section.value,
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
        title: "Grade",
        dataIndex: "grade",
        key: "grade",
    },
    {
        title: "Section",
        dataIndex: "section",
        key: "section",
    },
    {
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const studentFeesColumns = ref([
    {
        title: "Name of Collection",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Specific",
        dataIndex: "meta",
        key: "meta",
    },
    {
        title: "Amount",
        dataIndex: "amount",
        key: "amount",
    },
    {
        title: "Balance",
        dataIndex: "balance",
        key: "balance",
    },
]);
const showModal = ref(false);
const isEditing = ref(false);

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

    calculateAge();
};

const submit = () => {
    form.post(route("students.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const update = () => {
    form.put(route("students.update", form.id), {
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
        content: "archived students can be accessed in Archives.",
        okText: "OK",
        onOk() {
            router.put(route("student.archive", val.id));
            notification.success({
                placement: "topRight",
                duration: 3,
                rtl: true,
                message: "Successfully Sent to Archives",
                description: "Archived students can be accessed in Archives.",
            });
        },
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

const studentFees = ref(null);
const showStudentFeesModal = ref(false);

const handleChangeMunicipality = (val) => {
    form.municipality = val;
};

const showStudentFees = (val) => {
    val.meta.map((e) => {
        if (e.school_year == page.props.currentSchoolYear[0].name) {
            studentFees.value = [...val.meta];
        } else {
            studentFees.value = null;
        }
    });
    showStudentFeesModal.value = true;

    remainingBalance();
};

const runningBalance = ref(null);

const remainingBalance = () => {
    let temp = 0;
    studentFees.value.map((e) => {
        e.meta.map((meta) => {
            if (meta.balance != "PAID") {
                let toAdd = meta.balance == 0 ? meta.amount : meta.balance;
                temp = Number(temp) + Number(toAdd);
            }
        });
    });

    runningBalance.value = temp;
};

const calculateAge = () => {
    const currentDate = new Date();

    const diffTime = currentDate - new Date(form.birthday);
    const totalDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    let years = Math.floor(totalDays / 365.25);
    // let months = Math.floor((totalDays % 365.25) / 30.4375);
    // let days = Math.floor((totalDays % 365.25) % 30.4375);

    form.age = years + " ";
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Students</div>
            <div>
                <TableComponent
                    :dataSource="props.students.data"
                    :columns="columns"
                    :isLoading="loading"
                    :paginationData="props.students.meta"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div class="flex space-x-4">
                                <div class="w-auto">
                                    <a-input-search
                                        v-model:value="search"
                                        placeholder="Search Student"
                                        allowClear
                                    />
                                </div>
                                <div>
                                    <a-select
                                        class="w-[200px]"
                                        placeholder="Select Grade"
                                        v-model:value="grade"
                                        allowClear
                                        :options="grades()"
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .indexOf(
                                                        input.toLowerCase()
                                                    ) >= 0
                                        "
                                    >
                                    </a-select>
                                </div>
                                <div>
                                    <a-select
                                        class="w-[200px]"
                                        :placeholder="
                                            Number(grade) > 10
                                                ? 'Strand'
                                                : 'Section'
                                        "
                                        v-model:value="section"
                                        allowClear
                                        :options="
                                            Number(grade) > 10
                                                ? strands()
                                                : sections()
                                        "
                                        :filter-option="
                                            (input, option) =>
                                                option.label
                                                    .toLowerCase()
                                                    .indexOf(
                                                        input.toLowerCase()
                                                    ) >= 0
                                        "
                                    >
                                    </a-select>
                                </div>
                                <a-button @click="refresh()">Refresh</a-button>
                            </div>
                            <div class="justify-end">
                                <a-button type="primary" @click="handleAdd">
                                    Add Student
                                </a-button>
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template
                            v-if="slotProps.column.dataIndex === 'last_name'"
                        >
                            <div
                                @click="showStudentFees(slotProps.record)"
                                class="text-cyan-600 hover:cursor-pointer hover:underline"
                            >
                                {{ slotProps.record.last_name }}
                            </div>
                        </template>
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
                :title="isEditing ? 'Edit Student' : 'Add Student'"
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
                                required
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
                                <a-input v-model:value="form.suffix_name" />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.suffix_name"
                                />
                            </a-form-item>
                        </div>
                        <div class="flex justify-between mx-auto space-x-4">
                            <a-form-item required label="LRN" name="lrn">
                                <a-input v-model:value="form.lrn" />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.lrn"
                                />
                            </a-form-item>
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
                                <a-input v-model:value="form.age" disabled />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.age"
                                />
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
                        <div class="flex mx-auto space-x-4">
                            <a-form-item
                                required
                                label="Grade"
                                name="grade"
                                class="w-full"
                            >
                                <a-select
                                    v-model:value="form.grade"
                                    :options="grades()"
                                    :filter-option="
                                        (input, option) =>
                                            option.label
                                                .toLowerCase()
                                                .indexOf(input.toLowerCase()) >=
                                            0
                                    "
                                >
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.grade"
                                />
                            </a-form-item>
                            <a-form-item
                                required
                                :label="form.grade > 10 ? 'Strand' : 'Section'"
                                class="w-full"
                            >
                                <a-select
                                    v-model:value="form.section"
                                    :options="
                                        form.grade > 10
                                            ? strands()
                                            : sections(form.grade)
                                    "
                                    :filter-option="
                                        (input, option) =>
                                            option.label
                                                .toLowerCase()
                                                .indexOf(input.toLowerCase()) >=
                                            0
                                    "
                                />
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.section"
                                />
                            </a-form-item>
                        </div>
                    </a-card>

                    <a-card title="Address" class="mt-4 bg-gray-200">
                        <div class="flex justify-between mx-auto space-x-4">
                            <a-form-item required label="Province">
                                <a-select
                                    v-model:value="form.province"
                                    style="width: 200px"
                                >
                                    <a-select-option value="Bohol"
                                        >Bohol</a-select-option
                                    >
                                    <!-- Add other provinces as needed -->
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.province"
                                />
                            </a-form-item>
                            <a-form-item required label="Municipality">
                                <a-select
                                    v-model:value="form.municipality"
                                    style="width: 200px"
                                    @change="handleChangeMunicipality"
                                    allowClear
                                >
                                    <a-select-option value="Guindulman"
                                        >Guindulman</a-select-option
                                    >
                                    <!-- Add other municipalities in Bohol -->
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors.municipality"
                                />
                            </a-form-item>
                            <a-form-item required label="Barangay">
                                <a-select
                                    v-model:value="form.barangay"
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
                                    :message="form.errors.barangay"
                                />
                            </a-form-item>
                        </div>
                        <a-form-item required label="ID">
                            <a-input v-model:value="form.id_number" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.id_number"
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
            <a-modal
                v-model:open="showStudentFeesModal"
                title="Student Billings"
                width="880px"
                :footer="null"
            >
                <TableComponent
                    :dataSource="studentFees"
                    :columns="studentFeesColumns"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div class="font-bold text-lg">
                                Running Balance:
                                {{
                                    new Intl.NumberFormat("PHP", {
                                        style: "currency",
                                        currency: "PHP",
                                    }).format(runningBalance)
                                }}
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template v-if="slotProps.column.dataIndex === 'meta'">
                            <div
                                v-for="(val, i) in slotProps.record.meta"
                                :key="i"
                            >
                                <div class="mb-2">
                                    <ul class="list-disc">
                                        <li>{{ val.clearance }}</li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template
                            v-if="slotProps.column.dataIndex === 'amount'"
                        >
                            <div
                                v-for="(val, i) in slotProps.record.meta"
                                :key="i"
                            >
                                <div class="mb-2">
                                    <ul class="list-disc">
                                        <li>
                                            {{
                                                new Intl.NumberFormat("PHP", {
                                                    style: "currency",
                                                    currency: "PHP",
                                                }).format(val.amount)
                                            }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </template>
                        <template
                            v-if="slotProps.column.dataIndex === 'balance'"
                        >
                            <div
                                v-for="(val, i) in slotProps.record.meta"
                                :key="i"
                            >
                                <div v-if="val.balance !== 'PAID'">
                                    <ul class="list-disc">
                                        <li>
                                            {{
                                                new Intl.NumberFormat("PHP", {
                                                    style: "currency",
                                                    currency: "PHP",
                                                }).format(
                                                    val.balance == 0
                                                        ? val.amount
                                                        : val.balance
                                                )
                                            }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <ul class="list-disc">
                                        <a-tag
                                            class="font-semibold capitalize"
                                            color="#86EFAC"
                                        >
                                            {{ val.balance }}
                                        </a-tag>
                                    </ul>
                                </div>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
