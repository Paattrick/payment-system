<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { Modal, message } from "ant-design-vue";
import { onMounted, ref } from "vue";
import TableComponent from "@/Components/Table.vue";
import InputError from "@/Components/InputError.vue";
const [modal] = Modal.useModal();

const props = defineProps({
    fees: Object,
});

const form = useForm({});
const page = usePage();

const isDisable = ref(false);
const totalToPay = ref(0);

console.log(page.props.auth.user.meta);

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
        title: "Balance",
        dataIndex: "balance",
        key: "balance",
        align: "center",
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
const selectedBillings = ref({});
const selectedBillingsDisabled = ref(false);
const meta = ref({});
const sum = ref(0);
const showQr = ref(false);
const showFileModal = ref(false);
const file = ref(null);
const submitLoading = ref(false);

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
    console.log(form.meta);
    // router.post(
    //     route(
    //         "billings-submit.store",
    //         {
    //             fees: form.value,
    //             student: page.props.auth.user,
    //             file: file.value,
    //         },
    //         {
    //             onStart: () => (submitLoading.value = true),
    //             onFinish: () => (submitLoading.value = false),
    //             onSuccess: () => {
    //                 showFileModal.value = false;
    //             },
    //         }
    //     )
    // );
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

const onSelectChange = (value) => {
    selectedBillings.value = value;
};

const proceedPayment = () => {
    const total = [];
    if (form.meta !== undefined) {
        form.meta.forEach((element) => {
            total.push(Number(element.toPay));
        });
    }

    // iterate over each item in the array
    for (let i = 0; i < total.length; i++) {
        sum.value += total[i];
    }

    showQr.value = true;
};

const uploadFile = () => {
    showQr.value = false;
    showFileModal.value = true;
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Billings</div>
            <div>
                <TableComponent
                    :dataSource="page.props.auth.user.meta"
                    :columns="columns"
                    :isLoading="loading"
                    :hasRowSelection="true"
                    @onSelectChange="onSelectChange($event)"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div class="flex space-x-4">
                                <a-button @click="refresh()">Refresh</a-button>
                            </div>
                            <div class="flex justify-end">
                                <a-button
                                    type="primary"
                                    @click="proceedPayment()"
                                    >Pay Selected Billings</a-button
                                >
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
                                <div class="mb-2">
                                    <ul class="list-disc">
                                        <li>
                                            {{
                                                new Intl.NumberFormat("PHP", {
                                                    style: "currency",
                                                    currency: "PHP",
                                                }).format(
                                                    val.balance === "0"
                                                        ? val.amount
                                                        : val.balance
                                                )
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
                                    @change="handlePay($event, index)"
                                >
                                </a-input-number>
                            </template>
                        </template>
                    </a-table>
                </div>
                <div class="flex justify-end">
                    <a-button
                        @click="showModal = false"
                        type="primary"
                        :disabled="isDisable"
                    >
                        Okay
                    </a-button>
                </div>
            </a-modal>
            <div>
                <a-modal
                    v-model:open="showQr"
                    title="Scan To Pay"
                    :footer="null"
                >
                    <div class="">
                        <div class="flex pl-8">
                            <img
                                class="w-[400px] h-[600px]"
                                src="../../../../../public/build/assets/QR.jpg"
                            />
                        </div>
                    </div>
                    <div class="flex justify-end pt-5">
                        <a-button @click="uploadFile()" type="primary">
                            Proceed Payment
                        </a-button>
                    </div>
                </a-modal>
            </div>
            <div>
                <a-modal
                    v-model:open="showFileModal"
                    title="Payment"
                    :footer="null"
                >
                    <a-form layout="vertical">
                        <a-form-item label="Upload Screenshot">
                            <a-input v-model:value="file" type="file" />
                        </a-form-item>
                    </a-form>
                    <div class="flex justify-end pt-5">
                        <a-button
                            @click="submit()"
                            type="primary"
                            :loading="submitLoading"
                        >
                            Submit
                        </a-button>
                    </div>
                </a-modal>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
