<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { Modal, message } from "ant-design-vue";
import { onMounted, ref } from "vue";
import TableComponent from "@/Components/Table.vue";
const [modal] = Modal.useModal();

const props = defineProps({
    fees: Object,
});

const form = useForm({});
const page = usePage();

const isDisable = ref(false);
const totalToPay = ref(0);

onMounted(() => {
    form.value = { ...props.fees.data };
});

const columns = ref([
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
        title: "Amount",
        dataIndex: "amount",
        key: "amount",
        align: "center",
    },
    {
        title: "Amount To Pay",
        dataIndex: "actions",
        key: "actions",
        align: "center",
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
    router.post(
        route("billings-submit.store", {
            fees: form.value,
            student: page.props.auth.user,
        })
    );
};

const update = () => {
    form.put(route("employees.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const handlePay = (event, index) => {
    totalToPay.value = 0;
    if (Number(event) > Number(form.meta[index].amount)) {
        message.error("Amount to Pay must not exceed the exact amount");
        isDisable.value = true;
    } else {
        form.meta[index].balance =
            Number(form.meta[index].amount) - Number(event);
        isDisable.value = false;
    }
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
            <div class="page-title height-md:mb-30">Billings</div>
            <div>
                <TableComponent
                    :dataSource="props.fees.data"
                    :columns="columns"
                    :isLoading="loading"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div class="flex space-x-4">
                                <a-button @click="refresh()">Refresh</a-button>
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
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div class="flex space-x-4">
                                <div @click="handleEdit(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Pay Bill</span>
                                        </template>
                                        <a-button type="primary">Pay</a-button>
                                    </a-tooltip>
                                </div>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
            <a-modal
                v-model:open="showModal"
                title="Pay Bills"
                :footer="null"
                :afterClose="handleCancel"
                :width="600"
            >
                <div>
                    <a-table
                        :columns="descriptionColumns"
                        :dataSource="form.meta"
                        :pagination="false"
                        class="shadow-xl mb-4"
                        bordered
                    >
                        <template #bodyCell="{ column, record, text, index }">
                            <template v-if="column.dataIndex === 'meta'">
                                {{ record.clearance }}
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                {{
                                    new Intl.NumberFormat("PHP", {
                                        style: "currency",
                                        currency: "PHP",
                                    }).format(record.amount)
                                }}
                            </template>
                            <template v-if="column.dataIndex === 'actions'">
                                <a-input-number
                                    placeholder="0.00"
                                    :step="0.01"
                                    class="w-full"
                                    name="to_pay"
                                    v-model:value="record.toPay"
                                    @change="handlePay($event, index)"
                                >
                                </a-input-number>
                            </template>
                        </template>
                    </a-table>
                </div>
                <div class="flex justify-end">
                    <a-button
                        @click="submit()"
                        type="primary"
                        :disabled="isDisable"
                    >
                        Okay
                    </a-button>
                </div>
            </a-modal>
            <div></div>
        </div>
    </AuthenticatedLayout>
</template>
