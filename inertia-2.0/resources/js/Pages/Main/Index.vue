<template>
    <div class="w-screen h-screen bg-slate-950 flex items-center justify-center relative overflow-hidden p-6 gap-6">
        <div class="fixed top-6 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none">
            <transition-group name="toast-anim">
                <div 
                    v-for="toast in toasts" 
                    :key="toast.id"
                    class="pointer-events-auto w-full border rounded-xl p-4 shadow-2xl backdrop-blur-md flex items-start gap-3 transition-all duration-300"
                    :class="{
                        'bg-emerald-950/90 border-emerald-500/30 text-emerald-200': toast.type === 'success',
                        'bg-rose-950/90 border-rose-500/30 text-rose-200': toast.type === 'error'
                    }"
                >
                    <div class="mt-0.5">
                        <svg v-if="toast.type === 'success'" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg v-else class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="text-xs font-mono block font-bold uppercase tracking-wider">
                            {{ toast.type === 'success' ? 'Success Log' : 'Validation Error' }}
                        </span>
                        <p class="text-xs mt-0.5 opacity-90 leading-relaxed">{{ toast.message }}</p>
                    </div>
                </div>
            </transition-group>
        </div>

        <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl z-20 transition-all duration-300">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-100 tracking-wide">
                    {{ task ? 'Edit Task' : 'Create New Task' }}
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    {{ task ? 'Update the details of the existing task.' : 'Fill in the details below to log a new task.' }}
                </p>
            </div>

            <form @submit.prevent="saveTask" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Task Title</label>
                    <input 
                        v-model="form.title"
                        type="text" 
                        placeholder="e.g., Fix authentication bug" 
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors"
                        required
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Description</label>
                    <textarea 
                        v-model="form.description"
                        rows="3" 
                        placeholder="Provide some details about the task..." 
                        class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-200 placeholder-slate-600 focus:outline-none focus:border-emerald-500 transition-colors resize-none"
                    ></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Priority</label>
                    <select v-model="form.priority" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Due Date</label>
                    <input v-model="form.due_date" type="date" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer [color-scheme:dark]" />
                </div>

                <div class="pt-2">
                    <button :disabled="form.processing" type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 disabled:bg-emerald-800 disabled:text-emerald-400 text-slate-950 font-semibold text-sm py-2.5 px-4 rounded-lg transition-colors shadow-[0_4px_12px_rgba(16,185,129,0.2)] flex justify-center items-center">
                        <span v-if="form.processing">Processing...</span>
                        <span v-else>Save Task</span>
                    </button>
                </div>
                <div v-if="form.hasErrors" class="rounded-md bg-red-50/50 border border-red-200/50 p-4">
                    <ul class="ml-4 list-disc">
                        <li v-for="error in form.errors" class="text-sm text-red-700">
                            {{ error }}
                        </li>
                    </ul>
                </div>
            </form>
        </div>

        <div class="w-full max-w-xl h-[535px] bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl z-20 flex flex-col justify-between">
            <div>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-100 tracking-wide">Recent Tasks</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Recent activity logs.</p>
                    </div>
                    <span class="text-xs px-2.5 py-1 bg-slate-950 border border-slate-800 rounded-full font-mono text-slate-400">
                        Total: {{ tasks?.total || 0 }}
                    </span>
                </div>

                <div class="space-y-2.5 overflow-y-auto max-h-[360px] pr-1 custom-scrollbar">
                    <template v-if="tasks && tasks.data && tasks.data.length > 0">
                        <div 
                            v-for="item in tasks.data" 
                            :key="item.id" 
                            class="bg-slate-950/60 border border-slate-800/80 rounded-xl p-3 flex items-start justify-between gap-4 hover:border-slate-700 transition-colors"
                        >
                            <div class="flex flex-col gap-1">
                                <span class="text-sm font-semibold text-slate-200 tracking-wide line-clamp-1">{{ item.title }}</span>
                                <p class="text-xs text-slate-400 line-clamp-2 font-light leading-relaxed">{{ item.description || 'No description provided.' }}</p>
                                <span class="text-[10px] text-slate-500 font-mono mt-1">Due: {{ item.due_date }}</span>
                            </div>
                            
                            <span 
                                class="text-[10px] px-2 py-0.5 font-bold uppercase tracking-wider rounded-md font-mono"
                                :class="{
                                    'bg-blue-500/10 text-blue-400 border border-blue-500/20': item.priority === 'low',
                                    'bg-amber-500/10 text-amber-400 border border-amber-500/20': item.priority === 'medium',
                                    'bg-orange-500/10 text-orange-400 border border-orange-500/20': item.priority === 'high',
                                    'bg-rose-500/10 text-rose-400 border border-rose-500/20 animate-pulse': item.priority === 'urgent',
                                }"
                            >
                                {{ item.priority }}
                            </span>
                        </div>
                    </template>

                    <div v-else class="text-center py-12 border border-dashed border-slate-800 rounded-xl">
                        <p class="text-sm text-slate-500">No tasks discovered in database stack.</p>
                    </div>
                </div>
            </div>

            <div v-if="tasks && tasks.links && tasks.links.length > 3" class="flex items-center justify-center gap-1.5 pt-4 border-t border-slate-800/60">
                <Link
                    v-for="(link, index) in tasks.links"
                    :key="index"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="px-3 py-1.5 rounded-lg text-xs font-mono transition-all duration-200 border"
                    :class="{
                        'bg-emerald-500 text-slate-950 border-emerald-500 font-bold shadow-[0_0_10px_rgba(16,185,129,0.3)]': link.active,
                        'bg-slate-950 text-slate-400 border-slate-800 hover:text-slate-200 hover:border-slate-700': !link.active,
                        'opacity-40 pointer-events-none': !link.url
                    }"
                />
            </div>
        </div>

        <div class="absolute bottom-6 right-6 flex flex-col gap-3.5 items-end max-w-sm w-full select-none z-30">
            
            <div class="w-full flex flex-col gap-2 bg-slate-900/60 backdrop-blur-md border border-slate-800/80 rounded-xl p-3 shadow-xl">
                
                <div class="flex flex-col gap-1">
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider font-mono mb-0.5 block">Task Actions</span>
                    <div class="w-full overflow-hidden h-[76px] flex flex-col justify-end pointer-events-none">
                        <transition-group name="log-list" tag="div" class="flex flex-col gap-1 w-full justify-end">
                            <div v-for="log in taskLogs" :key="log.id" class="flex items-center justify-between bg-slate-950/80 border border-slate-800/40 rounded px-2.5 py-1.5 text-[10px] font-mono">
                                <span class="text-amber-400 font-bold">{{ log.method }} {{ log.endpoint }}</span>
                                <span :class="log.success ? 'text-emerald-400' : 'text-rose-500'">
                                    {{ log.success ? `${log.status} OK (${log.ms}ms)` : `${log.status} ERR (${log.ms}ms)` }}
                                </span>
                            </div>
                        </transition-group>
                        <div v-if="taskLogs.length === 0" class="text-[10px] font-mono text-slate-600 italic py-1 pl-1">No transaction logs recorded yet.</div>
                    </div>
                </div>

                <hr class="border-slate-800/60" />

                <div class="flex flex-col gap-1">
                    <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider font-mono mb-0.5 block">Heartbeat Echo Stream</span>
                    <div class="w-full overflow-hidden h-[76px] flex flex-col justify-end pointer-events-none">
                        <transition-group name="log-list" tag="div" class="flex flex-col gap-1 w-full justify-end">
                            <div v-for="log in heartbeatLogs" :key="log.id" class="flex items-center justify-between bg-slate-950/80 border border-slate-800/40 rounded px-2.5 py-1.5 text-[10px] font-mono">
                                <span class="text-slate-500">{{ log.method }} {{ log.endpoint }}</span>
                                <span :class="log.success ? 'text-emerald-400' : 'text-rose-500'">
                                    {{ log.success ? `${log.status} OK (${log.ms}ms)` : `${log.status} ERR (${log.ms}ms)` }}
                                </span>
                            </div>
                        </transition-group>
                    </div>
                </div>

            </div>

            <div class="w-full bg-slate-900/90 backdrop-blur-md border border-slate-800 rounded-xl p-4 shadow-2xl">
                <div class="flex items-center gap-4">
                    <div class="relative w-3.5 h-3.5 flex items-center justify-center">
                        <div class="absolute w-full h-full rounded-full opacity-75 animate-ping" :class="{'bg-amber-400': heartbeatStatus === 'loading', 'bg-emerald-400': heartbeatStatus === 'healthy', 'hidden': heartbeatStatus === 'error'}"></div>
                        <div class="w-2.5 h-2.5 rounded-full relative z-10" :class="{'bg-amber-400': heartbeatStatus === 'loading', 'bg-emerald-400': heartbeatStatus === 'healthy', 'bg-rose-500': heartbeatStatus === 'error'}"></div>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="text-sm font-semibold tracking-wide" :class="{'text-amber-400': heartbeatStatus === 'loading', 'text-emerald-400': heartbeatStatus === 'healthy', 'text-rose-500': heartbeatStatus === 'error'}">
                            {{ heartbeatStatus === 'healthy' ? 'System Operational' : heartbeatStatus === 'loading' ? 'Establishing Connection' : 'Connection Interrupted' }}
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Dual terminal loggers feeding normally</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import axios from "axios";

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

