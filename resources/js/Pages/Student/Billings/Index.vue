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
const tableData = ref([]);
const toPay = ref([]);

onMounted(() => {
    if (page.props.auth.user.meta === null) {
        tableData.value = props.fees.data;
    } else {
        tableData.value = page.props.auth.user.meta;
    }
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
]);

const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);
const selectedBillings = ref([]);
const showQr = ref(false);
const showFileModal = ref(false);
const file = ref(null);
const submitLoading = ref(false);

const handleCancel = () => {
    form.reset();
    form.errors = {};
    showModal.value = false;
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
            fees: toPay.value,
            student: page.props.auth.user,
            file: file.value,
        }),
        {
            onStart: () => (submitLoading.value = true),
            onFinish: () => (submitLoading.value = false),
            onSuccess: () => {
                showFileModal.value = false;
                refresh();
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

const onSelectChange = (value) => {
    selectedBillings.value = value;
    if (selectedBillings.value.length > 0) {
        isDisable.value = true;
        toPay.value = [];
    } else {
        isDisable.value = false;
    }

    props.fees.data.map((e) => {
        selectedBillings.value.map((x) => {
            if (e.id == x) {
                toPay.value.push({
                    id: e.id,
                    name: e.name,
                    meta: e.meta,
                });
            }
        });
    });
};

const uploadFile = () => {
    showQr.value = false;
    showFileModal.value = true;
};

const payBillings = () => {
    showModal.value = true;
};

const handlePayment = () => {
    showQr.value = true;
};

const handlePay = (event, index) => {
    console.log(index);
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Billings</div>
            <div>
                <TableComponent
                    :dataSource="tableData"
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
                                    :disabled="!isDisable"
                                    type="primary"
                                    @click="payBillings()"
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
                                <div>
                                    <ul class="list-disc">
                                        <li>
                                            {{
                                                new Intl.NumberFormat("PHP", {
                                                    style: "currency",
                                                    currency: "PHP",
                                                }).format(
                                                    val.amount - val.toPay
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
                        :dataSource="toPay"
                        :pagination="false"
                        class="shadow-xl mb-4"
                        bordered
                    >
                        <template #bodyCell="{ column, record, text, index }">
                            <template v-if="column.dataIndex === 'meta'">
                                {{ record.name }}
                            </template>
                            <template v-if="column.dataIndex === 'amount'">
                                <div
                                    v-for="(val, i) in record.meta"
                                    :index="i"
                                    class="flex justify-between mb-2 w-full"
                                >
                                    <div class="pt-2">
                                        {{ val.clearance }}
                                    </div>
                                    <div class="flex space-x-4">
                                        <div class="pt-2">
                                            {{
                                                new Intl.NumberFormat("PHP", {
                                                    style: "currency",
                                                    currency: "PHP",
                                                }).format(val.amount)
                                            }}
                                        </div>
                                        <div>
                                            <a-input-number
                                                placeholder="0.00"
                                                :step="0.01"
                                                class="w-full"
                                                name="to_pay"
                                                @change="handlePay($event, i)"
                                                v-model:value="val.toPay"
                                            >
                                            </a-input-number>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>
                    </a-table>
                </div>
                <div class="flex justify-end">
                    <a-button @click="handlePayment()" type="primary">
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
