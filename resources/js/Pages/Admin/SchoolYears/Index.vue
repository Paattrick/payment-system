<script setup>
import TableComponent from "@/Components/Table.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { EditFilled, ExclamationCircleOutlined } from "@ant-design/icons-vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { Modal, message } from "ant-design-vue";
import { createVNode, ref } from "vue";

const props = defineProps({
    school_years: Object,
});

const page = usePage();

const form = useForm({
    id: null,
    name: null,
    current_school_year: null,
});

console.log(props.school_years);

const selectedStudentId = ref(null);
const selectedFile = ref(null);

const columns = ref([
    {
        title: "Name ",
        dataIndex: "name",
        key: "name",
    },
    {
        title: "Status",
        dataIndex: "current_school_year",
        key: "current_school_year",
    },
    {
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
        width: 8,
    },
]);

const loading = ref(false);
const showModal = ref(false);

const viewSchoolYear = (data) => {
    form.id = data.id;
    form.name = data.name;
    form.current_school_year = data.current_school_year;
    showModal.value = true;
};

const submit = () => {
    form.put(route("school-year.update", form.id), {
        onSuccess: () => {
            message.success("Successfully updated.");
            handleCancel();
        },
    });
};

const handleCancel = () => {
    showModal.value = false;
    form.reset();
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">School Year</div>

            <div>
                <TableComponent
                    :dataSource="props.school_years.data"
                    :columns="columns"
                    :isLoading="loading"
                    :paginationData="props.school_years.meta"
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
                            v-if="
                                slotProps.column.dataIndex ===
                                'current_school_year'
                            "
                        >
                            {{ slotProps.record.current_school_year }}
                        </template>
                        <template
                            v-if="slotProps.column.dataIndex === 'actions'"
                        >
                            <div @click="viewSchoolYear(slotProps.record)">
                                <a-tooltip placement="topLeft">
                                    <template #title>
                                        <span>Edit School Year</span>
                                    </template>
                                    <a-button><EditFilled /></a-button>
                                </a-tooltip>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
            <a-modal v-model:open="showModal" :footer="null">
                <a-form layout="vertical">
                    <a-form-item label="Name">
                        <a-input type="text" v-model:value="form.name" />
                    </a-form-item>
                    <a-form-item label="Set as Current School Year">
                        <a-switch v-model:checked="form.current_school_year" />
                    </a-form-item>
                </a-form>
                <div class="flex justify-end mt-5">
                    <a-button class="mr-2" @click.prevent="handleCancel"
                        >Cancel</a-button
                    >
                    <a-button
                        type="primary"
                        :loading="form.processing"
                        @click.prevent="submit()"
                        >Update</a-button
                    >
                </div>
            </a-modal>
        </div>
    </AuthenticatedLayout>
</template>