defineProps<{
    task?: any;
    tasks?: {
        current_page: number;
        data: Array<{
            id: number;
            title: string;
            description: string | null;
            priority: string;
            status: string;
            due_date: string;
        }>;
        links: PaginationLink[];
        total: number;
    } | null;
}>();

interface Toast { id: number; message: string; type: 'success' | 'error'; }
const toasts = ref<Toast[]>([]);

const addToast = (message: string, type: 'success' | 'error' = 'success') => {
    const id = Date.now();
    toasts.value.push({ id, message, type });
    setTimeout(() => {
        toasts.value = toasts.value.filter(t => t.id !== id);
    }, 8000);
};

interface LogItem { id: number; method: 'GET' | 'POST'; endpoint: string; status: number; ms: number; success: boolean; }
const taskLogs = ref<LogItem[]>([]);
const heartbeatLogs = ref<LogItem[]>([]);

const addTaskLog = (method: 'POST', endpoint: string, status: number, ms: number, success: boolean) => {
    taskLogs.value.push({ id: Date.now() + Math.random(), method, endpoint, status, ms, success });
    if (taskLogs.value.length > 3) taskLogs.value.shift();
};

const addHeartbeatLog = (method: 'GET', endpoint: string, status: number, ms: number, success: boolean) => {
    heartbeatLogs.value.push({ id: Date.now() + Math.random(), method, endpoint, status, ms, success });
    if (heartbeatLogs.value.length > 3) heartbeatLogs.value.shift();
};

