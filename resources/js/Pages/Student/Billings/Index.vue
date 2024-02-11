<script setup>
import InputError from "@/Components/InputError.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {
    DeleteFilled,
    EditFilled,
    ExclamationCircleFilled,
    RestFilled,
    ShoppingCartOutlined,
} from "@ant-design/icons-vue";
import { useForm } from "@inertiajs/vue3";
import { Modal } from "ant-design-vue";
import { h, ref } from "vue";
const [modal] = Modal.useModal();

const props = defineProps({
    fees: Object,
});

const form = useForm({
    name: null,
    meta: [],
});

const clearance = ref("");
const amount = ref(0);

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
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const showModal = ref(false);
const isEditing = ref(false);

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

    console.log(form.meta);
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
    form.put(route("employees.update", form.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
        },
    });
};

const handlePay = (val) => {
    Modal.confirm({
        title: "Payment",
        icon: h(ExclamationCircleFilled),
        content: "Are you sure to continue to pay this billable?",
        okText: "OK",
        cancelText: "Cancel",
    });
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="mb-4 flex space-x-4">
                <a-button type="primary" @click="handleAdd"> Add Fee </a-button>
            </div>
            <div>
                <a-table :columns="columns" :dataSource="props.fees.data">
                    <template #bodyCell="{ column, record, text, index }">
                        <template v-if="column.dataIndex === 'meta'">
                            <div v-for="(val, i) in record.meta" :key="i">
                                <div class="mb-2">
                                    {{ i + 1 }}. {{ val.clearance }}
                                </div>
                            </div>
                        </template>
                        <template v-if="column.dataIndex === 'amount'">
                            <div v-for="(val, i) in record.meta" :key="i">
                                <div class="mb-2">
                                    {{
                                        new Intl.NumberFormat("PHP", {
                                            style: "currency",
                                            currency: "PHP",
                                        }).format(val.amount)
                                    }}
                                </div>
                            </div>
                        </template>
                        <template v-if="column.dataIndex === 'actions'">
                            <div class="flex space-x-4">
                                <div @click="handleEdit(record)">
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
                </a-table>
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
                                <a-tooltip placement="topLeft">
                                    <template #title>
                                        <span>Add this to cart</span>
                                    </template>
                                    <a-button @click="handlePay(index)">
                                        <ShoppingCartOutlined />
                                    </a-button>
                                </a-tooltip>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
