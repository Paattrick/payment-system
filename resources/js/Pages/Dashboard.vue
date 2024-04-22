<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import TableComponent from "@/Components/Table.vue";
import { ref, computed } from "vue";
import { onMounted } from "vue";
import { composables } from "@/Composables/index.js";
import dayjs from "dayjs";
import { MoreOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    students: Object,
    activeStudents: Number,
    archivedStudents: Number,
    history: Object,
    totalCollectibles: Number,
    collectedCollectibles: Number,
});
const { sections, grades, strands } = composables();
const page = usePage();
const reportType = ref(null);
const grade = ref(null);
const section = ref(null);
const status = ref(null);
const dateSelected = ref(null);
const collectiblesDate = ref(null);
const collectiblesType = ref(null);
const dateRange = ref(null);

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
        school_year: dateSelected.value,
        collectiblesType: collectiblesType.value,
        collectiblesDate:
            collectiblesType.value == "daily"
                ? dayjs(collectiblesDate.value).format("YYYY/MM/DD")
                : [firstDate.value, secondDate.value],
    });

    showReportModal.value = false;
});

const focus = () => {
    console.log("focus");
};
const firstDate = ref(null);
const secondDate = ref(null);
const handleChangeDate = () => {
    if (dateRange.value != null) {
        firstDate.value = dayjs(dateRange.value[0]).format("YYYY/MM/DD");
        secondDate.value = dayjs(dateRange.value[1]).format("YYYY/MM/DD");
    }
};

const showReportModal = ref(false);

