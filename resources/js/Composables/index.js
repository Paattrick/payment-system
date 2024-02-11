import { ref, onMounted, onUnmounted } from "vue";
import { usePage } from "@inertiajs/vue3";
// import moment from 'moment-timezone'
// import dayjs from "dayjs";
// import timezone from "dayjs/plugin/timezone";
// import utc from "dayjs/plugin/utc";

// dayjs.extend(utc);
// dayjs.extend(timezone);

export function composables() {
    function grades() {
        return [
            { label: "Grade 7", value: "7" },
            { label: "Grade 8", value: "8" },
            { label: "Grade 9", value: "9" },
            { label: "Grade 10", value: "10" },
            { label: "Grade 11", value: "11" },
            { label: "Grade 12", value: "12" },
        ];
    }
    function sections() {
        return [
            { label: "Honesty", value: "Honesty" },
            { label: "Humility", value: "Humility" },
            { label: "Courtesy", value: "Courtesy" },
            { label: "Chastity", value: "Chastity" },
            { label: "Love", value: "Love" },
            { label: "Faith", value: "Faith" },
        ];
    }
    function strands() {
        return [
            { label: "TVL-ICT", value: "TVL-ICT" },
            { label: "TVL-Beauty Care", value: "TVL-Beauty Care" },
            { label: "STEM", value: "STEM" },
            { label: "HUMSS", value: "HUMSS" },
        ];
    }

    return {
        grades,
        sections,
        strands,
    };
}
