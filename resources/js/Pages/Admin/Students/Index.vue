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
    DownOutlined,
} from "@ant-design/icons-vue";
import dayjs from "dayjs";
import { composables } from "@/Composables/index.js";
import { Modal, notification, message } from "ant-design-vue";
import { watchDebounced } from "@vueuse/core";
import { onMounted, watch, computed } from "vue";
import moment from "moment";
import "moment/dist/locale/zh-cn";
const [modal] = Modal.useModal();
import {
    regions,
    provinces,
    cities,
    barangays,
    provinceByName,
} from "select-philippines-address";

const props = defineProps({
    students: Object,
    fees: Object,
    grades: Object,
});

const { sections, grades, strands, phillipineProvinces } = composables();

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
    address: {
        province: null,
        municipality: null,
        barangay: null,
    },
    school_year_id: page.props?.currentSchoolYear[0]?.id,
    current_school_year: page.props?.activeSchoolYear[0]?.id,
    student_fees: null,
});

const search = ref(null);
const loading = ref(false);
const grade = ref(null);
const section = ref(null);
const testSections = ref([]);
const dataTable = ref([]);
const fees = ref([]);

onMounted(() => {
    setTable();
    setGrades();
});

const setTable = () => {
    dataTable.value = [];
    props.students.data.map((e) => {
        if (
            e.enrolled_school_years.includes(
                page.props.currentSchoolYear[0].id.toString()
            )
        ) {
            dataTable.value.push(e);
        }
    });
    setGrades();
};

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

watch(grade, async (newValue, oldValue) => {
    if (newValue != oldValue) {
        testSections.value = [];
    }

    props.grades.map((e) => {
        if (newValue == e.id) {
            e.sections.forEach((section) => {
                testSections.value.push({
                    label: section,
                    value: section,
                });
            });
        }
    });

    if (newValue == "" || newValue == null) {
        testSections.value = [];
    }
});

watch(
    () => form.grade,
    async (newValue, oldValue) => {
        if (newValue != oldValue) {
            testSections.value = [];
        }

        props.grades.map((e) => {
            if (newValue == e.id) {
                e.sections.forEach((section) => {
                    testSections.value.push({
                        label: section,
                        value: section,
                    });
                });
            }
        });

        if (newValue == "" || newValue == null) {
            testSections.value = [];
        }
    },
    { deep: true }
);

watch(
    () => form.contact_number,
    (newValue, oldValue) => {
        if (newValue) {
            const numericValue = newValue.replace(/\D/g, "");

            if (!Number.isInteger(Number(numericValue))) {
                console.error("Error: Contact number must be an integer.");

                form.contact_number = oldValue;
                return;
            }

            if (numericValue.length > 11) {
                console.error("Error: Contact number cannot exceed 11 digits.");

                form.contact_number = numericValue.slice(0, 11);
            } else {
                form.contact_number = numericValue;
            }
        }
    },
    { deep: true }
);

watch(
    () => form.lrn,
    (newValue, oldValue) => {
        if (newValue) {
            const numericValue = newValue.replace(/\D/g, "");

            if (!Number.isInteger(Number(numericValue))) {
                console.error("Error: Lrn must be an integer.");

                form.lrn = oldValue;
                return;
            }

            if (numericValue.length > 11) {
                console.error("Error: Lrn cannot exceed 11 digits.");

                form.lrn = numericValue.slice(0, 11);
            } else {
                form.lrn = numericValue;
            }
        }
    },
    { deep: true }
);

watch(
    () => form.name,
    (newValue, oldValue) => {
        // Check if the new value is empty (allows deletion)
        if (newValue === "") {
            // Update the form data with the empty value
            form.name = newValue;
            return;
        }

        // Check if the new value contains only alphabetical characters
        if (!/^[a-zA-Z\s]+$/.test(newValue)) {
            // If it contains non-alphabetical characters, it's an invalid input
            console.error(
                "Error: First name cannot contain numeric characters."
            );
            // Reset the input to its previous value
            form.name = oldValue;
        }
    },
    { deep: true }
);

watch(
    () => form.middle_name,
    (newValue, oldValue) => {
        // Check if the new value is empty (allows deletion)
        if (newValue === "") {
            // Update the form data with the empty value
            form.middle_name = newValue;
            return;
        }

        // Check if the new value contains only alphabetical characters
        if (!/^[a-zA-Z\s]+$/.test(newValue)) {
            // If it contains non-alphabetical characters, it's an invalid input
            console.error(
                "Error: First name cannot contain numeric characters."
            );
            // Reset the input to its previous value
            form.middle_name = oldValue;
        }
    },
    { deep: true }
);

