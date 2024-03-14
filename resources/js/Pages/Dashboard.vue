<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import TableComponent from "@/Components/Table.vue";
import { ref } from "vue";

const props = defineProps({
    students: Object,
    activeStudents: Number,
    archivedStudents: Number,
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
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="pb-5">
                    <a-row :gutter="16">
                        <a-col :span="12">
                            <a-card>
                                <a-statistic
                                    title="Active Students"
                                    :value="props.activeStudents"
                                    :value-style="{ color: '#3f8600' }"
                                    style="margin-right: 50px"
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
                                    title="Archived Students"
                                    :value="props.archivedStudents"
                                    class="demo-class"
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div>Recently Added</div>
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
