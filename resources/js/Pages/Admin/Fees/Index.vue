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
import { h, ref, onMounted } from "vue";
import TableComponent from "@/Components/Table.vue";
const [modal] = Modal.useModal();

const props = defineProps({
    fees: Object,
    school_years: Object,
});

const page = usePage();

const form = useForm({
    meta: [],
    name: null,
    school_year: null,
    collectibles: 0,
});

const clearance = ref("");
const amount = ref(0);
const toPay = ref(0);
const balance = ref(0);

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

onMounted(() => {
    setTable();
});

const showModal = ref(false);
const isEditing = ref(false);
const loading = ref(false);

const handleAdd = () => {
    showModal.value = true;
    isEditing.value = false;
};

const handleCancel = () => {
    refresh();
    form.reset();
    form.errors = {};
    total_collectibles.value = 0;
    showModal.value = false;
};

const dataTable = ref({
    meta: [],
});

const setTable = () => {
    props.fees.data.map((e) => {
        if (e.school_year_id == page.props.currentSchoolYear[0].id) {
            dataTable.value.meta.push({
                meta: e.meta,
                name: e.name,
                id: e.id,
                school_year_id: e.school_year_id,
            });
        }
    });
};

const addField = () => {
    if (!clearance.value) {
        return message.error("Specific is required");
    } else {
        form.meta.push({
            clearance: clearance.value,
            amount: parseFloat(amount.value).toFixed(2),
            toPay: parseFloat(toPay.value).toFixed(2),
            balance: parseFloat(amount.value).toFixed(2),
            status: null,
            totalPaid:
                parseFloat(amount.value).toFixed(2) -
                parseFloat(amount.value).toFixed(2),
        });
        clearance.value = null;
        amount.value = 0;
    }
};

const removeField = (index) => {
    form.meta.splice(index, 1);
};

const handleSelectedId = ref(null);

const handleEdit = (val) => {
    handleSelectedId.value = val.id;
    form.meta = val.meta;
    form.name = val.name;
    props.school_years.map((e) => {
        if (e.id == val.school_year_id) {
            form.school_year = e.name;
        }
    });

    showModal.value = true;
    isEditing.value = true;
};

const total_collectibles = ref(0);

const submit = () => {
    if (form.meta.length == 0) {
        return message.error("Specific is required");
    }

    form.meta.map((e) => {
        total_collectibles.value = total_collectibles.value + Number(e.amount);
    });
    form.collectibles = total_collectibles.value;
    form.post(route("fees.store"), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            total_collectibles.value = 0;
            setTable();
            showModal.value = false;
        },
    });
};

const update = () => {
    form.meta.map((e) => {
        total_collectibles.value = total_collectibles.value + Number(e.amount);
    });
    form.collectibles = total_collectibles.value;
    props.school_years.map((e) => {
        if (e.name == form.school_year) {
            form.school_year = e.id;
        }
    });
    form.put(route("fees.update", handleSelectedId.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            showModal.value = false;
            setTable();
        },
        onError: (e) => {
            total_collectibles.value = 0;
        },
    });
};

const handleDelete = (val) => {
    Modal.confirm({
        title: "Are you sure to delete this Payment?",
        icon: h(ExclamationCircleFilled),
        okText: "OK",
        onOk() {
            router.delete(route("fees.destroy", val.id), {
                onSuccess: () => {
                    setTable();
                },
            });
            message.success("Successfully Deleted!");
        },
        cancelText: "Cancel",
    });
};

const refresh = () => {
    location.reload();
};

const handleChangeClearance = () => {
    total_collectibles.value = 0;
    form.errors = {};
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Fees</div>

            <div>
                <TableComponent
                    :dataSource="dataTable.meta"
                    :columns="columns"
                    :isLoading="loading"
                >
                    <template #actionButtons>
                        <div class="flex justify-between">
                            <div>
                                <a-button @click="refresh()">Refresh</a-button>
                            </div>
                            <div class="justify-end">
                                <a-button type="primary" @click="handleAdd">
                                    Add Fee
                                </a-button>
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template v-if="slotProps.column.dataIndex === 'name'">
                            {{ slotProps.record.name }}
                        </template>
                        <template v-if="slotProps.column.dataIndex === 'meta'">
                            <div v-for="(val, i) in slotProps.record" :key="i">
                                <div v-for="(meta, index) in val" :key="index">
                                    <div class="mb-2">
                                        <ul class="list-disc">
                                            {{
                                                meta.clearance
                                            }}
                                        </ul>
                                    </div>
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
                                    {{
                                        new Intl.NumberFormat("PHP", {
                                            style: "currency",
                                            currency: "PHP",
                                        }).format(val.amount)
                                    }}
                                </div>
                            </div>
                        </template>
                        <template v-if="slotProps.column.dataIndex === 'meta'">
                            {{ slotProps.record.clearance }}
                        </template>
                        <template v-if="slotProps.column.dataIndex === 'meta'">
                            {{ slotProps.record.clearance }}
                        </template>
                        <template
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div class="flex space-x-4">
                                <div @click="handleEdit(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Edit</span>
                                        </template>
                                        <a-button><EditFilled /></a-button>
                                    </a-tooltip>
                                </div>
                                <div @click="handleDelete(slotProps.record)">
                                    <a-tooltip placement="topLeft">
                                        <template #title>
                                            <span>Delete Fee</span>
                                        </template>
                                        <a-button><RestFilled /></a-button>
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
                    <a-form-item required label="For School Year">
                        <a-select
                            v-model:value="form.school_year"
                            :options="
                                props.school_years.map((item) => ({
                                    value: item.id,
                                    label: item.name,
                                }))
                            "
                            :filter-option="
                                (input, option) =>
                                    option.label
                                        .toLowerCase()
                                        .indexOf(input.toLowerCase()) >= 0
                            "
                        >
                        </a-select>
                        <InputError
                            class="mt-2"
                            :message="form.errors.school_year"
                        />
                    </a-form-item>
                    <a-form-item required label="Name of collection">
                        <a-input v-model:value="form.name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </a-form-item>
                    <a-card title="Specific">
                        <a-form-item required label="Name">
                            <a-input
                                @change="handleChangeClearance"
                                v-model:value="clearance"
                            />
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
                                        <div>
                                            <InputError
                                                class="mt-2"
                                                :message="
                                                    form.errors[
                                                        `meta.${index}.clearance`
                                                    ]
                                                "
                                            />
                                        </div>
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
