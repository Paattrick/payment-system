<script setup>
import { ref } from "vue";
import { watchDebounced } from "@vueuse/core";
import { computed } from "@vue/reactivity";

const props = defineProps({
    currentPage: String,
    dataSource: {
        type: Object,
        default: {},
    },
    paginationData: {
        type: Object,
        default: {},
    },
    columns: {
        type: Array,
        required: true,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    isCommonHeaders: {
        type: Boolean,
        default: false,
    },
    hasRowSelection: {
        type: Boolean,
        default: false,
    },
    hasAddButton: {
        type: Boolean,
        default: false,
    },
});

const search = ref(null);

watchDebounced(
    [search],
    (data) => {
        pagination.value.current = 1;
        if (data[0] == search.value) {
            emit("handleSearch", data[0]);
        }
    },
    {
        debounce: 300,
    }
);

const pagination = computed(() => ({
    total: props.paginationData.total,
    current: props.paginationData.current_page,
    pageSize: props.paginationData.per_page,
    showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
    showSizeChanger: false,
    style: { marginRight: "10px" },
}));

const handleAdd = () => {
    emit("handleAdd");
};

const refresh = () => {
    emit("handleRefresh");
};

const handleTableChange = (event) => {
    emit("change", event);
};

const onSelectChange = (val) => {
    emit("onSelectChange", val);
};
</script>

<template>
    <div class="my-4 bg-white rounded-lg px-4 py-4">
        <div class="mb-4">
            <slot name="actionButtons" />
        </div>
        <a-table
            @change="handleTableChange"
            :dataSource="dataSource"
            :columns="columns"
            :pagination="
                Object.keys(props.paginationData).length == 0
                    ? false
                    : pagination
            "
            :loading="isLoading"
            bordered
            rowKey="id"
            :row-selection="
                hasRowSelection
                    ? {
                          onChange: onSelectChange,
                      }
                    : null
            "
        >
            <template #bodyCell="{ column, text, record }">
                <slot
                    name="customColumn"
                    :column="column"
                    :text="text"
                    :record="record"
                ></slot>
            </template>
        </a-table>
    </div>
</template>
