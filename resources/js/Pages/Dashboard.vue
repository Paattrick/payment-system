<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import TableComponent from "@/Components/Table.vue";
import { ref, computed } from "vue";
import { onMounted } from "vue";
import { composables } from "@/Composables/index.js";

const props = defineProps({
    students: Object,
    activeStudents: Number,
    archivedStudents: Number,
    history: Object,
});
const { sections, grades, strands } = composables();
const page = usePage();
const reportType = ref(null);
const grade = ref(null);
const section = ref(null);
const status = ref(null);

// onMounted(() => {
//     setInterval(refreshNotification, 5000);
// });

const refreshNotification = () => {
    router.reload({ only: ["history"] });
};
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
]);

const handleRedirectToArchived = () => {
    router.visit(route("archives.index"));
};

const handleRedirectToStudents = () => {
    router.visit(route("students.index"));
};

const handleCheckNotification = () => {
    router.visit(route("transaction.index"));
};

const exportLink = computed(() => {
    return route("dashboard.export", {
        type: reportType.value,
        grade: grade.value,
        section: section.value,
        status: status.value,
    });
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div v-if="!page.props.auth.role.is_student">
            <marquee
                ><div v-if="props.history !== null">
                    <a @click="handleCheckNotification" class="underline"
                        >{{ props.history?.name }} sent a payment request</a
                    >
                </div></marquee
            >
        </div>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div v-if="!page.props.auth.role.is_student" class="pb-5">
                    <a-row :gutter="16">
                        <a-col :span="12">
                            <a-card>
                                <a-statistic
                                    @click="handleRedirectToStudents"
                                    title="Active Students"
                                    :value="props.activeStudents"
                                    :value-style="{ color: '#3f8600' }"
                                    style="margin-right: 50px"
                                    class="demo-class hover:cursor-pointer"
                                >
                                    <template #prefix>
                                        <!-- <arrow-up-outlined /> -->
                                    </template>
                                </a-statistic>
                            </a-card>
                        </a-col>
                        <a-col :span="12">
                            <a-card>
                                <a-statistic
                                    @click="handleRedirectToArchived"
                                    title="Archived Students"
                                    :value="props.archivedStudents"
                                    class="demo-class hover:cursor-pointer"
                                    :value-style="{ color: '#cf1322' }"
                                >
                                    <template #prefix>
                                        <!-- <arrow-down-outlined /> -->
                                    </template>
                                </a-statistic>
                            </a-card>
                        </a-col>
                    </a-row>
                </div>

                <div
                    v-if="!page.props.auth.role.is_student"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-5"
                >
                    <a-card title="Generate Report" class="w-full">
                        <a-select class="w-full" v-model:value="reportType">
                            <a-select-option
                                value="generateTotalEnrolledStudents"
                                >Generate Students Report</a-select-option
                            >
                            <!-- <a-select-option value="generateTotalMoneyCollected"
                                >Total Money Collected</a-select-option
                            > -->
                        </a-select>
                        <div
                            class="mt-5 flex space-x-4"
                            v-if="reportType == 'generateTotalEnrolledStudents'"
                        >
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
                                                .indexOf(input.toLowerCase()) >=
                                            0
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
                                                .indexOf(input.toLowerCase()) >=
                                            0
                                    "
                                >
                                </a-select>
                            </div>
                            <div>
                                <a-select
                                    v-model:value="status"
                                    placeholder="Select Status"
                                >
                                    <a-select-option value="archived"
                                        >Archived</a-select-option
                                    >
                                    <a-select-option value="active"
                                        >Active</a-select-option
                                    >
                                </a-select>
                            </div>
                        </div>
                        <div class="flex justify-end mt-5">
                            <a target="_blank" :href="exportLink">
                                <a-button type="primary"> Submit </a-button>
                            </a>
                        </div>
                    </a-card>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="page-title height-md:mb-30">
                            Recently added students
                        </div>
                        <TableComponent
                            :dataSource="props.students.data"
                            :columns="columns"
                            :paginationData="props.students.meta"
                        >
                            <template #customColumn="slotProps"> </template>
                        </TableComponent>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
