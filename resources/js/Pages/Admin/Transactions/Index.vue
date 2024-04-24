<script setup>
import TableComponent from "@/Components/Table.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { EditFilled, ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { Modal } from "ant-design-vue";
import { createVNode, ref, onMounted } from "vue";

const props = defineProps({
    transactions: Object,
});

const page = usePage();

const form = useForm({
    name: null,
    meta: [],
    collector_id: page.props.auth?.user?.id,
});

const selectedStudentId = ref(null);
const selectedFile = ref(null);

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
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const loading = ref(false);
const showPaymentModal = ref(false);
const meta = ref(null);
const transactionId = ref(null);
const showReceipt = ref(false);
const showDeclineModal = ref(false);
const note = ref(null);
const dataTable = ref([]);

onMounted(() => {
    setTable();
});

const setTable = () => {
    props.transactions.data.map((e) => {
        if (e.school_year_id == page.props.currentSchoolYear[0].id) {
            dataTable.value.push(e);
        }
    });
};

const submit = () => {
    meta.value.map((e) => {
        e.meta.map((meta) => {
            if (Number(meta.amount) - Number(meta.toPay) == 0) {
                meta.balance = "PAID";
            }
            if (meta.balance !== "PAID" && meta.balance > 0) {
                const test = Number(meta.balance) - Number(meta.toPay);
                if (test == 0) {
                    meta.balance = "PAID";
                } else {
                    meta.balance = Number(meta.balance) - Number(meta.toPay);
                }
            }
            if (meta.balance !== "PAID" && meta.balance == 0) {
                meta.balance = Number(meta.amount) - Number(meta.toPay);
            }
            meta.toPay = meta.balance;
            meta.status = "accepted";
            meta.totalPaid = Number(meta.amount) - Number(meta.balance);
        });
    });
    router.post(
        route("submit-payment.store", selectedStudentId.value),
        {
            meta: meta.value,
            transactionId: transactionId.value,
            type: type.value,
            reference: reference.value,
            collector_id: form.collector_id,
            school_year_id: page.props.activeSchoolYear[0].id,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                showPaymentModal.value = false;
            },
        }
    );
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

const reference = ref(null);
const type = ref(null);
const viewPayment = (val) => {
    type.value = val.mode_of_payment;
    reference.value = val.reference;
    selectedFile.value = val.file;
    meta.value = val.meta;
    showPaymentModal.value = true;
    selectedStudentId.value = val.student_id;
    transactionId.value = val.id;
};

const handleDecline = () => {
    Modal.confirm({
        title: "Do you Want to decline these payment?",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode("div", {
            style: "color:red;",
        }),
        onOk() {
            showDeclineModal.value = true;
        },
        onCancel() {},
    });
};

const showReceiptModal = () => {
    showReceipt.value = true;
};

const submitDecline = () => {
    meta.value.map((e) => {
        e.meta.map((meta) => {
            meta.toPay = 0;
            meta.status = "";
        });
    });
    router.post(
        route("decline-payment.store", selectedStudentId.value),
        {
            meta: meta.value,
            transactionId: transactionId.value,
            note: note.value,
            type: type.value,
            collector_id: page.props.auth?.user?.id,
            reference: reference.value,
            school_year_id: page.props.activeSchoolYear[0]?.id,
        },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                showPaymentModal.value = false;
                showDeclineModal.value = false;
            },
        }
    );
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Payment Request</div>

            <div>
                <TableComponent
                    :dataSource="dataTable"
                    :columns="columns"
                    :isLoading="loading"
                    :paginationData="props.transactions.meta"
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
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div
                                v-if="!page.props.auth.role.is_student"
                                class="flex space-x-4"
                            >
                                <div @click="viewPayment(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>View Payment</span>
                                        </template>
                                        <a-button><EditFilled /></a-button>
                                    </a-tooltip>
                                </div>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
            <a-modal
                v-model:open="showPaymentModal"
                title="Payment"
                :footer="null"
            >
                <div>
                    <div v-for="(val, index) in meta" class="">
                        <div v-for="(x, i) in val.meta" class="flex space-x-8">
                            <a-card>
                                <div
                                    v-if="x.toPay != 0 && x.balance !== 'PAID'"
                                >
                                    <div>
                                        <span class="flex space-x-4">
                                            <div class="text-lg font-bold">
                                                Name:
                                            </div>
                                            <div class="text-lg">
                                                {{ val.name }}
                                            </div>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="flex space-x-4">
                                            <div class="text-lg font-bold">
                                                Specific:
                                            </div>
                                            <div class="text-lg">
                                                {{ x.clearance }}
                                            </div>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="flex space-x-4">
                                            <div class="text-lg font-bold">
                                                Total Amount:
                                            </div>
                                            <div class="text-lg">
                                                {{
                                                    new Intl.NumberFormat(
                                                        "PHP",
                                                        {
                                                            style: "currency",
                                                            currency: "PHP",
                                                        }
                                                    ).format(x.amount)
                                                }}
                                            </div>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="flex space-x-4">
                                            <div class="text-lg font-bold">
                                                Balance:
                                            </div>
                                            <div class="text-lg">
                                                {{
                                                    new Intl.NumberFormat(
                                                        "PHP",
                                                        {
                                                            style: "currency",
                                                            currency: "PHP",
                                                        }
                                                    ).format(x.balance)
                                                }}
                                            </div>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="flex space-x-4">
                                            <div class="text-lg font-bold">
                                                To Pay:
                                            </div>
                                            <div class="text-lg">
                                                {{
                                                    new Intl.NumberFormat(
                                                        "PHP",
                                                        {
                                                            style: "currency",
                                                            currency: "PHP",
                                                        }
                                                    ).format(x.toPay)
                                                }}
                                            </div>
                                        </span>
                                    </div>
                                </div>
                            </a-card>
                        </div>
                    </div>
                    <div class="mx-6">
                        <a @click="showReceiptModal"> view receipt </a>
                    </div>

                    <div
                        class="flex justify-end mt-5"
                        v-if="
                            page.props.auth.role.is_employee ||
                            !page.props.auth.role.is_admin
                        "
                    >
                        <a-button class="mr-2" @click.prevent="handleDecline"
                            >Decline</a-button
                        >
                        <a-button
                            type="primary"
                            :loading="form.processing"
                            @click.prevent="submit()"
                            >Accept</a-button
                        >
                    </div>
                </div>
            </a-modal>
            <div>
                <a-modal
                    v-model:open="showReceipt"
                    :title="`Reference No: ${reference}`"
                    :footer="null"
                >
                    <div class="flex justify-between">
                        <div class="mx-auto">
                            <img
                                :src="`../storage/images/${selectedFile}`"
                                class="h-[250px] w-[250px]"
                            />
                        </div>
                    </div>
                    <div class="flex justify-end mt-5">
                        <a-button type="primary" @click="showReceipt = false"
                            >Close</a-button
                        >
                    </div>
                </a-modal>
            </div>
            <a-modal v-model:open="showDeclineModal" :footer="null">
                <a-form layout="vertical">
                    <a-form-item label="Add Note">
                        <a-input
                            v-model:value="note"
                            placeholder="add note..."
                        />
                    </a-form-item>
                </a-form>
                <div
                    class="flex justify-end mt-5"
                    v-if="
                        page.props.auth.role.is_employee ||
                        !page.props.auth.role.is_admin
                    "
                >
                    <a-button class="mr-2" @click="showDeclineModal = false"
                        >Cancel</a-button
                    >
                    <a-button
                        type="primary"
                        :loading="form.processing"
                        @click.prevent="submitDecline()"
                        >Submit</a-button
                    >
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
