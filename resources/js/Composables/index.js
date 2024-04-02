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
    function sections(grade) {
        if(grade == '7') {
            return [
                { label: "Honesty", value: "Honesty" },
                { label: "Humility", value: "Humility" },
                { label: "Loyalty", value: "Loyalty" },
                { label: "Unity", value: "Unity" },
            ]
        }
        if(grade == '8') {
            return [
                { label: "Courtesy", value: "Courtesy" },
                { label: "Integrity", value: "Integrity" },
                { label: "Perseverance", value: "Perseverance" },
                { label: "Sincerity", value: "Sincerity" },
            ]
        }
        if(grade == '9') {
            return [
                { label: "Charity", value: "Charity" },
                { label: "Faith", value: "Faith" },
                { label: "Patience", value: "Patience" },
                { label: "Simplicity", value: "Simplicity" },
                { label: "Wisdom", value: "Wisdom" },
            ]
        }
        if(grade == '10') {
            return [
                { label: "Chastity", value: "Chastity" },
                { label: "Hope", value: "Hope" },
                { label: "Love", value: "Love" },
                { label: "Prosperity", value: "Prosperity" },
            ]
        }
        else{
            return [
                { label: "Honesty", value: "Honesty" },
                { label: "Humility", value: "Humility" },
                { label: "Loyalty", value: "Loyalty" },
                { label: "Unity", value: "Unity" },
                { label: "Courtesy", value: "Courtesy" },
                { label: "Integrity", value: "Integrity" },
                { label: "Perseverance", value: "Perseverance" },
                { label: "Sincerity", value: "Sincerity" },
                { label: "Charity", value: "Charity" },
                { label: "Faith", value: "Faith" },
                { label: "Patience", value: "Patience" },
                { label: "Simplicity", value: "Simplicity" },
                { label: "Wisdom", value: "Wisdom" },
                { label: "Chastity", value: "Chastity" },
                { label: "Hope", value: "Hope" },
                { label: "Love", value: "Love" },
                { label: "Prosperity", value: "Prosperity" },
            ];
        }
    }
    function strands() {
        return [
            { label: "STEM", value: "STEM" },
            { label: "HUMSS-A", value: "HUMSS-A" },
            { label: "HUMSS-B", value: "HUMSS-B" },
            { label: "TVL-ICT", value: "TVL-ICT" },
            { label: "TVL-Cookery", value: "TVL-Cookery" },
            { label: "TVL-Hairdressing", value: "TVL-Hairdressing" },
            { label: "TVL-Caregiving-A", value: "TVL-Caregiving-A" },
            { label: "TVL-Caregiving-B", value: "TVL-Caregiving-B" },
        ];
    }

    return {
        grades,
        sections,
        strands,
        
    };
    
 
}