watch(
    () => form.last_name,
    (newValue, oldValue) => {
        // Check if the new value is empty (allows deletion)
        if (newValue === "") {
            // Update the form data with the empty value
            form.last_name = newValue;
            return;
        }

        // Check if the new value contains only alphabetical characters
        if (!/^[a-zA-Z\s]+$/.test(newValue)) {
            // If it contains non-alphabetical characters, it's an invalid input
            console.error(
                "Error: First name cannot contain numeric characters."
            );
            // Reset the input to its previous value
            form.last_name = oldValue;
        }
    },
    { deep: true }
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
    testSections.value = [];
};

const handleCancel = () => {
    form.reset();
    form.errors = {};
    showModal.value = false;
};

const handleEdit = (val) => {
    let data = null;
    if (val.birthday !== null) {
        data = dayjs(val.birthday, "YYYY/MM/DD");
    }
    Object.entries(val).forEach(([key, value]) => {
        form[key] = value;
    });
    form.birthday = data;
    form.password = null;
    props.grades.map((e) => {
        if (e.id == val.grade_id) {
            form.grade = e.grade;
        }
    });

    if (val.address == "" || val.address == null) {
        form.address = {
            province: null,
            municipality: null,
            barangay: null,
        };
    } else {
        handleChangeProvince();
        handleChangeMunicipality();
    }
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
            setTable();
        },
    });
};

