<script setup>
import InputError from "@/Components/InputError.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    DeleteFilled,
    EditFilled,
    ExclamationCircleFilled,
    RestFilled,
} from "@ant-design/icons-vue";
import { useForm, router } from "@inertiajs/vue3";
import { Modal, message } from "ant-design-vue";
import { h, ref } from "vue";
import TableComponent from "@/Components/Table.vue";
const [modal] = Modal.useModal();

const props = defineProps({
    histories: Object,
});
console.log(props.histories);
const form = useForm({
    name: null,
    meta: [],
});

const clearance = ref("");
const amount = ref(0);
const toPay = ref(0);
const balance = ref(0);
const note = ref(null);
const showNoteModal = ref(false);

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
        title: "Order Id",
        dataIndex: "id",
        key: "id",
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

const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);

const handleAdd = () => {
    showModal.value = true;
    isEditing.value = false;
};

const handleCancel = () => {
    form.reset();
    form.errors = {};
    showModal.value = false;
};

const addField = () => {
    form.meta.push({
        clearance: clearance.value,
        amount: amount.value,
        toPay: toPay.value,
        balance: balance.value,
    });
    clearance.value = null;
    amount.value = 0;
};

const removeField = (index) => {
    form.meta.splice(index, 1);
};

const handleEdit = (val) => {
    Object.entries(val).forEach(([key, value]) => {
        form[key] = value;
    });

    showModal.value = true;
    isEditing.value = true;
};

const submit = () => {
    form.post(route("fees.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const update = () => {
    form.put(route("fees.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const handleDelete = (val) => {
    Modal.confirm({
        title: "Are you sure to delete this Payment?",
        icon: h(ExclamationCircleFilled),
        okText: "OK",
        onOk() {
            router.delete(route("fees.destroy", val.id));
            message.success("Successfully Deleted!");
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

const viewNote = (val) => {
    note.value = val.note;
    showNoteModal.value = true;
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">History</div>

            <div>
                <TableComponent
                    :dataSource="props.histories.data"
                    :columns="columns"
                    :isLoading="loading"
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
