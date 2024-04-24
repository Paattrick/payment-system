<script setup>
import TableComponent from "@/Components/Table.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "ant-design-vue";
import "dayjs/locale/en";
import moment from "moment";
import "moment/dist/locale/zh-cn";
import { ref, onMounted } from "vue";
const [modal] = Modal.useModal();

const props = defineProps({
    histories: Object,
});

const page = usePage();
const note = ref(null);
const showNoteModal = ref(false);
const dataTable = ref([]);

const columns = ref([
    {
        title: "Name ",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Message",
        dataIndex: "message",
        key: "message",
    },
    {
        title: "Status",
        dataIndex: "status",
        key: "status",
    },
    {
        title: "Date",
        dataIndex: "created_at",
        key: "created_at",
    },
    {
        title: "Reference No.",
        dataIndex: "reference",
        key: "reference",
    },
]);

const descriptionColumns = ref([
    {
        title: "Specific",
        dataIndex: "meta",
        key: "meta",
        align: "center",
    },
    {
        title: "Amount",
        dataIndex: "amount",
        key: "amount",
        align: "center",
    },
    {
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const loading = ref(false);

onMounted(() => {
    setTable();
});

const setTable = () => {
    props.histories.data.map((e) => {
        if (e.school_year_id == page.props.currentSchoolYear[0].id) {
            dataTable.value.push(e);
        }
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

const viewNote = (val) => {
    note.value = val.note;
    showNoteModal.value = true;
};

const handleChange = (event) => {
    router.get(
        window.location.pathname,
        {
            page: event.current,
        },
        {
            replace: true,
            preserveState: true,
            preserveScroll: true,
            onStart: () => (loading.value = true),
            onFinish: () => (loading.value = false),
        }
    );
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">History</div>

            <div>
                <TableComponent
                    :dataSource="dataTable"
                    :columns="columns"
                    :isLoading="loading"
                    @change="handleChange"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div>
                                <a-button @click="refresh()">Refresh</a-button>
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template
                            v-if="slotProps.column.dataIndex === 'message'"
                        >
                            {{ slotProps.record.name }}
                            {{ "Submitted a Payment" }}
                        </template>
                        <template
                            v-if="slotProps.column.dataIndex === 'created_at'"
                        >
                            {{
                                moment(slotProps.record.created_at).format(
                                    "DD-MM-YYYY h:mm "
                                )
                            }}
                        </template>
                        <template
                            v-if="slotProps.column.dataIndex === 'status'"
                        >
                            <div v-if="slotProps.record.status === 'accepted'">
                                <a-tag
                                    class="font-semibold capitalize"
                                    color="#86EFAC"
                                >
                                    {{ slotProps.record.status }}
                                </a-tag>
                            </div>
                            <div v-if="slotProps.record.status === 'declined'">
                                <a-tooltip placement="top">
                                    <template #title>
                                        <span>View Note</span>
                                    </template>
                                    <a-tag
                                        class="font-semibold capitalize"
                                        color="#f50"
                                    >
                                        <div
                                            @click="viewNote(slotProps.record)"
                                            class="hover:cursor-pointer"
                                        >
                                            {{ slotProps.record.status }}
                                        </div>
                                    </a-tag>
                                </a-tooltip>
                            </div>
                            <div v-if="slotProps.record.status === 'pending'">
                                <a-tag
                                    class="font-semibold capitalize"
                                    color="#d97706"
                                >
                                    {{ slotProps.record.status }}
                                </a-tag>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
            <a-modal v-model:open="showNoteModal" title="Note" :footer="null">
                <a-card>
                    <a-timeline>
                        <a-timeline-item color="#d97706">{{
                            note
                        }}</a-timeline-item>
                    </a-timeline>
                </a-card>
                <div class="flex justify-end mt-5">
                    <a-button class="mr-2" @click="showNoteModal = false"
                        >Close</a-button
                    >
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
