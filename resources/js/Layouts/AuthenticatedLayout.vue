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
} from "@ant-design/icons-vue";

const showingNavigationDropdown = ref(false);
const page = usePage();

const collapsed = ref(false);
const selectedKeys = ref([]);

const links = ref([
    {
        name: "Dashboard",
        link: "dashboard",
        key: "1",
    },
    {
        name: "Students",
        link: "students.index",
        key: "2",
    },
    {
        name: "Collectors",
        link: "employees.index",
        key: "3",
    },
    {
        name: "Transactions",
        link: "transaction.index",
        key: "4",
    },
    {
        name: page.props.auth.role.is_student == true ? "Billings" : "Fees",
        link:
            page.props.auth.role.is_student == true
                ? "billings.index"
                : "fees.index",
        key: "5",
    },
    {
        name: "History",
        link: "history.index",
        key: "6",
    },
    {
        name: "Archives",
        link: "archives.index",
        key: "7",
    },
]);
</script>

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

<template>
    <a-layout class="min-h-screen">
        <a-layout-sider
            v-model:collapsed="collapsed"
            :trigger="null"
            collapsible
        >
            <div class="flex text-white h-[40px] justify-center pt-5">GNHS</div>
            <a-menu theme="dark" mode="inline" class="mt-3">
                <a-menu-item v-for="(link, index) in links" :key="link.key">
                    <div
                        class="flex"
                        :class="
                            route().current(link.link)
                                ? 'rounded-md bg-[#1677ff]'
                                : ''
                        "
                    >
                        <user-outlined :class="!collapsed ? 'pt-3 ml-5' : ''" />
                        <span>
                            <Link
                                :href="route(link.link)"
                                :active="route().current(link.link)"
                                class="text-white"
                            >
                                <div>
                                    {{ link.name }}
                                </div>
                            </Link>
                        </span>
                    </div>
                </a-menu-item>
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
    <div>
        <div class="min-h-screen bg-white">
            <nav class="bg-white border-b border-gray-100">
                <!-- Primary Navigation Menu -->
                <div class="bg-rose-700 mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <Link
                                    v-if="
                                        page.props.auth.role.is_admin ||
                                        page.props.auth.role.is_employee
                                    "
                                    :href="route('students.index')"
                                    :active="route().current('students.index')"
                                    class="text-white py-6"
                                >
                                    <div
                                        :class="
                                            route().current('students.index')
                                                ? 'py-1 rounded-md font-medium tracking-wide'
                                                : ''
                                        "
                                    >
                                        Students
                                    </div>
                                </Link>
                                <Link
                                    v-if="page.props.auth.role.is_admin"
                                    :href="route('employees.index')"
                                    :active="route().current('employees.index')"
                                    class="text-white py-6"
                                >
                                    <div
                                        :class="
                                            route().current('employees.index')
                                                ? 'py-1 rounded-md font-medium tracking-wide'
                                                : ''
                                        "
                                    >
                                        Collectors
                                    </div>
                                </Link>
                                <Link
                                    :href="route('transaction.index')"
                                    :active="
                                        route().current('transaction.index')
                                    "
                                    class="text-white py-6"
                                >
                                    <div
                                        :class="
                                            route().current('transaction.index')
                                                ? 'py-1 rounded-md font-medium tracking-wide'
                                                : ''
                                        "
                                    >
                                        Transactions
                                    </div>
                                </Link>
                                <Link
                                    v-if="
                                        page.props.auth.role.is_admin ||
                                        page.props.auth.role.is_student
                                    "
                                    :href="
                                        page.props.auth.role.is_student
                                            ? route('billings.index')
                                            : route('fees.index')
                                    "
                                    :active="route().current('fees.index')"
                                    class="text-white py-6"
                                >
                                    <div
                                        :class="
                                            route().current('fees.index')
                                                ? 'py-1 rounded-md font-medium tracking-wide'
                                                : ''
                                        "
                                    >
                                        {{
                                            page.props.auth.role.is_admin ||
                                            page.props.auth.role.is_employee
                                                ? "Fees"
                                                : "Billings"
                                        }}
                                    </div>
                                </Link>
                                <Link
                                    :href="route('history.index')"
                                    :active="route().current('history.index')"
                                    class="text-white py-6"
                                >
                                    <div
                                        :class="
                                            route().current('history.index')
                                                ? 'py-1 rounded-md font-medium tracking-wide'
                                                : ''
                                        "
                                    >
                                        History
                                    </div>
                                </Link>
                                <Link
                                    v-if="page.props.auth.role.is_admin"
                                    :href="route('archives.index')"
                                    :active="route().current('archives.index')"
                                    class="text-white py-6"
                                >
                                    <div
                                        :class="
                                            route().current('archives.index')
                                                ? 'py-1 rounded-md font-medium tracking-wide'
                                                : ''
                                        "
                                    >
                                        Archives
                                    </div>
                                </Link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <!-- Settings Dropdown -->
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
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
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
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800">
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="font-medium text-sm text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-gray-200 min-h-screen py-8 height-md:mb-32 flex-1 relative"
            >
                <div class="py-8 height-md:mb-32 max-w-7xl mx-auto">
                    <slot />
                </div>
            </header>
        </div>
    </div>
</template>
