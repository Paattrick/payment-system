<script setup>
import InputError from "@/Components/InputError.vue";
import TableComponent from "@/Components/Table.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { EditFilled, DeleteFilled } from "@ant-design/icons-vue";
import { router, useForm } from "@inertiajs/vue3";
import { Modal, message, notification } from "ant-design-vue";
import { h, ref } from "vue";

const props = defineProps({
    grades: Object,
});
console.log(props.grades);
const form = useForm({
    grade: null,
    sections: [],
});

const columns = ref([
    {
        title: "Grades ",
        dataIndex: "grade",
        key: "grade",
    },
    {
        title: "Sections",
        dataIndex: "sections",
        key: "sections",
    },
    {
        title: "Actions",
        dataIndex: "actions",
        key: "actions",
    },
]);

const loading = ref(false);
const showModal = ref(false);
const isEditing = ref(false);
const section = ref(null);

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

const handleAdd = () => {
    showModal.value = true;
};

const handleCancel = () => {
    showModal.value = false;
};

const handleAddSection = () => {
    if (form.sections.length == 0) {
        form.sections.push(section.value);
        section.value = null;
    } else {
        if (!form.sections.some((e) => e == section.value)) {
            form.sections.push(section.value);
            form.errors.sections = null;
            section.value = null;
        } else {
            form.errors.sections = "section is already added";
        }
    }
};

const submit = () => {
    form.post(route("grades.store"), {
        onSuccess: () => {
            notification.success({
                placement: "topRight",
                message: "Successfully Added",
                duration: 3,
                rtl: true,
            });
            showModal.value = false;
            form.reset();
            section.value = null;
        },
    });
};

const selectedGradeId = ref(null);

const handleEdit = (data) => {
    isEditing.value = true;
    selectedGradeId.value = data.id;
    form.grade = data.grade;
    form.sections = data.sections;
    showModal.value = true;
};

const update = () => {
    form.put(route("grades.update", selectedGradeId.value), {
        onSuccess: () => {
            notification.success({
                placement: "topRight",
                message: "Successfully Updated",
                duration: 3,
                rtl: true,
            });
            showModal.value = false;
            form.reset();
            section.value = null;
        },
    });
};

const handleDelete = (data) => {
    router.delete(route("grades.destroy", data.id));
};
</script>
<template>
    <AuthenticatedLayout>
        <div>
            <div class="page-title height-md:mb-30">Grade and Sections</div>

            <div>
                <TableComponent
                    :dataSource="props.grades"
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
                                    Add Grade and Section
                                </a-button>
                            </div>
                        </div>
                    </template>
                    <template #customColumn="slotProps">
                        <template
                            v-if="slotProps.column.dataIndex === 'sections'"
                        >
                            <div
                                v-for="(section, index) in slotProps.record
                                    .sections"
                                :key="index"
                            >
                                {{ section }}
                            </div>
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
                                            <span>Delete</span>
                                        </template>
                                        <a-button><DeleteFilled /></a-button>
                                    </a-tooltip>
                                </div>
                            </div>
                        </template>
                    </template>
                </TableComponent>
            </div>
            <a-modal
                v-model:open="showModal"
                :title="
                    isEditing
                        ? 'Edit Grade and Section'
                        : 'Add Grade and Section'
                "
                :footer="null"
                :afterClose="handleCancel"
                :width="600"
            >
                <a-form :model="form" name="basic" layout="vertical">
                    <a-form-item required label="Grade">
                        <a-input v-model:value="form.grade" />
                        <InputError class="mt-2" :message="form.errors.grade" />
                    </a-form-item>

                    <a-form-item label="Sections">
                        <a-input v-model:value="section" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.sections"
                        />
                        <a-button
                            class="my-5"
                            type="primary"
                            @click="handleAddSection"
                            >Add Section</a-button
                        >
                        <a-select
                            v-if="form.sections.length > 0"
                            v-model:value="form.sections"
                            mode="multiple"
                            style="width: 100%"
                        >
                        </a-select>
                    </a-form-item>

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
