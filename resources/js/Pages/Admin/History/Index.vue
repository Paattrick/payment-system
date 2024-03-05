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

const form = useForm({
    name: null,
    meta: [],
});

const clearance = ref("");
const amount = ref(0);
const toPay = ref(0);
const balance = ref(0);

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
                                <a-tag
                                    class="font-semibold capitalize"
                                    color="#f50"
                                >
                                    {{ slotProps.record.status }}
                                </a-tag>
                            </div>
                        </template>
                        <!-- <template
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div class="flex space-x-4">
                                <div @click="handleEdit(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>View Payment</span>
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
                        </template> -->
                    </template>
                </TableComponent>
            </div>
            <a-modal
                v-model:open="showModal"
                :title="isEditing ? 'Edit Fee' : 'Add Fee'"
                :footer="null"
                :afterClose="handleCancel"
                :width="600"
            >
                <a-form :model="form" name="basic" layout="vertical">
                    <a-form-item required label="Name">
                        <a-input v-model:value="form.name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </a-form-item>
                    <a-card title="Specific">
                        <a-form-item required label="Name">
                            <a-input v-model:value="clearance" />
                        </a-form-item>
                        <a-form-item required label="Amount">
                            <a-input v-model:value="amount" />
                        </a-form-item>
                        <a-button class="mb-4" type="primary" @click="addField"
                            >Add Specific</a-button
                        >
                        <div v-if="form.meta.length > 0">
                            <a-table
                                :columns="descriptionColumns"
                                :dataSource="form.meta"
                                :pagination="false"
                                class="shadow-xl"
                                bordered
                            >
                                <template
                                    #bodyCell="{ column, record, text, index }"
                                >
                                    <template
                                        v-if="column.dataIndex === 'meta'"
                                    >
                                        {{ record.clearance }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'amount'"
                                    >
                                        {{
                                            new Intl.NumberFormat("PHP", {
                                                style: "currency",
                                                currency: "PHP",
                                            }).format(record.amount)
                                        }}
                                    </template>
                                    <template
                                        v-if="column.dataIndex === 'actions'"
                                    >
                                        <a-button @click="removeField(index)">
                                            <DeleteFilled />
                                        </a-button>
                                    </template>
                                </template>
                            </a-table>
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
        </div>
    </AuthenticatedLayout>
</template>