const update = () => {
    form.put(route("students.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
            setTable();
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
            router.put(route("student.archive", val.id), {
                onSuccess: () => {
                    dataTable.value = null;
                    setTable();
                    notification.success({
                        placement: "topRight",
                        duration: 3,
                        rtl: true,
                        message: "Successfully Sent to Archives",
                        description:
                            "Archived students can be accessed in Archives.",
                    });
                },
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
        onSuccess: () => {
            setTable();
        },
    });
};

const studentFees = ref(null);
const showStudentFeesModal = ref(false);

const showStudentFees = (val) => {
    if (val.student_fees !== null) {
        val?.student_fees.map((e) => {
            if (e.school_year_id == page.props.currentSchoolYear[0].id) {
                studentFees.value = [...val.student_fees];
            } else {
                studentFees.value = null;
            }
        });
    }
    showStudentFeesModal.value = true;

    remainingBalance();
};

const runningBalance = ref(null);

const remainingBalance = () => {
    let temp = 0;
    if (studentFees.value !== null) {
        studentFees.value.map((e) => {
            e.meta.map((meta) => {
                if (meta.balance != "PAID") {
                    let toAdd = meta.balance == 0 ? meta.amount : meta.balance;
                    temp = Number(temp) + Number(toAdd);
                }
            });
        });
    }

    runningBalance.value = temp;
};

const age = ref(null);

const calculateAge = () => {
    if (form.birthday !== null) {
        const currentDate = new Date();

        const diffTime = currentDate - new Date(form.birthday);
        const totalDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        let years = Math.floor(totalDays / 365.25);
        // let months = Math.floor((totalDays % 365.25) / 30.4375);
        // let days = Math.floor((totalDays % 365.25) % 30.4375);

        age.value = years + " ";
    }
};

const selectedStudents = ref([]);

const onSelectChange = (value) => {
    selectedStudents.value = value;
};

const enrollStudents = () => {
    router.put(
        route("students.enroll", {
            students: selectedStudents.value,
            current_school_year: page.props?.activeSchoolYear[0]?.id,
        }),
        {
            onSuccess: () => {
                notification.success("Successfully Enrolled");
            },
        }
    );
};

const csvForm = useForm({
    file: null,
    school_year_id: page.props?.currentSchoolYear[0]?.id,
    current_school_year: page.props?.activeSchoolYear[0]?.id,
});

const showImportCsvModal = ref(false);

const handleImportCsv = () => {
    showImportCsvModal.value = true;
};

const importCsv = () => {
    csvForm.post(route("import.csv"), {
        onSuccess: () => {
            showImportCsvModal.value = false;
            csvForm.reset();
            setTable();
        },
    });
};

const municipalities = ref([]);
const municipalityBarangays = ref([]);
const selectedMunicipalityByCode = ref(null);

const handleChangeProvince = () => {
    provinceByName(form.address.province).then(async (province) => {
        await cities(province.province_code).then((city) => {
            municipalities.value = [...city];
        });
    });
};

const handleChangeMunicipality = () => {
    barangays(form.address.municipality).then((barangays) => {
        municipalityBarangays.value = [...barangays];
    });
};

const tempGrades = ref([]);

const setGrades = () => {
    dataTable.value.map((e) => {
        props.grades.map((grade) => {
            if (e.grade_id == grade.id) {
                e.grade_id = grade.grade;
            }
        });
    });
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Students</div>
            <div>
                <TableComponent
                    :dataSource="dataTable"
                    :columns="columns"
                    :isLoading="loading"
                    :paginationData="props.students.meta"
                    :hasRowSelection="true"
                    @onSelectChange="onSelectChange($event)"
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
                                        :options="
                                            props.grades.map((item) => ({
                                                value: item.id,
                                                label: item.grade,
                                            }))
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
                                        :options="testSections"
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
                            <div class="flex space-x-4 justify-end">
                                <div v-if="selectedStudents.length > 0">
                                    <a-dropdown :trigger="['click']">
                                        <a-button
                                            class="ant-dropdown-link"
                                            @click.prevent
                                        >
                                            Actions
                                            <DownOutlined />
                                        </a-button>
                                        <template #overlay>
                                            <a-menu>
                                                <a-menu-item key="0">
                                                    <a @click="enrollStudents()"
                                                        >Enroll Student's</a
                                                    >
                                                </a-menu-item>
                                            </a-menu>
                                        </template>
                                    </a-dropdown>
                                </div>
                                <a-button
                                    type="default"
                                    @click="handleImportCsv"
                                >
                                    Import CSV
                                </a-button>
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
                        <template v-if="slotProps.column.dataIndex === 'grade'">
                            {{ slotProps.record.grade_id }}
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
                    <a-card title="Personal Details" class="bg-gray-200 mb-5">
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
                            <a-form-item label="Middle Name" name="middle_name">
                                <a-input v-model:value="form.middle_name" />
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
                                    <a-select-option value="Jr"
                                        >Jr</a-select-option
                                    >
                                    <a-select-option value="Sr"
                                        >Sr</a-select-option
                                    >
                                    <a-select-option value="II"
                                        >II</a-select-option
                                    >
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
                                    class="w-full"
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

                    <a-card title="School Details" class="bg-gray-200">
                        <a-form-item required label="LRN" name="lrn">
                            <a-input v-model:value="form.lrn" />
                            <InputError
                                class="mt-2"
                                :message="form.errors.lrn"
                            />
                        </a-form-item>
                        <div class="flex mx-auto space-x-4">
                            <a-form-item
                                required
                                label="Grade"
                                name="grade"
                                class="w-full"
                            >
                                <a-select
                                    v-model:value="form.grade"
                                    :options="
                                        props.grades.map((item) => ({
                                            value: item.id,
                                            label: item.grade,
                                        }))
                                    "
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
                                    :options="testSections"
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
                                    v-model:value="form.address.province"
                                    style="width: 200px"
                                    :options="phillipineProvinces()"
                                    @change="handleChangeProvince"
                                >
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
                                    :options="
                                        municipalities.map((item) => ({
                                            value: item.city_code,
                                            label: item.city_name,
                                        }))
                                    "
                                    @change="handleChangeMunicipality"
                                >
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="
                                        form.errors['address.municipality']
                                    "
                                />
                            </a-form-item>
                            <a-form-item required label="Barangay">
                                <a-select
                                    v-model:value="form.address.barangay"
                                    style="width: 200px"
                                    allowClear
                                    :options="
                                        municipalityBarangays.map((item) => ({
                                            value: item.brgy_name,
                                            label: item.brgy_name,
                                        }))
                                    "
                                >
                                </a-select>
                                <InputError
                                    class="mt-2"
                                    :message="form.errors['address.barangay']"
                                />
                            </a-form-item>
                        </div>
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
            <a-modal
                v-model:open="showImportCsvModal"
                :footer="null"
                title="Import CSV"
            >
                <a-form layout="vertical" enctype="multipart/form-data">
                    <div class="flex pl-0">
                        <div>
                            <a-form-item label="Upload CSV">
                                <a-input
                                    type="file"
                                    @input="
                                        csvForm.file = $event.target.files[0]
                                    "
                                />
                                <!-- <InputError
                                    class="mt-2"
                                    :message="page.props?.errors?.file"
                                /> -->
                            </a-form-item>
                        </div>
                    </div>
                    <div class="flex justify-end pt-5">
                        <a-button
                            @click="importCsv()"
                            type="primary"
                            :loading="csvForm.processing"
                        >
                            Import
                        </a-button>
                    </div>
                </a-form>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
