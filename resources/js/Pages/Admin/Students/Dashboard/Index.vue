<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";
import { composables } from "@/Composables/index.js";

const { sections, strands, grades } = composables();

const grade = ref(null);
const section = ref(null);

const submit = () => {
    router.visit(
        route("students.index", {
            grade: grade.value,
            section: section.value,
        })
    );
};
</script>
<template>
    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto">
            <div>Students Filtering</div>
            <div>
                <a-card class="bg-gray-200">
                    <a-form-item label="Search Student" layout="vertical">
                        <a-input-search />
                    </a-form-item>
                    <a-form layout="vertical">
                        <div>
                            <a-form-item label="Grade">
                                <a-select
                                    v-model:value="grade"
                                    allowClear
                                    :options="grades()"
                                    :filter-option="
                                        (input, option) =>
                                            option.label
                                                .toLowerCase()
                                                .indexOf(input.toLowerCase()) >=
                                            0
                                    "
                                >
                                </a-select>
                            </a-form-item>
                            <a-form-item
                                :label="
                                    Number(grade) > 10 ? 'Strand' : 'Section'
                                "
                            >
                                <a-select
                                    v-model:value="section"
                                    allowClear
                                    :options="
                                        Number(grade) > 10
                                            ? strands()
                                            : sections()
                                    "
                                    :filter-option="
                                        (input, option) =>
                                            option.label
                                                .toLowerCase()
                                                .indexOf(input.toLowerCase()) >=
                                            0
                                    "
                                >
                                </a-select>
                            </a-form-item>
                        </div>
                        <div>
                            <a-button
                                type="primary"
                                :loading="false"
                                @click.prevent="submit()"
                                >Search</a-button
                            >
                        </div>
                    </a-form>
                </a-card>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
