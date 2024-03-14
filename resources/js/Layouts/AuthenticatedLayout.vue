<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link, usePage } from "@inertiajs/vue3";
import {
    MenuUnfoldOutlined,
    MenuFoldOutlined,
    UserOutlined,
    DashboardOutlined,
    CreditCardOutlined,
    HistoryOutlined,
    TeamOutlined,
    ClockCircleOutlined,
    DeleteOutlined,
    BellFilled,
} from "@ant-design/icons-vue";

const showingNavigationDropdown = ref(false);
const page = usePage();

const collapsed = ref(false);
const selectedKeys = ref([]);
</script>

<template>
    <a-layout class="min-h-screen">
        <a-layout-sider
            v-model:collapsed="collapsed"
            :trigger="null"
            collapsible
        >
            <div class="flex text-white h-[40px] justify-center pt-5">GNHS</div>
            <a-menu theme="dark" mode="inline" class="mt-3">
                <a-menu
                    v-model:selectedKeys="selectedKeys"
                    theme="dark"
                    mode="inline"
                >
                    <a-menu-item
                        key="1"
                        :class="
                            route().current('dashboard')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                    >
                        <DashboardOutlined />
                        <span>
                            <Link
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                                class="text-white"
                            >
                                Dashboard
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="2"
                        v-if="page.props.auth.role.is_admin"
                        :class="
                            route().current('students.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                    >
                        <UserOutlined />
                        <span>
                            <Link
                                :href="route('students.index')"
                                :active="route().current('students.index')"
                                class="text-white"
                            >
                                Students
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="3"
                        v-if="page.props.auth.role.is_admin"
                        :class="
                            route().current('employees.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                    >
                        <TeamOutlined />
                        <span>
                            <Link
                                :href="route('employees.index')"
                                :active="route().current('employees.index')"
                                class="text-white"
                            >
                                Collectors
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="4"
                        v-if="
                            page.props.auth.role.is_admin ||
                            page.props.auth.role.is_employee
                        "
                        :class="
                            route().current('transaction.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                    >
                        <ClockCircleOutlined />
                        <span>
                            <Link
                                :href="route('transaction.index')"
                                :active="route().current('transaction.index')"
                                class="text-white"
                            >
                                Transactions
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="5"
                        :class="
                            route().current('billings.index') ||
                            route().current('fees.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                        v-if="!page.props.auth.role.is_employee"
                    >
                        <CreditCardOutlined />
                        <span>
                            <Link
                                :href="
                                    page.props.auth.role.is_student
                                        ? route('billings.index')
                                        : route('fees.index')
                                "
                                :active="route().current('archives.index')"
                                class="text-white"
                            >
                                Fees
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="6"
                        :class="
                            route().current('history.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                        v-if="!page.props.auth.role.is_student"
                    >
                        <HistoryOutlined />
                        <span>
                            <Link
                                :href="route('history.index')"
                                :active="route().current('history.index')"
                                class="text-white"
                            >
                                History
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="6"
                        :class="
                            route().current('student-history.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                        v-if="page.props.auth.role.is_student"
                    >
                        <HistoryOutlined />
                        <span>
                            <Link
                                :href="route('student-history.index')"
                                :active="
                                    route().current('student-history.index')
                                "
                                class="text-white"
                            >
                                History
                            </Link></span
                        >
                    </a-menu-item>
                    <a-menu-item
                        key="7"
                        v-if="page.props.auth.role.is_admin"
                        :class="
                            route().current('archives.index')
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                    >
                        <DeleteOutlined />
                        <span>
                            <Link
                                :href="route('archives.index')"
                                :active="route().current('archives.index')"
                                class="text-white"
                            >
                                Archives
                            </Link></span
                        >
                    </a-menu-item>
                </a-menu>
            </a-menu>
        </a-layout-sider>
        <a-layout>
            <a-layout-header style="background: #be123c; padding: 0">
                <div class="flex justify-between">
                    <div class="ml-5">
                        <menu-unfold-outlined
                            v-if="collapsed"
                            class="trigger"
                            @click="() => (collapsed = !collapsed)"
                        />
                        <menu-fold-outlined
                            v-else
                            class="trigger"
                            @click="() => (collapsed = !collapsed)"
                        />
                    </div>
                    <div class="hidden sm:flex sm:items-center sm:ms-6 mr-5">
                        <!-- Settings Dropdown -->
                        <div
                            class="pt-2 mr-4"
                            v-if="!page.props.auth.role.is_student"
                        >
                            <Link
                                :href="route('transaction.index')"
                                :active="route().current('transaction.index')"
                            >
                                <a-badge :count="page.props.notificationCount">
                                    <!-- <a-avatar shape="square" size="large" /> -->
                                    <BellFilled class="text-2xl" />
                                </a-badge>
                            </Link>
                        </div>
                        <div class="ms-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="ms-2 -me-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button
                            @click="
                                showingNavigationDropdown =
                                    !showingNavigationDropdown
                            "
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        >
                            <svg
                                class="h-6 w-6"
                                stroke="currentColor"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex':
                                            !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex':
                                            showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </a-layout-header>

            <!-- <a-layout-content
                :style="{
                    margin: '24px 16px',
                    padding: '24px',
                    background: '#fff',
                    minHeight: '280px',
                }"
            >
                Content
            </a-layout-content> -->
            <header
                class="bg-gray-200 min-h-screen py-8 height-md:mb-32 flex-1 relative"
            >
                <div class="py-8 height-md:mb-32 max-w-7xl mx-auto">
                    <slot />
                </div>
            </header>
        </a-layout>
    </a-layout>
</template>

<style>
#components-layout-demo-custom-trigger .trigger {
    font-size: 18px;
    line-height: 64px;
    padding: 0 24px;
    cursor: pointer;
    transition: color 0.3s;
}

#components-layout-demo-custom-trigger .trigger:hover {
    color: #1890ff;
}

#components-layout-demo-custom-trigger .logo {
    height: 32px;
    background: rgba(255, 255, 255, 0.3);
    margin: 16px;
}

.site-layout .site-layout-background {
    background: #fff;
}
</style>
