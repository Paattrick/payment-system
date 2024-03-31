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

const form = useForm({
    meta: null,
});
const page = usePage();
const isDisable = ref(false);
const toPay = ref([]);

form.meta = [...page.props.auth.user.meta];

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

const columnsAdmission = ref([
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
        title: "Amount To Pay",
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
const showAdmission = ref(false);
const reference = ref(null);
const imageForm = useForm({
    image: null,
});

const handleCancel = () => {
    // form.reset();
    // form.errors = {};
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
    imageForm.post(
        route("billings-submit.store", {
            forceFormData: true,
            fees: form.meta,
            student: page.props.auth.user,
            reference: reference.value,
        }),
        {
            onSuccess: () => {
                showQr.value = false;
            },
        }
    );

    showFileModal.value = false;
};

const refresh = () => {
    window.location.reload();
};

const onSelectChange = (value) => {
    selectedBillings.value = value;
    if (selectedBillings.value.length > 0) {
        isDisable.value = true;
        toPay.value = [];
    } else {
        isDisable.value = false;
    }

    form.meta.map((e) => {
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
    // if (form.meta !== null) {
    //     form.meta.map((x) => {
    //         x.meta.map((meta) => {
    //             if (Number(meta.balance) > 0) {
    //                 if (
    //                     Number(meta.balance) + Number(meta.toPay) >
    //                     Number(meta.amount)
    //                 ) {
    //                     return message.error(
    //                         `Your payment is greater than the exact amount`
    //                     );
    //                 }
    //                 if (
    //                     Number(meta.balance) + Number(meta.toPay) <=
    //                     Number(meta.amount)
    //                 ) {
    //                     showQr.value = true;
    //                 }
    //             }
    //             if (Number(meta.balance) == 0) {
    //                 if (meta.toPay > meta.amount) {
    //                     return message.error(
    //                         `Your payment is greater than the exact amount`
    //                     );
    //                 }
    //                 if (meta.toPay < meta.amount) {
    //                     showQr.value = true;
    //                 }
    //             }
    //         });
    //     });
    // }
    showQr.value = true;
    showModal.value = false;
};

const selectedImage = ref(null);

const onChangeFile = (event) => {
    console.log(event.target.files[0]);
    selectedImage.value = event.target.files[0];
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Billings</div>
            <div>
                <TableComponent
                    :dataSource="form.meta"
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
                                <div v-if="val.balance !== 'PAID'">
                                    <ul class="list-disc">
                                        <li>
                                            {{
                                                new Intl.NumberFormat("PHP", {
                                                    style: "currency",
                                                    currency: "PHP",
                                                }).format(
                                                    val.balance == 0
                                                        ? val.amount
                                                        : val.balance
                                                )
                                            }}
                                        </li>
                                    </ul>
                                </div>
                                <div v-else>
                                    <ul class="list-disc">
                                        <a-tag
                                            class="font-semibold capitalize"
                                            color="#86EFAC"
                                        >
                                            {{ val.balance }}
                                        </a-tag>
                                    </ul>
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
                                <div v-for="(val, i) in record.meta" :index="i">
                                    <div
                                        v-if="val.balance != 'PAID'"
                                        class="flex justify-between mb-2 w-full"
                                    >
                                        <div class="pt-2">
                                            {{ val.clearance }}
                                        </div>
                                        <div class="flex space-x-4">
                                            <div class="pt-2">
                                                {{
                                                    new Intl.NumberFormat(
                                                        "PHP",
                                                        {
                                                            style: "currency",
                                                            currency: "PHP",
                                                        }
                                                    ).format(
                                                        val.balance > 0
                                                            ? val.balance
                                                            : val.amount
                                                    )
                                                }}
                                            </div>
                                            <div>
                                                <a-input-number
                                                    placeholder="0.00"
                                                    :step="0.01"
                                                    class="w-full"
                                                    name="to_pay"
                                                    v-model:value="val.toPay"
                                                >
                                                </a-input-number>
                                            </div>
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
                    <a-form layout="vertical" enctype="multipart/form-data">
                        <div class="flex pl-0">
                            <div>
                                <img
                                    class="w-[200px] h-[200px]"
                                    src="../../../../../public/build/assets/QR.jpg"
                                />
                            </div>
                            <div>
                                <a-form-item label="Upload Screenshot">
                                    <a-input
                                        type="file"
                                        @input="
                                            imageForm.image =
                                                $event.target.files[0]
                                        "
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="page.props?.errors?.file"
                                    />
                                </a-form-item>
                                <a-form-item label="Reference No.">
                                    <a-input
                                        v-model:value="reference"
                                        type="text"
                                    />
                                    <InputError
                                        class="mt-2"
                                        :message="page.props?.errors?.reference"
                                    />
                                </a-form-item>
                            </div>
                        </div>
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