const handleModalReport = (type) => {
    reportType.value = type;
    showReportModal.value = true;
};
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
        <div class="">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div v-if="!page.props.auth.role.is_student" class="pb-5">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-green-200 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="ml-[100px] mt-5">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth="{1.5}"
                                        stroke="currentColor"
                                        class="w-[100px] h-[70px]"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                                        />
                                    </svg>
                                </div>
                                <div class="">
                                    <div class="flex justify-end">
                                        <MoreOutlined
                                            class="mt-2 mr-2 text-lg font-bold hover:cursor-pointer"
                                            @click="
                                                handleModalReport(
                                                    'generateTotalCollectibles'
                                                )
                                            "
                                        />
                                    </div>
                                    <div class="font-bold">
                                        {{
                                            new Intl.NumberFormat("PHP", {
                                                style: "currency",
                                                currency: "PHP",
                                            }).format(
                                                props.totalCollectibles *
                                                    Number(props.activeStudents)
                                            )
                                        }}
                                    </div>
                                    <div class="font-bold mt-5 mb-5">
                                        Total Collectibles of
                                        {{
                                            page.props?.activeSchoolYear[0]
                                                ?.name
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-yellow-200 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="ml-[100px] mt-5">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-[100px] h-[70px]"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z"
                                        />
                                    </svg>
                                </div>
                                <div class="">
                                    <div class="flex justify-end">
                                        <MoreOutlined
                                            class="mt-2 mr-2 text-lg font-bold hover:cursor-pointer"
                                            @click="
                                                handleModalReport(
                                                    'generateTotalCollectibles'
                                                )
                                            "
                                        />
                                    </div>
                                    <div class="font-bold">
                                        {{
                                            new Intl.NumberFormat("PHP", {
                                                style: "currency",
                                                currency: "PHP",
                                            }).format(
                                                props.collectedCollectibles
                                            )
                                        }}
                                    </div>
                                    <div class="font-bold mt-5">
                                        Amounts Received of
                                        {{
                                            page.props?.activeSchoolYear[0]
                                                ?.name
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-orange-200 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="ml-[100px] mt-5">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth="{1.5}"
                                        stroke="currentColor"
                                        class="w-[100px] h-[70px]"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z"
                                        />
                                    </svg>
                                </div>
                                <div class="">
                                    <div class="flex justify-end">
                                        <MoreOutlined
                                            class="mt-2 mr-2 text-lg font-bold hover:cursor-pointer"
                                            @click="
                                                handleModalReport(
                                                    'generateTotalCollectibles'
                                                )
                                            "
                                        />
                                    </div>
                                    <div class="font-bold">
                                        {{
                                            new Intl.NumberFormat("PHP", {
                                                style: "currency",
                                                currency: "PHP",
                                            }).format(
                                                props.totalCollectibles -
                                                    props.collectedCollectibles
                                            )
                                        }}
                                    </div>
                                    <div class="font-bold mt-5 mb-5">
                                        Remaining Collectibles of
                                        {{
                                            page.props?.activeSchoolYear[0]
                                                ?.name
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-200 rounded-lg">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="ml-[100px]">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="w-[100px] h-[70px] mt-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"
                                        />
                                    </svg>
                                </div>
                                <div class="">
                                    <div class="flex justify-end">
                                        <MoreOutlined
                                            class="mt-2 mr-2 text-lg font-bold hover:cursor-pointer"
                                            @click="
                                                handleModalReport(
                                                    'generateTotalEnrolledStudents'
                                                )
                                            "
                                        />
                                    </div>
                                    <div class="font-bold">
                                        {{ props.activeStudents }}
                                    </div>
                                    <div class="font-bold mt-5">
                                        Total Enrolled Students
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a-modal
                        v-model:open="showReportModal"
                        :footer="null"
                        :title="
                            reportType == 'generateTotalEnrolledStudents'
                                ? 'Create Specific Report For Students'
                                : 'Create Specific Report For Collectibles'
                        "
                    >
                        <div
                            class="mt-5"
                            v-if="reportType == 'generateTotalEnrolledStudents'"
                        >
                            <a-form layout="vertical">
                                <a-form-item label="Select Grade">
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
                                </a-form-item>
                                <a-form-item label="Select Section">
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
                                </a-form-item>
                                <a-form-item label="Select Status">
                                    <a-select
                                        v-model:value="status"
                                        placeholder="Select Status"
                                        class="w-full"
                                    >
                                        <a-select-option value="archived"
                                            >Archived</a-select-option
                                        >
                                        <a-select-option value="active"
                                            >Active</a-select-option
                                        >
                                    </a-select>
                                </a-form-item>
                            </a-form>
                        </div>
                        <div
                            class="mt-5"
                            v-if="reportType == 'generateTotalCollectibles'"
                        >
                            <a-form layout="vertical">
                                <a-form-item label="Select School Year">
                                    <a-select
                                        ref="select"
                                        v-model:value="dateSelected"
                                        @focus="focus"
                                        @change="handleChangeDate"
                                        class="w-full"
                                    >
                                        <a-select-option
                                            v-for="(date, index) in page.props
                                                .schoolYears"
                                            :index="index"
                                            :value="date.id"
                                        >
                                            {{ date.name }}
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                                <a-form-item label="Select Report Type">
                                    <a-select
                                        ref="select"
                                        v-model:value="collectiblesType"
                                        class="w-full"
                                        @focus="focus"
                                        allowClear
                                    >
                                        <a-select-option
                                            index="0"
                                            value="daily"
                                        >
                                            Daily
                                        </a-select-option>
                                        <a-select-option
                                            index="1"
                                            value="monthly"
                                        >
                                            Monthly
                                        </a-select-option>
                                    </a-select>
                                </a-form-item>
                                <a-form-item
                                    v-if="collectiblesType != null"
                                    label="Select Date"
                                >
                                    <a-date-picker
                                        v-if="collectiblesType == 'daily'"
                                        v-model:value="collectiblesDate"
                                        format="YYYY/MM/DD"
                                        class="w-full"
                                    />
                                    <a-range-picker
                                        v-if="collectiblesType == 'monthly'"
                                        v-model:value="dateRange"
                                        @change="handleChangeDate"
                                        class="w-full"
                                    />
                                </a-form-item>
                            </a-form>
                        </div>
                        <div class="flex justify-end mt-5">
                            <a target="_blank" :href="exportLink">
                                <a-button type="primary"> Submit </a-button>
                            </a>
                        </div>
                    </a-modal>
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
