<script setup>
import InputError from "@/Components/InputError.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    DeleteFilled,
    EditFilled,
    ExclamationCircleFilled,
    RestFilled,
} from "@ant-design/icons-vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { Modal, message } from "ant-design-vue";
import { h, ref } from "vue";
import TableComponent from "@/Components/Table.vue";
const [modal] = Modal.useModal();

const props = defineProps({
    transactions: Object,
});

const page = usePage();

const form = useForm({
    name: null,
    meta: [],
});

const clearance = ref("");
const amount = ref(0);
const toPay = ref(0);
const balance = ref(0);
const selectedStudentId = ref(null);

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

const descriptionColumns = ref([
    {
        title: "Specific",
        dataIndex: "meta",
        key: "meta",
        align: "center",
    },
    {
        title: "To Pay",
        dataIndex: "amount",
        key: "amount",
        align: "center",
    },
]);

const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const showPaymentModal = ref(false);
const meta = ref(null);
const transactionId = ref(null);

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
    meta.value.map((e) => {
        e.meta.map((meta) => {
            if (meta.balance !== "PAID" && meta.balance != 0) {
                meta.balance = Number(meta.balance) - Number(meta.toPay);
            } else {
                meta.balance =
                    Number(meta.amount) - Number(meta.toPay) == 0
                        ? "PAID"
                        : Number(meta.amount) - Number(meta.toPay);
            }
        });
    });
    router.post(
        route("submit-payment.store", selectedStudentId.value),
        {
            meta: meta.value,
            transactionId: transactionId.value,
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

const viewPayment = (val) => {
    meta.value = val.meta;
    showPaymentModal.value = true;
    selectedStudentId.value = val.student_id;
    transactionId.value = val.id;
};

const handleDecline = () => {
    router.post(
        route("decline-payment.store", selectedStudentId.value),
        {
            meta: meta.value,
            transactionId: transactionId.value,
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
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Transactions</div>

            <div>
                <TableComponent
                    :dataSource="props.transactions.data"
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
            <a-modal
                v-model:open="showPaymentModal"
                title="Payment"
                :footer="null"
            >
                <div>
                    <div v-for="(val, index) in meta" class="">
                        <div v-for="(x, i) in val.meta" class="flex space-x-8">
                            <a-card>
                                <div v-if="x.toPay != 0">
                                    <div>
                                        <span> Name: {{ val.name }} </span>
                                    </div>
                                    <div>Specific: {{ x.clearance }}</div>
                                    <div>Total Amount: {{ x.amount }}</div>
                                    <div>To Pay: {{ x.toPay }}</div>
                                </div>
                            </a-card>
                        </div>
                    </div>

                    <!-- <a-table
                        :dataSource="meta"
                        :columns="descriptionColumns"
                        :pagination="false"
                    >
                        <template #bodyCell="{ column, record, text }">
                            <template v-if="column.dataIndex === 'meta'">
                                <div v-for="(val, i) in record.meta" :key="i">
                                    <div v-if="val.toPay !== '0'" class="mb-2">
                                        {{ val.clearance }} {{ record }}
                                    </div>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                <div v-for="(val, i) in record.meta" :key="i">
                                    <div class="mb-2" v-if="val.toPay !== '0'">
                                        <ul class="list-disc">
                                            <li>
                                                {{
                                                    new Intl.NumberFormat(
                                                        "PHP",
                                                        {
                                                            style: "currency",
                                                            currency: "PHP",
                                                        }
                                                    ).format(val.toPay)
                                                }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </a-table> -->
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
        </div>
    </AuthenticatedLayout>
</template>