const form = useForm({
    title: "",
    description: "",
    priority: "medium",
    due_date: "",
});

const saveTask = () => {
    const startTime = performance.now();
    
    form.post("/tasks", {
        onSuccess: () => {
            const duration = Math.round(performance.now() - startTime);
            console.log("Success block running: Request completed successfully.");
            
            addTaskLog('POST', '/tasks', 200, duration, true);

            if (form.hasErrors) {
                addToast("Task saved, but please check the remaining errors below.", "error");
            } else {
                addToast("Task created successfully.", "success");
            }
            form.reset();
        },
        onError: (errors) => {
            const duration = Math.round(performance.now() - startTime);
            console.log("Error block running: Validation failed.", errors);
            
            addTaskLog('POST', '/tasks', 422, duration, false);
        }
    });
};

const heartbeatStatus = ref<'loading' | 'healthy' | 'error'>('loading');
const heartbeatIntervalId = ref<ReturnType<typeof setInterval> | null>(null);
const initialTimeoutId = ref<ReturnType<typeof setTimeout> | null>(null);

const heartbeatClient = axios.create({
    baseURL: '/',
    headers: {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    }
});

const heartBeat = async () => {
    const startTime = performance.now();
    const useFetch = false; 

    try { 
        let responseStatus: number;
        let responseData: any;

        if (useFetch) {
            const res = await fetch("/heartbeat");
            responseStatus = res.status;
            
            if (res.ok) {
                responseData = await res.json();
            } else {
                throw { response: { status: res.status } };
            }
        } else {
            const res = await heartbeatClient.get("heartbeat");
            responseStatus = res.status;
            responseData = res.data;
        }
        
        console.log("response data", responseData);
        const duration = Math.round(performance.now() - startTime);

        if (responseStatus === 200 && responseData?.status === 'ok') {
            heartbeatStatus.value = 'healthy';
            addHeartbeatLog('GET', '/heartbeat', 200, duration, true);
        } else {
            heartbeatStatus.value = 'error';
            addHeartbeatLog('GET', '/heartbeat', responseStatus, duration, false);
            if (heartbeatIntervalId.value) clearInterval(heartbeatIntervalId.value);
        }

    } catch (error: any) {
        heartbeatStatus.value = 'error';
        const status = error.response?.status || 500; 
        addHeartbeatLog('GET', '/heartbeat', status, Math.round(performance.now() - startTime), false);
        if (heartbeatIntervalId.value) clearInterval(heartbeatIntervalId.value);
    }
};

onMounted(() => {
    initialTimeoutId.value = setTimeout(() => {
        heartBeat();
        heartbeatIntervalId.value = setInterval(heartBeat, 300);
    }, 3000); 
});

onBeforeUnmount(() => {
    if (heartbeatIntervalId.value) clearInterval(heartbeatIntervalId.value);
    if (initialTimeoutId.value) clearTimeout(initialTimeoutId.value);
});
</script>
